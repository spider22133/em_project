<?
namespace Vettich\Autoposting\Posts\vk;
use Vettich\Autoposting\PostingFunc;
use Vettich\Autoposting\PostingLogs;

IncludeModuleLangFile(__FILE__);

class Posting extends \Vettich\Autoposting\Posting
{
	static private $app_id = '5139034';

	function method($method, $data)
	{
		if($method == '')
			return false;

		$url = 'https://api.vk.com/method/'.$method;
		$return = json_decode(PostingFunc::_curl_post($url, $data, false), 1);
		return $return;
	}

	/**
	* Публикует запись в ВКонтакте
	* 
	* @param array $arFields поля публикуемого элемента
	* @param array $arAcconts в какие аккаунты публиковать
	* @param array $arPost данные о публикации
	* @param array $arSite массив полей сайта
	* @param array $arOptionally массив дополнительных параметров
	*	например array('post_ids' => array(acc_id => post_id), 
	*		'type' => 'post|edit|delete')
	*/
	function post($arFields, $arAccounts, $arPost, $arSite, $arOptionally=array())
	{
		global $APPLICATION;
		$type = $arOptionally['type'];

		if(\COption::GetOptionString(PostingFunc::module_id(), 'is_vk_enable', false) != 'Y')
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

			$post_data = array();
			$post_data['access_token'] = $arAccount['ACCESS_TOKEN'];
			$post_data['owner_id'] = ($arAccount['GROUP_PUBLISH']=='Y'?'-':'').$arAccount['GROUP_PUBLISH_ID'];

			if(($type == 'delete' or $type == 'edit')
				&& $arAccount['VK_PUBLICATION_MODE'] == 'none')
				continue;

			if(empty($arAccount['VK_PUBLICATION_MODE']))
				$arAccount['VK_PUBLICATION_MODE'] = 'update';

			if($type == 'delete'
				or ($type == 'edit'
					&& $arAccount['VK_PUBLICATION_MODE'] == 'del_add'))
			{
				if(!empty($post_id))
				{
					$post_data['post_id'] = $post_id;
					$rs = self::method('wall.delete', $post_data);
					unset($post_data['post_id']);
				}
			}

			if($type != 'delete')
			{
				if($arAccount['VK_PUBLISH_DATE'] != '' && $arAccount['VK_PUBLISH_DATE'] != 'none')
				{
					$time = strtotime($arFields[$arAccount['VK_PUBLISH_DATE']]);
					if($time > time())
						$post_data['publish_date'] = $time;
				}

				$post_data['from_group'] = 0;
				if($arAccount['IS_GROUP_PUBLISH'] == 'Y')
					$post_data['from_group'] = 1;

				$post_data['message'] = parent::replaceMacros($arAccount['VK_MESSAGE'], $arFields, $arSite, $arPost);
				$post_data['message'] = strip_tags($post_data['message']);
				$post_data['message'] = $APPLICATION->ConvertCharset($post_data['message'], SITE_CHARSET, "UTF-8");
				$post_data['message'] = trim(html_entity_decode($post_data['message']));
				if(empty($post_data['message']))
					unset($post_data['message']);

				if($arAccount['VK_PHOTO'] != '' && $arAccount['VK_PHOTO'] != 'none')
				{
					$res = self::attach_photo($arAccount['VK_PHOTO'], $arFields, $arAccount, $arPost);
					if(!empty($res))
						$post_data['attachments'] = $res;
				}
				if($arAccount['VK_PHOTOS'] != $arAccount['VK_PHOTO']
					&& $arAccount['VK_PHOTOS'] != ''
					&& $arAccount['VK_PHOTOS'] != 'none')
				{
					$res = self::attach_photo($arAccount['VK_PHOTOS'], $arFields, $arAccount, $arPost);
					if(!empty($res))
						$post_data['attachments'] = array_merge((array)$post_data['attachments'], $res);
				}
				if(count($post_data['attachments']) > 10)
					array_splice($post_data['attachments'], 10);

				if($arAccount['VK_LINK'] != '' && $arAccount['VK_LINK'] != 'none')
				{
					$res = self::getLinkFromProperty($arAccount['VK_LINK'], $arFields, $arPost, $arSite);
					if(!empty($res))
					{
						if(count($post_data['attachments']) > 9)
							array_splice($post_data['attachments'], 9);
						$post_data['attachments'][] = $res;
					}
				}

				if(isset($post_data['attachments']))
					$post_data['attachments'] = implode(',', $post_data['attachments']);

				if($type == 'edit' && !empty($post_id) && $arAccount['VK_PUBLICATION_MODE'] == 'update')
				{
					$post_data['post_id'] = $arOptionally['post_ids'][$acc_id];
					$method = 'wall.edit';
				}
				else
					$method = 'wall.post';

				$rs = self::method($method, $post_data);
			}

			if(isset($rs['error']))
			{
				if(\COption::GetOptionString(PostingFunc::module_id(), 'vk_log_error', false) == 'Y')
				{
					if($rs['error']['error_code'] == 14)
					{
						$text = GetMessage('VK_ERROR_14',
							array(
								'#CODE#' => $rs['error']['error_code'],
								'#MESSAGE#'=>$rs['error']['error_msg'],
								'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
								'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
								'#ID#' => $arFields['ID'],
								'#ACC_NAME#' => parent::GetUrlPostAcc('vk', $acc_id, $arAccount['NAME']),
							)
						);
					}
					else
					{
						if($type == 'delete')
							$text = GetMessage('VK_ERROR_DELETE',
								array(
									'#CODE#' => $rs['error']['error_code'],
									'#MESSAGE#'=>$rs['error']['error_msg'],
									'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
									'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
									'#ID#' => $arFields['ID'],
									'#ACC_NAME#' => parent::GetUrlPostAcc('vk', $acc_id, $arAccount['NAME']),
								)
							);
						else
							$text = GetMessage('VK_ERROR',
									array(
										'#CODE#' => $rs['error']['error_code'],
										'#MESSAGE#'=>$rs['error']['error_msg'],
										'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
										'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
										'#ID#' => $arFields['ID'],
										'#ACC_NAME#' => parent::GetUrlPostAcc('vk', $acc_id, $arAccount['NAME']),
									)
								);

					}
					PostingLogs::addLog('vk', $text, 'Error');
				}
			}
			else
			{
				if(!empty($rs['response']['post_id']))
					$arResult[$acc_id] = $rs['response']['post_id'];
				if(\COption::GetOptionString(PostingFunc::module_id(), 'vk_log_success', false) == 'Y')
				{
					if($type == 'edit' && !empty($post_id))
						$text = GetMessage('VK_SUCCESS_EDIT',
							array(
								'#URL#' => self::getUrlPost($arAccount, $post_id),
								'#ACC_NAME#' => parent::GetUrlPostAcc('vk', $acc_id, $arAccount['NAME']),
							)
						);
					elseif($type == 'delete')
						$text = GetMessage('VK_SUCCESS_DELETE',
							array(
								'#URL#' => self::getUrlPost($arAccount, $post_id),
								'#ACC_NAME#' => parent::GetUrlPostAcc('vk', $acc_id, $arAccount['NAME']),
							)
						);
					else
						$text = GetMessage('VK_SUCCESS',
							array(
								'#URL#' => self::getUrlPost($arAccount, $rs['response']['post_id']),
								'#ACC_NAME#' => parent::GetUrlPostAcc('vk', $acc_id, $arAccount['NAME']),
							)
						);
					PostingLogs::addLog('vk', $text, 'Success');
				}
			}
		}
		return $arResult;
	}

	function getUrlPost($arAccount, $post_id)
	{
		if($arAccount['GROUP_PUBLISH']=='Y')
			return 'https://vk.com/public'.$arAccount['GROUP_PUBLISH_ID'].'?w=wall-'.$arAccount['GROUP_PUBLISH_ID'].'_'.$post_id;
		else
			return 'https://vk.com/id'.$arAccount['GROUP_PUBLISH_ID'].'?w=wall'.$arAccount['GROUP_PUBLISH_ID'].'_'.$post_id;
	}

	function attach_photo($sProp, $arFields, $arAccount, $arPost)
	{
		$result = array();
		$files = parent::getFilesFromProperty($sProp, $arFields);
		if(!empty($files))
		{
			$rs = self::upload_files($files, $arAccount);
			if(isset($rs['response']) && !empty($rs['response']))
				foreach($rs['response'] as $value)
				{
					$result[] = $value['id'];
				}
		}
		return $result;
	}

	function upload_files($arFilesName, $arAccount)
	{
		if(count($arFilesName) > 5)
		{
			$arFilesName = array_chunk($arFilesName, 5);
			$ret = array();
			foreach($arFilesName as $arr)
			{
				$ret = array_merge_recursive($ret, self::upload_files($arr, $arAccount));
			}
			return $ret;
		}

		$files = array();
		foreach($arFilesName as $key => $fileName)
		{
			if(count($files) >= 10)
				break;
			$files['file'.(count($files)+1)] = parent::getCurlFilename($fileName);
		}

		$params = array('access_token' => $arAccount['ACCESS_TOKEN']);
		if($arAccount['GROUP_PUBLISH'] == 'Y')
			$params['group_id'] = $arAccount['GROUP_PUBLISH_ID'];
		else
			$params['user_id'] = $arAccount['GROUP_PUBLISH_ID'];
		$dataArray = self::method('photos.getWallUploadServer', $params);

		$response = json_decode(PostingFunc::_curl_post($dataArray['response']['upload_url'], $files, false), 1);
		$response_photo = json_decode($response['photo'],1);
		if(empty($response) or empty($response_photo))
			return false;

		$data = array(
			'photo' => $response['photo'],
			'server' => $response['server'],
			'hash' => $response['hash'],
			'access_token' => $arAccount['ACCESS_TOKEN'],
		);
		if($arAccount['GROUP_PUBLISH'] == 'Y')
			$data['group_id'] = $arAccount['GROUP_PUBLISH_ID'];
		else
			$data['user_id'] = $arAccount['GROUP_PUBLISH_ID'];
		return self::method('photos.saveWallPhoto', $data);
	}
}