<?
IncludeModuleLangFile(__FILE__);

class VPostingTwitter
{
	/**
	* Публикует запись в Twitter
	* 
	* @param array $arFields поля публикуемого элемента
	* @param array $arAcconts в какие аккаунты публиковать
	* @param array $arPost данные о публикации
	* @param array $arSite массив полей сайта
	*/
	function post($arFields, $arAccounts, $arPost, $arSite)
	{
		global $APPLICATION;

		if(VOptions::get('is_twitter_enable', false, VettichPostingFunc::module_id()) != 'Y')
			return;
		
		$post_data = array();
		foreach($arAccounts as $acc_id)
		{
			if(intval(CVDB::get('twitter_accounts')) >= intval($acc_id))
			{
				$post_data = array();
				$arAccount = VettichPosting::paramValues('twitter_accounts', $acc_id);

				if($arAccount['is_enable'] != 'Y')
					continue;

				$twit = new Abraham\TwitterOAuth\TwitterOAuth($arAccount['api_key'], $arAccount['api_secret'], $arAccount['access_token'], $arAccount['access_token_secret']);
				$rs = $twit->get('account/verify_credentials');
				if(!isset($rs->errors))
				{
					$message_sep = true;
					if($arPost['twitter_message_sep'] != 'Y')
						$message_sep = false;

					$post_data['status'] = VettichPosting::replaceMacros($arPost['twitter_message'], $arFields, $arSite, $arPost);
					$post_data['status'] = strip_tags($post_data['status']);
					$post_data['status'] = $APPLICATION->ConvertCharset($post_data['status'], SITE_CHARSET, "UTF-8");
					$post_data['status'] = trim(html_entity_decode($post_data['status']));

					$media_ids = array();
					if($arPost['twitter_photo'] != '' && $arPost['twitter_photo'] != 'none')
					{
						if($res = self::get_media_ids($arPost['twitter_photo'], $arFields, $twit))
							$media_ids[] = $res;
					}

					if($arPost['twitter_photos'] != $arPost['twitter_photo'] && $arPost['twitter_photos'] != '' && $arPost['twitter_photos'] != 'none')
					{
						if($res = self::get_media_ids($arPost['twitter_photos'], $arFields, $twit))
							$media_ids[] = $res;
					}

					$status_fsize = 140;
					if(!empty($media_ids))
					{
						$post_data['media_ids'] = implode(',', $media_ids);
						$status_fsize -= 24;
					}
					$post_data['status'] = VettichPostingFunc::substr($post_data['status'], 0, $status_fsize, 'UTF-8', $message_sep, '...');
					if($arPost['twitter_link'] != '' && $arPost['twitter_link'] != 'none')
					{
						$link = self::attach_link($arPost['twitter_link'], $arFields, $arPost, $arSite);
						if(!empty($link))
						{
							$status_fsize -= 24;
							$post_data['status'] = VettichPostingFunc::substr($post_data['status'], 0, $status_fsize, 'UTF-8', $message_sep, '...');
							$post_data['status'] .= "\n".$link;
						}
					}

					$rs = $twit->post('statuses/update', $post_data);
					$debug[] = $post_data;
					$debug[] = $rs;
				}
				VOptions::debugg($debug, 'twit');

				if(isset($rs->errors))
				{
					if(VOptions::get('twitter_log_error', false, VettichPostingFunc::module_id()) == 'Y')
					{
						$text = GetMessage('TWITTER_ERROR',
							array(
								'#CODE#' => $rs->errors[0]->code,
								'#MESSAGE#'=>$rs->errors[0]->message,
								'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
								'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
								'#ID#' => $arFields['ID'],
								'#ACC_NAME#' => VettichPosting::GetUrlPostAcc('twitter', $acc_id, $arAccount['name']),
							)
						);
						VettichPostingLogs::addLog('twitter', $text, 'Error');
					}
				}
				else
				{
					if(VOptions::get('twitter_log_success', false, VettichPostingFunc::module_id()) == 'Y')
					{
						$text = GetMessage('TWITTER_SUCCESS',
							array(
								'#URL#' => self::getUrlPost($arAccount, $rs),
								'#ACC_NAME#' => VettichPosting::GetUrlPostAcc('twitter', $acc_id, $arAccount['name']),
							)
						);
						VettichPostingLogs::addLog('twitter', $text, 'Success');
					}
				}
			}
		}
	}

	function getUrlPost($arAccount, $response)
	{
		if(isset($response->user->id))
			return 'http://twitter.com/'.$response->user->id.'/status/'.$response->id;
		return '';
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

	function get_media_ids($sProp, $arFields, &$twit)
	{
		$result = array();

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
			foreach($files as $file_name)
			{
				if($media_id = self::get_media_id($file_name, $twit))
					$result[] = $media_id;
			}
		}
		else
		{
			$file_name = CFile::GetPath($arFields[$sProp]);
			if($file_name != '')
				if($media_id = self::get_media_id($_SERVER['DOCUMENT_ROOT'].$file_name, $twit))
					$result[] = $media_id;
		}

		return implode(',', $result);
	}

	function get_media_id($file_name, &$twit)
	{
		$res = $twit->upload('media/upload', array('media'=>$file_name));
		if(!isset($res->error))
			return $res->media_id_string;

		return false;
	}
}