<?
namespace Vettich\Autoposting\Posts\facebook;
use Facebook;
use Vettich\Autoposting\PostingFunc;
use Vettich\Autoposting\PostingLogs;

if(!defined('IS_FACEBOOK_AUTOLOAD') && !version_compare(PHP_VERSION, '5.4.0', '<'))
{
	define('IS_FACEBOOK_AUTOLOAD', true);
	require VETTICH_AUTOPOSTING_DIR.'/classes/Facebook/autoload.php';
}
IncludeModuleLangFile(__FILE__);

class Posting extends \Vettich\Autoposting\Posting
{
	/**
	* Публикует запись в Facebook
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
		
		if(\COption::GetOptionString(PostingFunc::module_id(), 'is_fb_enable', false) != 'Y')
			return;

		if(!empty($arOptionally['post_ids']))
			$arResult = $arOptionally['post_ids'];
		else
			$arResult = array();

		foreach($arAccounts as $acc_id)
		{
			$post_id = $arResult[$acc_id];
			$arAccount = Func::GetAccountValues($acc_id, $arPost['ID']);
			if(empty($arAccount))
				continue;

			$arPost['arAccount'] = $arAccount;
			$arPost['ACCPREFIX'] = Func::ACCPREFIX;

			if($arAccount['IS_ENABLE'] != 'Y')
				continue;

			if(($type == 'delete' or $type == 'edit')
				&& $arAccount['FB_PUBLICATION_MODE'] == 'none')
				continue;

			$fb = new Facebook\Facebook(array(
				'app_id'				=> $arAccount['APP_ID'],
				'app_secret'			=> $arAccount['APP_SECRET'],
				'default_graph_version'	=> 'v2.5',
				'default_access_token'	=> $arAccount['ACCESS_TOKEN'],
			));

			try{
				$result = $fb->get('/'. $arAccount['GROUP_ID'] .'?fields=access_token')->getGraphObject()->asArray();
				if(isset($result['access_token']))
					$fb->setDefaultAccessToken($result['access_token']);
			}
			catch (Facebook\Exceptions\FacebookResponseException $e){
				// continue;
			} catch (Facebook\Exceptions\FacebookSDKException $e) {
				// continue;
			}

			if(empty($arAccount['FB_PUBLICATION_MODE']))
				$arAccount['FB_PUBLICATION_MODE'] = 'update';

			if($type == 'delete'
				or ($type == 'edit'
					&& $arAccount['FB_PUBLICATION_MODE'] == 'del_add'))
			{
				if(!empty($post_id))
				{
					try{
						$result = $fb->delete($post_id);
						if(\COption::GetOptionString(PostingFunc::module_id(), 'fb_log_success', false) == 'Y')
						{
							$text = GetMessage('FB_SUCCESS_DELETE',
								array(
									'#URL#' => self::getUrlPost($arAccount, $post_id),
									'#ACC_NAME#' => parent::GetUrlPostAcc('facebook', $acc_id, $arAccount['NAME']),
								)
							);
							PostingLogs::addLog('facebook', $text, 'Success');
						}
					} catch(Facebook\Exceptions\FacebookSDKException $error) {
						if(\COption::GetOptionString(PostingFunc::module_id(), 'fb_log_error', false) == 'Y')
						{
							$text = GetMessage('FB_ERROR_DELETE',
								array(
									'#CODE#' => $error->getCode(),
									'#MESSAGE#' => $error->getMessage(),
									'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
									'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
									'#ID#' => $arFields['ID'],
									'#URL#' => self::getUrlPost($arAccount, $post_id),
									'#ACC_NAME#' => parent::GetUrlPostAcc('facebook', $acc_id, $arAccount['NAME']),
								)
							);
							PostingLogs::addLog('facebook', $text, 'Error');
						}
					}
				}
				continue;
			}

			$post_data = array();

			if($arAccount['FB_PUBLISH_DATE'] != '' && $arAccount['FB_PUBLISH_DATE'] != 'none')
			{
				$time = strtotime($arFields[$arAccount['FB_PUBLISH_DATE']]);
				if($time > time())
				{
					$post_data['scheduled_publish_time'] = $time;
					$post_data['published'] = false;
				}
			}

			$post_data['message'] = parent::replaceMacros($arAccount['FB_MESSAGE'], $arFields, $arSite, $arPost);
			$post_data['message'] = strip_tags($post_data['message']);
			$post_data['message'] = $APPLICATION->ConvertCharset($post_data['message'], SITE_CHARSET, "UTF-8");
			$post_data['message'] = trim(html_entity_decode($post_data['message']));

			if(empty($post_data['message']))
				unset($post_data['message']);

			if($arAccount['FB_LINK'] != '' && $arAccount['FB_LINK'] != 'none')
			{
				$post_data['link'] = self::getLinkFromProperty($arAccount['FB_LINK'], $arFields, $arPost, $arSite);
			}

			if($arAccount['FB_PHOTO'] != '' && $arAccount['FB_PHOTO'] != 'none')
			{
				$res = self::getLinkFromProperty($arAccount['FB_PHOTO'], $arFields, $arPost, $arSite);
				if($res)
					$post_data['picture'] = $res;
			}

			if($arAccount['FB_NAME'] != '' && $arAccount['FB_NAME'] != 'none' && !empty($post_data['link']))
			{
				$post_data['name'] = parent::getStringFromProperty($arAccount['FB_NAME'], $arFields, $arSite, $arPost);
				$post_data['name'] = strip_tags($post_data['name']);
				$post_data['name'] = $APPLICATION->ConvertCharset($post_data['name'], SITE_CHARSET, "UTF-8");
				$post_data['name'] = html_entity_decode($post_data['name']);
				$post_data['name'] = substr($post_data['name'], 0, 255);
			}

			if($arAccount['FB_DESCRIPTION'] != '' && $arAccount['FB_DESCRIPTION'] != 'none' && !empty($post_data['link']))
			{
				$post_data['description'] = parent::getStringFromProperty($arAccount['FB_DESCRIPTION'], $arFields, $arSite, $arPost);
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
				if($type == 'edit' && !empty($post_id) && $arAccount['FB_PUBLICATION_MODE'] == 'update')
					$rs = $fb->post('/'.$post_id, $post_data)->getDecodedBody();
				else
					$rs = $fb->post('/'.$arAccount['GROUP_ID'].'/feed', $post_data)->getDecodedBody();
				if($type != 'delete' && $type != 'edit')
					$arResult[$acc_id] = $rs['id'];
				if($plink)
				{
					$text = GetMessage('FB_WARNING_NOT_URL',
						array(
							'#URL#' => self::getUrlPost($arAccount, $arResult[$acc_id]),
							'#ACC_NAME#' => parent::GetUrlPostAcc('facebook', $acc_id, $arAccount['NAME']),
						)
					);
					PostingLogs::addLog('facebook', $text, 'Warning');
				}
				elseif(\COption::GetOptionString(PostingFunc::module_id(), 'fb_log_success', false) == 'Y')
				{
					$text = GetMessage('FB_SUCCESS',
						array(
							'#URL#' => self::getUrlPost($arAccount, $arResult[$acc_id]),
							'#ACC_NAME#' => parent::GetUrlPostAcc('facebook', $acc_id, $arAccount['NAME']),
						)
					);
					PostingLogs::addLog('facebook', $text, 'Success');
				}
			} catch (Facebook\Exceptions\FacebookResponseException $e) {
				$error = $e;
			} catch (Facebook\Exceptions\FacebookSDKException $e) {
				$error = $e;
			}
			if(!!$error
				&& $error->getCode() == 100
				&& strpos($error->getMessage(), 'link is not properly formatted') !== false)
			{
				try {
					$ulink = $post_data['link'];
					unset($post_data['link']);
					unset($post_data['picture']);
					unset($post_data['name']);
					unset($post_data['description']);
					if($type == 'edit' && !empty($post_id) && $arAccount['FB_PUBLICATION_MODE'] == 'update')
						$rs = $fb->post('/'.$post_id, $post_data)->getDecodedBody();
					else
						$rs = $fb->post('/'.$arAccount['GROUP_ID'].'/feed', $post_data)->getDecodedBody();
					if($type != 'delete' && $type != 'edit')
						$arResult[$acc_id] = $rs['id'];
					if(\COption::GetOptionString(PostingFunc::module_id(), 'fb_log_error', false) == 'Y')
					{
						$text = GetMessage('FB_WARNING_INCORRECT_URL',
							array(
								'#ULINK#' => $ulink,
								'#CODE#' => $error->getCode(),
								'#MESSAGE#' => $error->getMessage(),
								'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
								'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
								'#ID#' => $arFields['ID'],
								'#URL#' => self::getUrlPost($arAccount, $arResult[$acc_id]),
								'#ACC_NAME#' => parent::GetUrlPostAcc('facebook', $acc_id, $arAccount['NAME']),
							)
						);
						PostingLogs::addLog('facebook', $text, 'Warning');
					}
					$error = false;
				} catch (Facebook\Exceptions\FacebookResponseException $e) {
					$error = $e;
				} catch (Facebook\Exceptions\FacebookSDKException $e) {
					$error = $e;
				}
			}
			if(!!$error)
			{
				if(\COption::GetOptionString(PostingFunc::module_id(), 'fb_log_error', false) == 'Y')
				{
					$text = GetMessage('FB_ERROR',
						array(
							'#CODE#' => $error->getCode(),
							'#MESSAGE#' => $error->getMessage(),
							'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
							'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
							'#ID#' => $arFields['ID'],
							'#ACC_NAME#' => parent::GetUrlPostAcc('facebook', $acc_id, $arAccount['NAME']),
						)
					);
					PostingLogs::addLog('facebook', $text, 'Error');
				}
			}
		}
		return $arResult;
	}

	function getUrlPost($arAccount, $node_id)
	{
		return 'http://facebook.com/'.$node_id;
	}
}