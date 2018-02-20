<?
IncludeModuleLangFile(__FILE__);

class VPostingFB
{
	/**
	* Публикует запись в Facebook
	* 
	* @param array $arFields поля публикуемого элемента
	* @param array $arAcconts в какие аккаунты публиковать
	* @param array $arPost данные о публикации
	* @param array $arSite массив полей сайта
	*/
	function post($arFields, $arAccounts, $arPost, $arSite)
	{
		global $APPLICATION;
		
		if(VOptions::get("is_fb_enable", false, VettichPostingFunc::module_id()) != 'Y')
			return;
		
		foreach($arAccounts as $acc_id)
		{
			if(intval(CVDB::get("fb_accounts")) >= intval($acc_id))
			{
				$arAccount = VettichPosting::paramValues('fb_accounts', $acc_id);
				if($arAccount['is_enable'] != 'Y')
					continue;

				$fb = new Facebook\Facebook(array(
					'app_id'				=> $arAccount['app_id'],
					'app_secret'			=> $arAccount['app_secret'],
					'default_graph_version'	=> 'v2.5',
					'default_access_token'	=> $arAccount['access_token'],
				));
				try{
					$result = $fb->get('/'. $arAccount['group_id'] .'?fields=access_token')->getGraphObject()->asArray();
					if(isset($result['access_token']))
						$fb->setDefaultAccessToken($result['access_token']);
				}
				catch (Facebook\Exceptions\FacebookResponseException $e){
					// continue;
				} catch (Facebook\Exceptions\FacebookSDKException $e) {
					// continue;
				}

				$post_data = array();

				if($arPost['fb_publish_date'] != '' && $arPost['fb_publish_date'] != 'none')
				{
					$time = strtotime($arFields[$arPost['fb_publish_date']]);
					if($time > time())
					{
						$post_data['scheduled_publish_time'] = $time;
						$post_data['published'] = false;
					}
				}

				$post_data['message'] = VettichPosting::replaceMacros($arPost['fb_message'], $arFields, $arSite, $arPost);
				$post_data['message'] = strip_tags($post_data['message']);
				$post_data['message'] = $APPLICATION->ConvertCharset($post_data['message'], SITE_CHARSET, "UTF-8");
				$post_data['message'] = trim(html_entity_decode($post_data['message']));

				if(empty($post_data['message']))
					unset($post_data['message']);

				if($arPost['fb_link'] != '' && $arPost['fb_link'] != 'none')
				{
					$post_data['link'] = self::attach_link($arPost['fb_link'], $arFields, $arPost, $arSite);
				}

				if($arPost['fb_photo'] != '' && $arPost['fb_photo'] != 'none')
				{
					$res = self::attach_link($arPost['fb_photo'], $arFields, $arPost, $arSite);
					if($res)
						$post_data['picture'] = $res;
				}

				if($arPost['fb_name'] != '' && $arPost['fb_name'] != 'none' && !empty($post_data['link']))
				{
					$post_data['name'] = VettichPosting::getStringFromProperty($arPost['fb_name'], $arFields, $arSite, $arPost);
					$post_data['name'] = strip_tags($post_data['name']);
					$post_data['name'] = $APPLICATION->ConvertCharset($post_data['name'], SITE_CHARSET, "UTF-8");
					$post_data['name'] = html_entity_decode($post_data['name']);
				}

				if($arPost['fb_description'] != '' && $arPost['fb_description'] != 'none' && !empty($post_data['link']))
				{
					$post_data['description'] = VettichPosting::getStringFromProperty($arPost['fb_description'], $arFields, $arSite, $arPost);
					$post_data['description'] = strip_tags($post_data['description']);
					$post_data['description'] = $APPLICATION->ConvertCharset($post_data['description'], SITE_CHARSET, "UTF-8");
					$post_data['description'] = html_entity_decode($post_data['description']);
				}

				try {
					if(isset($post_data['picture']) && empty($post_data['link']))
					{
						$plink = true;
						unset($post_data['picture']);
					}
					$rs = $fb->post('/'.$arAccount['group_id'].'/feed', $post_data);
					if($plink)
					{
						$text = GetMessage('FB_WARNING_NOT_URL',
							array(
								'#URL#' => self::getUrlPost($arAccount, $rs),
								'#ACC_NAME#' => VettichPosting::GetUrlPostAcc('facebook', $acc_id, $arAccount['name']),
							)
						);
						VettichPostingLogs::addLog('facebook', $text, 'Warning');
					}
					elseif(VOptions::get('fb_log_success', false, VettichPostingFunc::module_id()) == 'Y')
					{
						$text = GetMessage('FB_SUCCESS',
							array(
								'#URL#' => self::getUrlPost($arAccount, $rs),
								'#ACC_NAME#' => VettichPosting::GetUrlPostAcc('facebook', $acc_id, $arAccount['name']),
							)
						);
						VettichPostingLogs::addLog('facebook', $text, 'Success');
					}
				}
				catch (Facebook\Exceptions\FacebookResponseException $e)
				{
					if(VOptions::get('fb_log_error', false, VettichPostingFunc::module_id()) == 'Y')
					{
						$text = GetMessage('FB_ERROR',
							array(
								'#CODE#' => $e->getCode(),
								'#MESSAGE#'=>$e->getMessage(),
								'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
								'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
								'#ID#' => $arFields['ID'],
								'#ACC_NAME#' => VettichPosting::GetUrlPostAcc('facebook', $acc_id, $arAccount['name']),
							)
						);
						VettichPostingLogs::addLog('facebook', $text, 'Error');
					}
				} catch (Facebook\Exceptions\FacebookSDKException $e) {
					if(VOptions::get('fb_log_error', false, VettichPostingFunc::module_id()) == 'Y')
					{
						$text = GetMessage('FB_ERROR',
							array(
								'#CODE#' => $e->getCode(),
								'#MESSAGE#'=>$e->getMessage(),
								'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
								'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
								'#ID#' => $arFields['ID'],
								'#ACC_NAME#' => VettichPosting::GetUrlPostAcc('facebook', $acc_id, $arAccount['name']),
							)
						);
						VettichPostingLogs::addLog('facebook', $text, 'Error');
					}
				}
			}
		}
	}

	function getUrlPost($arAccount, $response)
	{
		return 'http://facebook.com/'.$arAccount['group_id'];
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
			elseif($sProp == 'DETAIL_PICTURE' 
				or $sProp == 'PREVIEW_PICTURE')
			{
				$imgPath = CFile::GetPath($arFields[$sProp]);
				if($imgPath)
					$result = self::get_link($imgPath, $arSite, $arPost);
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
}