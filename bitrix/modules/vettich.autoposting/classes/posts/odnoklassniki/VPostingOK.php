<?
IncludeModuleLangFile(__FILE__);

if(!defined('IS_ODNOKLASSNIKI_SDK_LOAD'))
{
	define('IS_ODNOKLASSNIKI_SDK_LOAD', true);
	require_once __DIR__.'/../../Odnoklassniki/odnoklassniki_sdk.php';
}

class VPostingOK
{
	/**
	* Публикует запись в Одноклассники
	* 
	* @param array $arFields поля публикуемого элемента
	* @param array $arAcconts в какие аккаунты публиковать
	* @param array $arPost данные о публикации
	* @param array $arSite массив полей сайта
	*/
	function post($arFields, $arAccounts, $arPost, $arSite)
	{
		global $APPLICATION;

		if(VOptions::get('is_odnoklassniki_enable', false, VettichPostingFunc::module_id()) != 'Y')
			return;
		
		foreach($arAccounts as $acc_id)
		{
			if(intval(CVDB::get('odnoklassniki_accounts')) >= intval($acc_id))
			{
				$post_data = array();
				$arAccount = VettichPosting::paramValues('odnoklassniki_accounts', $acc_id);

				if($arAccount['is_enable'] != 'Y')
					continue;

				OdnoklassnikiSDK::init(
					$arAccount['api_id'],
					$arAccount['api_public_key'],
					$arAccount['api_secret_key'],
					$arAccount['access_token']
				);

				$attach = (object)array('media'=>array());
				$mess = VettichPosting::replaceMacros($arPost['odnoklassniki_message'], $arFields, $arSite, $arPost);
				$mess = strip_tags($mess);
				$mess = trim(html_entity_decode($mess));
				$attach->media[] = (object)array(
					'type'=>'text',
					'text'=>$APPLICATION->ConvertCharset($mess, SITE_CHARSET, "UTF-8")
				);

				if($arPost['odnoklassniki_publish_date'] != '' && $arPost['odnoklassniki_publish_date'] != 'none')
				{
					$time = strtotime($arFields[$arPost['odnoklassniki_publish_date']]);
					if($time > time())
						$attach->publishAt = date('Y-m-d h:i:s', $time);
				}

				if($arAccount['is_group_publish'] == 'Y')
					$attach->onBehalfOfGroup = true;
				else
					$attach->onBehalfOfGroup = false;

				$photo_list = array();
				if($arPost['odnoklassniki_photo'] != '' && $arPost['odnoklassniki_photo'] != 'none')
				{
					$photo_list = array_merge($photo_list, self::upload_photos($arPost['odnoklassniki_photo'], $arFields, $arAccount));
				}
				if($arPost['odnoklassniki_photo_other'] != '' && $arPost['odnoklassniki_photo_other'] != 'none')
				{
					$photo_list = array_merge($photo_list, self::upload_photos($arPost['odnoklassniki_photo_other'], $arFields, $arAccount));
				}

				if(!empty($photo_list))
				{
					$attach->media[] = (object)array(
						'type'=>'photo',
						'list'=>$photo_list,
					);
				}

				$attach_link_key = -1;
				if($arPost['odnoklassniki_link'] != '' && $arPost['odnoklassniki_link'] != 'none')
				{
					$link = self::attach_link($arPost['odnoklassniki_link'], $arFields, $arPost, $arSite);
					if(!empty($link))
					{
						$attach->media[] = (object)array(
							'type'=>'link',
							'url'=>&$link
						);
						$attach_link_key = count($attach->media) - 1;
					}
				}

				$params = array(
					'gid'=>$arAccount['group_id'],
					'type'=>'GROUP_THEME',
					'attachment'=>json_encode($attach)
				);
				$rs = OdnoklassnikiSDK::makeRequest('mediatopic.post', $params);

				if(isset($rs['error_code']) && $rs['error_code'] == 5000)
				{
					$slink = $link;
					if(strpos($link, 'https')===0)
						$link = 'http'.substr($link, strlen('https'));
					else
						$link = 'https'.substr($link, strlen('http'));
					$params = array(
						'gid'=>$arAccount['group_id'],
						'type'=>'GROUP_THEME',
						'attachment'=>json_encode($attach)
					);
					$rs = OdnoklassnikiSDK::makeRequest('mediatopic.post', $params);

					if(isset($rs['error_code']) && $rs['error_code'] == 5000 && $attach_link_key >= 0)
					{
						$link_err = $rs;
						unset($attach->media[$attach_link_key]);
						$params = array(
							'gid'=>$arAccount['group_id'],
							'type'=>'GROUP_THEME',
							'attachment'=>json_encode($attach)
						);
						$rs = OdnoklassnikiSDK::makeRequest('mediatopic.post', $params);
					}
				}

				if(isset($rs['error_code']) or empty($rs))
				{
					if(CVDB::get('odnoklassniki_log_error', false) != 'Y')
					{
						if(empty($rs))
						{
							$text = GetMessage('ODNOKLASSNIKI_ERROR_UNKNOWN',
								array(
									'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
									'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
									'#ID#' => $arFields['ID'],
									'#ACC_NAME#' => $arAccount['name'],
								)
							);
						}
						else
						{
							$text = GetMessage('ODNOKLASSNIKI_ERROR',
								array(
									'#CODE#' => $rs['error_code'],
									'#MESSAGE#'=>$rs['error_msg'],
									'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
									'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
									'#ID#' => $arFields['ID'],
									'#ACC_NAME#' => $arAccount['name'],
								)
							);
						}
						VettichPostingLogs::addLog('odnoklassniki', $text, 'Error');
					}
				}
				elseif(isset($link_err['error_code']))
				{
					if(CVDB::get('odnoklassniki_log_error', false) != 'Y')
					{
						$text = GetMessage('ODNOKLASSNIKI_WARNING_URL',
							array(
								'#ERR_CODE#' => $link_err['error_code'],
								'#ERR_MESSAGE#'=>$link_err['error_msg'],
								'#URL_ATTACH#'=> $slink ?: $link,
								'#URL#' => self::getUrlPost($arAccount, $rs),
								'#ACC_NAME#' => VettichPosting::GetUrlPostAcc('odnoklassniki', $acc_id, $arAccount['name']),
							)
						);
						VettichPostingLogs::addLog('odnoklassniki', $text, 'Warning');
					}
				}
				else
				{
					if(CVDB::get('odnoklassniki_log_success', false) != 'Y')
					{
						$text = GetMessage('ODNOKLASSNIKI_SUCCESS',
							array(
								'#URL#' => self::getUrlPost($arAccount, $rs),
								'#ACC_NAME#' => VettichPosting::GetUrlPostAcc('odnoklassniki', $acc_id, $arAccount['name']),
							)
						);
						VettichPostingLogs::addLog('odnoklassniki', $text, 'Success');
					}
				}
			}
		}
	}

