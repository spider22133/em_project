<?
IncludeModuleLangFile(__FILE__);

class VPostingVk
{
	static private $app_id = '5139034';

	function method($method, $data)
	{
		if($method == '')
			return false;

		$url = 'https://api.vk.com/method/'.$method;
		$return = json_decode(VettichPostingFunc::_curl_post($url, $data, false), 1);
		return $return;
	}

	/**
	* Публикует запись в ВКонтакте
	* 
	* @param array $arFields поля публикуемого элемента
	* @param array $arAcconts в какие аккаунты публиковать
	* @param array $arPost данные о публикации
	* @param array $arSite массив полей сайта
	*/
	function post($arFields, $arAccounts, $arPost, $arSite)
	{
		global $APPLICATION;


		if(VOptions::get("is_vk_enable", false, VettichPostingFunc::module_id()) != 'Y')
			return;

		$method = 'wall.post';
		$post_data = array();
		foreach($arAccounts as $acc_id)
		{
			if(intval(CVDB::get("vk_accounts")) >= intval($acc_id))
			{
				$post_data = array();
				$arAccount = VettichPosting::paramValues('vk_accounts', $acc_id);
				VOptions::debugg($arAccount, 'vk');

				if($arAccount['is_enable'] != 'Y')
					continue;

				$post_data['access_token'] = $arAccount['access_token'];

				if($arPost['vk_publish_date'] != '' && $arPost['vk_publish_date'] != 'none')
				{
					$time = strtotime($arFields[$arPost['vk_publish_date']]);
					if($time > time())
						$post_data['publish_date'] = $time;
				}

				if($arAccount['is_group_publish'] == 'Y')
					$post_data['from_group'] = 1;
				else
					$post_data['from_group'] = 0;

				$post_data['owner_id'] = ($arAccount['group_publish']=='Y'?'-':'').$arAccount['group_publish_id'];

				$post_data['message'] = VettichPosting::replaceMacros($arPost['vk_message'], $arFields, $arSite, $arPost);
				$post_data['message'] = strip_tags($post_data['message']);
				$post_data['message'] = $APPLICATION->ConvertCharset($post_data['message'], SITE_CHARSET, "UTF-8");
				$post_data['message'] = trim(html_entity_decode($post_data['message']));
				if(empty($post_data['message']))
					unset($post_data['message']);

				if($arPost['vk_photo'] != '' && $arPost['vk_photo'] != 'none')
				{
					$res = self::attach_photo($arPost['vk_photo'], $arFields, $arAccount, $arPost);
					if(empty($post_data['attachments']))
					{
						$post_data['attachments'] = $res;
					}
					else
					{
						$post_data['attachments'] .= ','.$res;
					}
				}

				if($arPost['vk_photos'] != $arPost['vk_photo'] && $arPost['vk_photos'] != '' && $arPost['vk_photos'] != 'none')
				{
					$res = self::attach_photo($arPost['vk_photos'], $arFields, $arAccount, $arPost);
					if(empty($post_data['attachments']))
					{
						$post_data['attachments'] = $res;
					}
					else
					{
						$post_data['attachments'] .= ','.$res;
					}
				}

				if($arPost['vk_link'] != '' && $arPost['vk_link'] != 'none')
				{
					$res = self::attach_link($arPost['vk_link'], $arFields, $arPost, $arSite);
					if(empty($post_data['attachments']))
					{
						$post_data['attachments'] = $res;
					}
					else
					{
						$post_data['attachments'] .= ','.$res;
					}
				}

				$rs = self::method($method, $post_data);
				if(isset($rs['error']))
				{
					if(VOptions::get('vk_log_error', false, VettichPostingFunc::module_id()) == 'Y')
					{
						$text = GetMessage('VK_ERROR',
							array(
								'#CODE#' => $rs['error']['error_code'],
								'#MESSAGE#'=>$rs['error']['error_msg'],
								'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
								'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
								'#ID#' => $arFields['ID'],
								'#ACC_NAME#' => VettichPosting::GetUrlPostAcc('vk', $acc_id, $arAccount['name']),
							)
						);
						VettichPostingLogs::addLog('vk', $text, 'Error');
					}
				}
				else
				{
					if(VOptions::get('vk_log_success', false, VettichPostingFunc::module_id()) == 'Y')
					{
						$text = GetMessage('VK_SUCCESS',
							array(
								'#URL#' => self::getUrlPost($arAccount, $rs),
								'#ACC_NAME#' => VettichPosting::GetUrlPostAcc('vk', $acc_id, $arAccount['name']),
							)
						);
						VettichPostingLogs::addLog('vk', $text, 'Success');
					}
				}
			}
		}
	}

