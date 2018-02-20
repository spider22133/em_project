<?
namespace Vettich\Autoposting\Posts\twitter;
use Vettich\Autoposting\PostingFunc;
use Vettich\Autoposting\PostingLogs;

if(!defined('IS_TWITTER_AUTOLOAD'))
{
	define('IS_TWITTER_AUTOLOAD', true);
	require VETTICH_AUTOPOSTING_DIR.'/classes/Twitter/autoload.php';
}
IncludeModuleLangFile(__FILE__);

class Posting extends \Vettich\Autoposting\Posting
{
	/**
	* Публикует запись в Twitter
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

		if(\COption::GetOptionString(PostingFunc::module_id(), 'is_twitter_enable', false) != 'Y')
			return;
		
		$post_data = array();
		foreach($arAccounts as $acc_id)
		{
			$post_id = $arOptionally['post_ids'][$acc_id];
			$arAccount = Func::GetAccountValues($acc_id, $arPost['ID']);
			if(empty($arAccount))
				continue;

			$arPost['arAccount'] = $arAccount;
			$arPost['ACCPREFIX'] = Func::ACCPREFIX;

			if($arAccount['IS_ENABLE'] != 'Y')
				continue;

			if(($type == 'delete' or $type == 'edit')
				&& $arAccount['TWITTER_PUBLICATION_MODE'] == 'none')
				continue;

			if(empty($arAccount['TWITTER_PUBLICATION_MODE']))
				$arAccount['TWITTER_PUBLICATION_MODE'] = 'del_add';

			try{
				$twit = new \Abraham\TwitterOAuth\TwitterOAuth($arAccount['API_KEY'], $arAccount['API_SECRET'], $arAccount['ACCESS_TOKEN'], $arAccount['ACCESS_TOKEN_SECRET']);
				$rs = $twit->get('account/verify_credentials');
			}
			catch(\Exception $e){
				$error = true;
			}
			if(!isset($rs->errors))
			{
				if($type == 'delete'
					or ($type == 'edit'
						&& $arAccount['TWITTER_PUBLICATION_MODE'] == 'del_add'))
				{
					if(!empty($post_id))
					{
						try{
							$post_data['id'] = $post_id['id'];
							$twit->post('statuses/destroy', $post_data);
							unset($post_data['id']);
						} catch (\Exception $e) {
							$error = $e;
						}
					}
					// if($type == 'delete')
					// 	continue;
				}

				if($type != 'delete')
				{
					$message_sep = true;
					if($arAccount['TWITTER_MESSAGE_SEP'] != 'Y')
						$message_sep = false;

					$post_data['status'] = parent::replaceMacros($arAccount['TWITTER_MESSAGE'], $arFields, $arSite, $arAccount);
					$post_data['status'] = strip_tags($post_data['status']);
					$post_data['status'] = $APPLICATION->ConvertCharset($post_data['status'], SITE_CHARSET, "UTF-8");
					$post_data['status'] = trim(html_entity_decode($post_data['status']));

					$media_ids = array();
					if($arAccount['TWITTER_PHOTO'] != '' && $arAccount['TWITTER_PHOTO'] != 'none')
					{
						if($res = self::get_media_ids($arAccount['TWITTER_PHOTO'], $arFields, $twit))
							$media_ids[] = $res;
					}

					if($arAccount['TWITTER_PHOTOS'] != $arAccount['TWITTER_PHOTO'] && $arAccount['TWITTER_PHOTOS'] != '' && $arAccount['TWITTER_PHOTOS'] != 'none')
					{
						if($res = self::get_media_ids($arAccount['TWITTER_PHOTOS'], $arFields, $twit))
							$media_ids[] = $res;
					}

					if(count($media_ids) > 4)
						$media_ids = array_slice($media_ids, 0, 4);

					$status_fsize = 140;
					if(!empty($media_ids))
					{
						$post_data['media_ids'] = implode(',', $media_ids);
						$status_fsize -= 24;
					}
					$post_data['status'] = PostingFunc::substr($post_data['status'], 0, $status_fsize, 'UTF-8', $message_sep, '...');
					if($arAccount['TWITTER_LINK'] != '' && $arAccount['TWITTER_LINK'] != 'none')
					{
						$link = self::getLinkFromProperty($arAccount['TWITTER_LINK'], $arFields, $arPost, $arSite);
						if(!empty($link))
						{
							$status_fsize -= 24;
							$post_data['status'] = PostingFunc::substr($post_data['status'], 0, $status_fsize, 'UTF-8', $message_sep, '...');
							$post_data['status'] .= "\n".$link;
						}
					}

					if($type == 'edit' && !empty($post_id) && $arAccount['TWITTER_PUBLICATION_MODE'] == 'reply')
						$post_data['in_reply_to_status_id'] = $post_id['id'];

					try{
						$rs = $twit->post('statuses/update', $post_data);
					}
					catch(\Exception $e){
						$error = true;
					}
				}
			}

			if(isset($rs->errors))
			{
				if(\COption::GetOptionString(PostingFunc::module_id(), 'twitter_log_error', false) == 'Y')
				{
					if($type == 'edit' && !empty($post_id))
						$text = GetMessage('TWITTER_ERROR_EDIT',
							array(
								'#CODE#' => $rs->errors[0]->code,
								'#MESSAGE#'=>$rs->errors[0]->message,
								'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
								'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
								'#ID#' => $arFields['ID'],
								'#ACC_NAME#' => parent::GetUrlPostAcc('twitter', $acc_id, $arAccount['NAME']),
							)
						);
					if($type == 'delete' && !empty($post_id))
						$text = GetMessage('TWITTER_ERROR_DELETE',
							array(
								'#CODE#' => $rs->errors[0]->code,
								'#MESSAGE#'=>$rs->errors[0]->message,
								'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
								'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
								'#ID#' => $arFields['ID'],
								'#ACC_NAME#' => parent::GetUrlPostAcc('twitter', $acc_id, $arAccount['NAME']),
							)
						);
					else
						$text = GetMessage('TWITTER_ERROR',
							array(
								'#CODE#' => $rs->errors[0]->code,
								'#MESSAGE#'=>$rs->errors[0]->message,
								'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
								'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
								'#ID#' => $arFields['ID'],
								'#ACC_NAME#' => parent::GetUrlPostAcc('twitter', $acc_id, $arAccount['NAME']),
							)
						);
					PostingLogs::addLog('twitter', $text, 'Error');
				}
			}
			elseif($error)
			{
				$arResult[$acc_id] = false;
				if(\COption::GetOptionString(PostingFunc::module_id(), 'twitter_log_error', false) == 'Y')
				{
					$text = GetMessage('TWITTER_ERROR_UNKNOWN',
						array(
							'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
							'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
							'#ID#' => $arFields['ID'],
							'#ACC_NAME#' => parent::GetUrlPostAcc('twitter', $acc_id, $arAccount['NAME']),
						)
					);
					PostingLogs::addLog('twitter', $text, 'Error');
				}
			}
			else
			{
				$post_id = array(
					'user_id' => $rs->user->id,
					'id' => $rs->id
				);
				if($type != 'delete')
					$arResult[$acc_id] = $post_id;
				if(\COption::GetOptionString(PostingFunc::module_id(), 'twitter_log_success', false) == 'Y')
				{
					if($type == 'edit' && !empty($post_id))
						$text = GetMessage('TWITTER_SUCCESS_EDIT',
							array(
								'#URL#' => self::getUrlPost($arAccount, $post_id),
								'#ACC_NAME#' => parent::GetUrlPostAcc('twitter', $acc_id, $arAccount['NAME']),
							)
						);
					elseif($type == 'delete' && !empty($post_id))
						$text = GetMessage('TWITTER_SUCCESS_DELETE',
							array(
								'#URL#' => self::getUrlPost($arAccount, $post_id),
								'#ACC_NAME#' => parent::GetUrlPostAcc('twitter', $acc_id, $arAccount['NAME']),
							)
						);
					else
						$text = GetMessage('TWITTER_SUCCESS',
							array(
								'#URL#' => self::getUrlPost($arAccount, $post_id),
								'#ACC_NAME#' => parent::GetUrlPostAcc('twitter', $acc_id, $arAccount['NAME']),
							)
						);
					PostingLogs::addLog('twitter', $text, 'Success');
				}
			}
		}
		return $arResult;
	}

	function getUrlPost($arAccount, $post_id)
	{
		return 'http://twitter.com/'.$post_id['user_id'].'/status/'.$post_id['id'];
	}

	function get_media_ids($sProp, $arFields, &$twit)
	{
		$result = array();

		$files = parent::getFilesFromProperty($sProp, $arFields);
		$files = array_slice($files, 0, 4);
		foreach($files as $file_name)
		{
			if($media_id = self::get_media_id($file_name, $twit))
				$result[] = $media_id;
		}
		return implode(',', $result);
	}

	function get_media_id($file_name, &$twit)
	{
		try{
			$res = $twit->upload('media/upload', array('media'=>$file_name));
		}
		catch(\Exception $e){
			return false;
		}
		if(!isset($res->error))
			return $res->media_id_string;

		return false;
	}
}