	function getUrlPost($arAccount, $response)
	{
		return 'http://ok.ru/group/'.$arAccount['group_id'].'/topic/'.$response;
	}

	function attach_link($sProp, $arFields, $arPost, $arSite)
	{
		$result = '';
		if(strpos($sProp, 'PROPERTY_') === 0)
		{
			$prop_code = substr($sProp, strlen('PROPERTY_'));
			$arProp = $arFields['PROPERTIES'][$prop_code];
			if($arProp && $arProp['PROPERTY_TYPE'] == 'S' && $arProp['USER_TYPE'] == '')
			{
				if($arProp['MULTIPLE'] == 'Y')
				{
					foreach($arProp['VALUES'] as $value)
					{
						$result = self::get_link($value, $arSite, $arPost);
						break;
					}
				}
				else
				{
					$result = self::get_link($arProp['VALUE'], $arSite, $arPost);
				}
			}
		}
		else
		{
			if($sProp == 'DETAIL_PAGE_URL'
				or $sProp == 'LIST_PAGE_URL')
			{
				$result = self::get_link($arFields[$sProp],  $arSite, $arPost);
			}
		}
		return $result;
	}

	function get_link($link, $arSite, $arPost)
	{
		$link = trim($link);
		if(empty($link))
			return '';

		if(strpos($link, 'http') === 0)
		{
			return $link;
		}

		$result = VettichPosting::getServerURL($arSite, $arPost);
		if(strpos($link, '/') === 0)
		{
			return $result.substr($link, 1);
		}
		return $result.$link;
	}

	function upload_photos($sProp, $arFields, $arAccount)
	{
		$result = array();
		$files = array();
		if(strpos($sProp, 'PROPERTY_') === 0)
		{
			$prop_code = substr($sProp, strlen('PROPERTY_'));
			$arProp = $arFields['PROPERTIES'][$prop_code];
			if($arProp && $arProp['PROPERTY_TYPE'] == 'F')
			{
				if($arProp['MULTIPLE'] == 'Y')
				{
					foreach($arProp['VALUES'] as $k=>$arValue)
					{
						$files[] = $_SERVER['DOCUMENT_ROOT'].CFile::GetPath($arValue);
					}
				}
				else
				{
					$files[] = $_SERVER['DOCUMENT_ROOT'].CFile::GetPath($arProp['VALUE']);
				}
			}
		}
		else
		{
			$file_name = CFile::GetPath($arFields[$sProp]);
			if($file_name != '')
				$files[] = $_SERVER['DOCUMENT_ROOT'].$file_name;
		}

		if(empty($files))
			return array();

		$params = array(
			'gid' => $arAccount['group_id'],
			'count' => count($files),
		);
		$rs = OdnoklassnikiSDK::makeRequest('photosV2.getUploadUrl', $params);
		if(isset($rs['error_code']))
			return array();

		$upload_files = array();
		foreach($files as $k=>$file_name)
		{
			$upload_files['pic'.$k] = VettichPosting::getCurlFilename($file_name);
		}
		$rs = json_decode(VettichPostingFunc::_curl_post($rs['upload_url'], $upload_files));
		
		$list = array();
		if(isset($rs->photos))
			foreach($rs->photos as $k=>$val)
				$list[] = (object)array('id'=>$val->token);

		return $list;
	}

	function get_media_id($file_name, &$twit)
	{
		$res = $twit->upload('media/upload', array('media'=>$file_name));
		if(!isset($res->error))
			return $res->media_id_string;

		return false;
	}
}