<?
namespace Vettich\Autoposting\Posts\odnoklassniki;
use Vettich\Autoposting\PostingFunc;
use Vettich\Autoposting\PostingLogs;

IncludeModuleLangFile(__FILE__);

if(!defined('IS_ODNOKLASSNIKI_SDK_LOAD'))
{
	define('IS_ODNOKLASSNIKI_SDK_LOAD', true);
	require_once VETTICH_AUTOPOSTING_DIR.'/classes/Odnoklassniki/odnoklassniki_sdk.php';
}

class Posting extends \Vettich\Autoposting\Posting
{
	/**
	* Публикует запись в Одноклассники
	* 
	* @param array $arFields поля публикуемого элемента
	* @param array $arAcconts в какие аккаунты публиковать
	* @param array $arPost данные о публикации
	* @param array $arSite массив полей сайта
	*/
	function post($arFields, $arAccounts, $arPost, $arSite, $arOptionally=array())
	{
		global $APPLICATION;
		$type = $arOptionally['type'];

		if(\COption::GetOptionString(PostingFunc::module_id(), 'is_odnoklassniki_enable', false) != 'Y')
			return;

		$arResult = array();
		foreach($arAccounts as $acc_id)
		{
			$post_id = $arOptionally['post_ids'][$acc_id];
			$arAccount = Func::GetAccountValues($acc_id, $arPost['ID']);
			if(empty($arAccount))
				continue;

			$arPost['arAccount'] = $arAccount;
			$arPost['ACCPREFIX'] = Func::ACCPREFIX;

			$post_data = array();

			if($arAccount['IS_ENABLE'] != 'Y')
				continue;

			\OdnoklassnikiSDK::init(
				$arAccount['API_ID'],
				$arAccount['API_PUBLIC_KEY'],
				$arAccount['API_SECRET_KEY'],
				$arAccount['ACCESS_TOKEN']
			);

			if(($type == 'delete' or $type == 'edit')
				&& $arAccount['ODNOKLASSNIKI_PUBLICATION_MODE'] == 'none')
				continue;

			if(empty($arAccount['ODNOKLASSNIKI_PUBLICATION_MODE']))
				$arAccount['ODNOKLASSNIKI_PUBLICATION_MODE'] = 'update';

			if($type == 'delete'
				or ($type == 'edit'
					&& $arAccount['ODNOKLASSNIKI_PUBLICATION_MODE'] == 'del_add'))
			{
				if(!empty($post_id))
				{
				}
			}

			$attach = (object)array('media'=>array());
			$mess = parent::replaceMacros($arAccount['ODNOKLASSNIKI_MESSAGE'], $arFields, $arSite, $arPost);
			$mess = strip_tags($mess);
			$mess = trim(html_entity_decode($mess));
			$attach->media[] = (object)array(
				'type'=>'text',
				'text'=>$APPLICATION->ConvertCharset($mess, SITE_CHARSET, "UTF-8")
			);

			if($arAccount['ODNOKLASSNIKI_PUBLISH_DATE'] != '' && $arAccount['ODNOKLASSNIKI_PUBLISH_DATE'] != 'none')
			{
				$time = strtotime($arFields[$arAccount['ODNOKLASSNIKI_PUBLISH_DATE']]);
				if($time > time())
					$attach->publishAt = date('Y-m-d h:i:s', $time);
			}

			if($arAccount['IS_GROUP_PUBLISH'] == 'Y')
				$attach->onBehalfOfGroup = true;
			else
				$attach->onBehalfOfGroup = false;

			$photo_list = array();
			if($arAccount['ODNOKLASSNIKI_PHOTO'] != '' && $arAccount['ODNOKLASSNIKI_PHOTO'] != 'none')
			{
				$photo_list = array_merge($photo_list, self::upload_photos($arAccount['ODNOKLASSNIKI_PHOTO'], $arFields, $arAccount));
			}
			if($arAccount['ODNOKLASSNIKI_PHOTO_OTHER'] != '' && $arAccount['ODNOKLASSNIKI_PHOTO_OTHER'] != 'none')
			{
				$photo_list = array_merge($photo_list, self::upload_photos($arAccount['ODNOKLASSNIKI_PHOTO_OTHER'], $arFields, $arAccount));
			}

			if(!empty($photo_list))
			{
				$attach->media[] = (object)array(
					'type'=>'photo',
					'list'=>$photo_list,
				);
			}

			$attach_link_key = -1;
			if($arAccount['ODNOKLASSNIKI_LINK'] != '' && $arAccount['ODNOKLASSNIKI_LINK'] != 'none')
			{
				$link = self::getLinkFromProperty($arAccount['ODNOKLASSNIKI_LINK'], $arFields, $arPost, $arSite);
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
				'gid'=>$arAccount['GROUP_ID'],
				'type'=>'GROUP_THEME',
				'attachment'=>json_encode($attach)
			);
			$rs = \OdnoklassnikiSDK::makeRequest('mediatopic.post', $params);

			if(isset($rs['error_code']) && $rs['error_code'] == 5000)
			{
				$slink = $link;
				if(strpos($link, 'https')===0)
					$link = 'http'.substr($link, strlen('https'));
				else
					$link = 'https'.substr($link, strlen('http'));
				$params = array(
					'gid'=>$arAccount['GROUP_ID'],
					'type'=>'GROUP_THEME',
					'attachment'=>json_encode($attach)
				);
				$rs = \OdnoklassnikiSDK::makeRequest('mediatopic.post', $params);

				if(isset($rs['error_code']) && $rs['error_code'] == 5000 && $attach_link_key >= 0)
				{
					$link_err = $rs;
					unset($attach->media[$attach_link_key]);
					$params = array(
						'gid'=>$arAccount['GROUP_ID'],
						'type'=>'GROUP_THEME',
						'attachment'=>json_encode($attach)
					);
					$rs = \OdnoklassnikiSDK::makeRequest('mediatopic.post', $params);
				}
			}

			if(isset($rs['error_code']) or empty($rs))
			{
				if(\COption::GetOptionString(PostingFunc::module_id(), 'odnoklassniki_log_error', false) == 'Y')
				{
					if(empty($rs))
					{
						$text = GetMessage('ODNOKLASSNIKI_ERROR_UNKNOWN',
							array(
								'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
								'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
								'#ID#' => $arFields['ID'],
								'#ACC_NAME#' => $arAccount['NAME'],
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
								'#ACC_NAME#' => $arAccount['NAME'],
							)
						);
					}
					PostingLogs::addLog('odnoklassniki', $text, 'Error');
				}
			}
			elseif(isset($link_err['error_code']))
			{
				$arResult[$acc_id] = $rs;
				if(\COption::GetOptionString(PostingFunc::module_id(), 'odnoklassniki_log_error', false) == 'Y')
				{
					$text = GetMessage('ODNOKLASSNIKI_WARNING_URL',
						array(
							'#ERR_CODE#' => $link_err['error_code'],
							'#ERR_MESSAGE#'=>$link_err['error_msg'],
							'#URL_ATTACH#'=> $slink ?: $link,
							'#URL#' => self::getUrlPost($arAccount, $rs),
							'#ACC_NAME#' => parent::GetUrlPostAcc('odnoklassniki', $acc_id, $arAccount['NAME']),
						)
					);
					PostingLogs::addLog('odnoklassniki', $text, 'Warning');
				}
			}
			else
			{
				$arResult[$acc_id] = $rs;
				if(\COption::GetOptionString(PostingFunc::module_id(), 'odnoklassniki_log_success', false) == 'Y')
				{
					$text = GetMessage('ODNOKLASSNIKI_SUCCESS',
						array(
							'#URL#' => self::getUrlPost($arAccount, $rs),
							'#ACC_NAME#' => parent::GetUrlPostAcc('odnoklassniki', $acc_id, $arAccount['NAME']),
						)
					);
					PostingLogs::addLog('odnoklassniki', $text, 'Success');
				}
			}
		}
		return $arResult;
	}

	function getUrlPost($arAccount, $post_id)
	{
		return 'http://ok.ru/group/'.$arAccount['GROUP_ID'].'/topic/'.$post_id;
	}

	function upload_photos($sProp, $arFields, $arAccount)
	{
		$result = array();
		$files = parent::getFilesFromProperty($sProp, $arFields);
		if(empty($files))
			return array();

		$params = array(
			'gid' => $arAccount['GROUP_ID'],
			'count' => count($files),
		);
		$rs = \OdnoklassnikiSDK::makeRequest('photosV2.getUploadUrl', $params);
		if(isset($rs['error_code']))
			return array();

		$upload_files = array();
		foreach($files as $k=>$file_name)
		{
			$upload_files['pic'.$k] = parent::getCurlFilename($file_name);
		}
		$rs = json_decode(PostingFunc::_curl_post($rs['upload_url'], $upload_files));
		
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