	function getUrlPost($arAccount, $response)
	{
		if($arAccount['group_publish']=='Y')
			return 'https://vk.com/public'.$arAccount['group_publish_id'].'?w=wall-'.$arAccount['group_publish_id'].'_'.$response['response']['post_id'];
		else
			return 'https://vk.com/id'.$arAccount['group_publish_id'].'?w=wall'.$arAccount['group_publish_id'].'_'.$response['response']['post_id'];
	}

	function attach_photo($sProp, $arFields, $arAccount, $arPost)
	{
		$result = '';
		if(strpos($sProp, 'PROPERTY_') === 0)
		{
			$files = array();
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
			if(!empty($files))
			{
				$img = self::upload_files($files, $arAccount);
			}
			if(isset($img['response']) && !empty($img['response']))
			{
				foreach($img['response'] as $value)
				{
					if($result != '')
					{
						$result .= ','.$value['id'];
					}
					else
					{
						$result = $value['id'];
					}
				}
			}
		}
		else
		{
			$img_path = CFile::GetPath($arFields[$sProp]);
			if($img_path != '')
			{
				$img = self::upload_files(array($_SERVER['DOCUMENT_ROOT'].$img_path), $arAccount);
				if(isset($img['response']) && isset($img['response'][0]))
				{
					if($result != '')
					{
						$result .= ','.$img['response'][0]['id'];
					}
					else
					{
						$result = $img['response'][0]['id'];
					}
				}
			}
		}
		return $result;
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

	function upload_files($arFilesName, $arAccount, $sFileType='photo')
	{
		if($sFileType == 'photo')
		{
			if(count($arFilesName) > 5)
			{
				$arFilesName = array_chunk($arFilesName, 5);
				$ret = array();
				foreach($arFilesName as $arr)
				{
					$ret = array_merge_recursive($ret, self::upload_files($arr, $arAccount, $sFileType));
				}
				return $ret;
			}

			$files = array();
			foreach($arFilesName as $key => $fileName)
			{
				if(count($files) >= 10)
					break;
				if (version_compare(PHP_VERSION, '5.6.0', '<'))
				{
					$files['file'.(count($files)+1)] = '@'.$fileName;
				}
				else
				{
					$files['file'.(count($files)+1)] = new \CURLFile($fileName);
				}
			}

			$params = array('access_token' => $arAccount['access_token']);
			if($arAccount['group_publish'] == 'Y')
				$params['group_id'] = $arAccount['group_publish_id'];
			else
				$params['user_id'] = $arAccount['group_publish_id'];
			$dataArray=self::method('photos.getWallUploadServer', $params);

			$response = json_decode(VettichPostingFunc::_curl_post($dataArray['response']['upload_url'], $files, false), 1);
			$response_photo = json_decode($response['photo'],1);
			if(empty($response) or empty($response_photo))
				return false;

			$data = array(
				'group_id' => $arAccount['group_publish_id'],
				'photo' => $response['photo'],
				'server' => $response['server'],
				'hash' => $response['hash'],
				'access_token' => $arAccount['access_token'],
			);
			return self::method('photos.saveWallPhoto', $data);
		}
		elseif($sFileType == 'video')
		{
			// todo
			// разобраться с отправкой видео, и реализовать...в будущем
			return false;

			$data = array(
				'name' => $arFilesName['title'],
				'link' => $arFilesName['path'],
				'access_token' => $arAccount['access_token'],
			);
			return self::method('video.save', $data);
		}
	}
}