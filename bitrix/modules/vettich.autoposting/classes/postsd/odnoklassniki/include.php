<?
IncludeModuleLangFile(__FILE__);

if(!defined('IS_ODNOKLASSNIKI_SDK_LOAD'))
{
	define('IS_ODNOKLASSNIKI_SDK_LOAD', true);
	require_once __DIR__.'/../../Odnoklassniki/odnoklassniki_sdk.php';
}

class odnoklassniki
{
	function get_name()
	{
		return GetMessage('ODNOKLASSNIKI_NAME');
	}

	function get_list()
	{
		$result = array();
		foreach(self::GetIDs() as $i)
		{
			$result[$i] = VOptions::getGroup('odnoklassniki_accounts', 'name', $i, VettichPosting::module_id());
		}
		return $result;
	}

	function get_default()
	{
		return array();
	}

	function PageTitle()
	{
		return GetMessage('ODNOKLASSNIKI_PAGE_TITLE');
	}

	function GetFields()
	{
		return array(
			'id' => 'ID',
			'name' => GetMessage('ODNOKLASSNIKI_ACCOUNTS_NAME'),
			'is_enable' => GetMessage('ODNOKLASSNIKI_IS_ENABLE'),
			'api_id' => GetMessage('ODNOKLASSNIKI_API_ID'),
			'api_public_key' => GetMessage('ODNOKLASSNIKI_API_PUBLIC_KEY'),
			'api_secret_key' => array('content'=>GetMessage('ODNOKLASSNIKI_API_SECRET_KEY'), 'default'=>false),
			'access_token' => array('content'=>GetMessage('ODNOKLASSNIKI_ACCESS_TOKEN'), 'default'=>false),
		);
	}

	static function ChangeRow(&$row, $values)
	{
		if(empty($row))
			return;
		
		$row->AddInputField("name", array("size"=>20));
		$row->AddInputField("api_id", array("size"=>18));
		$row->AddInputField("api_secret", array("size"=>18));
		$row->AddInputField("api_secret_key", array("size"=>18));
		$row->AddInputField("access_token", array("size"=>18));

		$row->AddViewField('is_enable', $values['is_enable'] == 'Y' ? GetMessage('YES') : GetMessage('NO'));
		$row->AddCheckField('is_enable');
	}

	function GetArModuleParamsPosts($index)
	{
		$arProps = VettichPosting::getProps();
		$iblock_id = VettichPosting::GetByID($index, 'iblock_id');
		$values = isset($arProps[$iblock_id]) ? $arProps[$iblock_id] : array('none' => 'none');
		$arPostParams = array(
			'TABS' => array(
				'ODNOKLASSNIKI_TAB' => array(
					'NAME' => GetMessage('ODNOKLASSNIKI_TAB_NAME'),
					'TITLE' => GetMessage('ODNOKLASSNIKI_TAB_TITLE')
				)
			),
			'PARAMS' => array(
				'odnoklassniki_photo' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('POST_ODNOKLASSNIKI_PHOTO'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'DEFAULT' => VettichPosting::GetByID($index, 'odnoklassniki_photo'),
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1011,
				),
				'odnoklassniki_message' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('POST_ODNOKLASSNIKI_MESSAGE'),
					'DESCRIPTION' => GetMessage('POST_ODNOKLASSNIKI_MESSAGE_DESCRIPTION'),
					'TYPE' => 'TEXTAREA',
					'DEFAULT' => VettichPosting::GetByID($index, 'odnoklassniki_message'),
					'CHOISE' => 'SIMPLE',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'SORT' => 1050,
				),
			),
		);

		return $arPostParams;
	}

	function GetArModuleParams($index)
	{
		$arModuleParams = array(
			'FORM' => array(
				'PARAMS' => array(
					'ID' => $index,
				),
				'TYPE' => array('WITH_PARAMS', 'NOT_DEFAULT'),
			),
			'TABS' => array(
				'ODNOKLASSNIKI_TAB' => array(
					'NAME' => GetMessage('ODNOKLASSNIKI_TAB_NAME'),
					'TITLE' => GetMessage('ODNOKLASSNIKI_TAB_TITLE')
				)
			),
			'BUTTONS' => array(
				'SAVE' => array(
					'NAME' => GetMessage('SAVE_BUTTON'),
				),
				'APPLY' => array(
					'NAME' => GetMessage('APPLY_BUTTON'),
				),
				'RESTORE_DEFAULTS' => array(
					'ENABLE' => 'N',
				)
			),
			'PARAMS' => array(
				'name' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_ACCOUNTS_NAME'),
					'DESCRIPTION' => GetMessage('ODNOKLASSNIKI_ACCOUNTS_NAME_DESCRIPTION'),
					'DEFAULT' 		=> self::GetByID($index, 'name'),
					'TYPE' => 'STRING',
					'REQUIRED' => 'Y',
				),
				'is_enable' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_IS_ENABLE'),
					'DEFAULT' => self::GetByID($index, 'is_enable'),
					'TYPE' => 'CHECKBOX',
				),
				'api_id' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_API_ID'),
					'DEFAULT' => self::GetByID($index, 'api_key'),
					'TYPE' => 'STRING',
				),
				'api_public_key' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_API_PUBLIC_KEY'),
					'DEFAULT' => self::GetByID($index, 'api_secret'),
					'TYPE' => 'STRING',
				),
				'api_secret_key' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_API_SECRET_KEY'),
					'DEFAULT' => self::GetByID($index, 'api_secret'),
					'TYPE' => 'STRING',
				),
				'access_token' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_ACCESS_TOKEN'),
					'DEFAULT' => self::GetByID($index, 'access_token'),
					'TYPE' => 'STRING',
				),
				'note1' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_NOTE'),
					'TEXT' => GetMessage('ODNOKLASSNIKI_NOTE_TEXT'),
					'DEFAULT' => '',
					'TYPE' => 'NOTE',
				),
			)
		);
		return $arModuleParams;
	}

	function SaveParams($index=0, $arParams=false)
	{
		if($arParams===false)
		{
			$arParams = self::GetArModuleParams($index);
			$arParams = $arParams['PARAMS'];
		}

		VettichPosting::SaveParams($index, $arParams, 'odnoklassniki_accounts');
	}

	function GetIDs()
	{
		return VettichPosting::GetIDs('odnoklassniki_accounts');
	}

	function GetByID($index, $param_name=false)
	{
		return VettichPosting::GetByID($index, $param_name, 'odnoklassniki_accounts');
	}

	function GetList($sort = array('id'=>'asc'), $arFields=false)
	{
		if($arFields===false)
			$arFields = self::GetFields();

		CModule::IncludeModule('iblock');

		$arResult = array();
		foreach(self::GetIDs() as $i)
		{
			foreach($arFields as $k=>$v)
			{
				$arResult[$i][$k] = VOptions::get('odnoklassniki_accounts['.$i.']['.$k.']', '', VettichPosting::module_id());
			}
			if(array_key_exists('id', $arFields))
			{
				$arResult[$i]['id'] = $i;
			}
		}
		VettichPosting::_usort($arResult, $sort);
		return $arResult;
	}

	function Save($id, $arFields)
	{
		VettichPosting::Save($id, $arFields, 'odnoklassniki_accounts');
	}

	function SaveFields($fields)
	{
		VettichPosting::SaveFields($fields, 'odnoklassniki_accounts');
	}

	function Delete($id)
	{
		VettichPosting::Delete($id, 'odnoklassniki_accounts');
	}

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

		if(VOptions::get('is_odnoklassniki_enable') != 'Y')
			return;
		
		$post_data = array();
		foreach($arAccounts as $acc_id)
		{
			if(intval(VOptions::get('odnoklassniki_accounts')) >= intval($acc_id))
			{
				$post_data = array();
				$arAccount = VOptions::getValues('odnoklassniki_accounts', $acc_id);

				if($arAccount['is_enable'] != 'Y')
					continue;

				$twit = new Abraham\TwitterOAuth\TwitterOAuth($arAccount['api_key'], $arAccount['api_secret'], $arAccount['access_token'], $arAccount['access_token_secret']);
				$rs = $twit->get('account/verify_credentials');
				if(!isset($rs->errors))
				{
					$message_sep = true;
					if($arPost['odnoklassniki_message_sep'] != 'Y')
						$message_sep = false;

					$post_data['status'] = VettichPosting::replaceMacros($arPost['odnoklassniki_message'], $arFields, $arSite, $arPost);
					$post_data['status'] = strip_tags($post_data['status']);
					$post_data['status'] = $APPLICATION->ConvertCharset($post_data['status'], SITE_CHARSET, "UTF-8");
					$post_data['status'] = VettichPosting::substr($post_data['status'], 0, 140, 'UTF-8', $message_sep, '...');

					if($arPost['odnoklassniki_link'] != '' && $arPost['odnoklassniki_link'] != 'none')
					{
						$link = self::attach_link($arPost['odnoklassniki_link'], $arFields, $arPost, $arSite);
						if(!empty($link))
						{
							$post_data['status'] = VettichPosting::substr($post_data['status'], 0, 116, 'UTF-8', $message_sep, '...');
							$post_data['status'] .= "\n".$link;
						}
					}

					$media_ids = array();
					if($arPost['odnoklassniki_photo'] != '' && $arPost['odnoklassniki_photo'] != 'none')
					{
						if($res = self::get_media_ids($arPost['odnoklassniki_photo'], $arFields, $twit))
							$media_ids[] = $res;
					}

					if($arPost['odnoklassniki_photos'] != $arPost['odnoklassniki_photo'] && $arPost['odnoklassniki_photos'] != '' && $arPost['odnoklassniki_photos'] != 'none')
					{
						if($res = self::get_media_ids($arPost['odnoklassniki_photos'], $arFields, $twit))
							$media_ids[] = $res;
					}

					if(!empty($media_ids))
						$post_data['media_ids'] = implode(',', $media_ids);

					$rs = $twit->post('statuses/update', $post_data);
				}

				if(isset($rs->errors))
				{
					if(VOptions::get('odnoklassniki_log_error', false, VettichPosting::module_id()) != 'Y')
					{
						$text = GetMessage('ODNOKLASSNIKI_ERROR',
							array(
								'#CODE#' => $rs->errors[0]->code,
								'#MESSAGE#'=>$rs->errors[0]->code,
								'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
								'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
								'#ID#' => $arFields['ID'],
								'#ACC_NAME#' => $arAccount['name'],
							)
						);
						VettichPosting::addLog('odnoklassniki', $text, 'Error');
					}
				}
				else
				{
					if(VOptions::get('odnoklassniki_log_success', false, VettichPosting::module_id()) != 'Y')
					{
						$text = GetMessage('ODNOKLASSNIKI_SUCCESS',
							array(
								'#URL#' => self::getUrlPost($arAccount, $rs),
								'#ACC_NAME#' => $arAccount['name'],
							)
						);
						VettichPosting::addLog('odnoklassniki', $text, 'Success');
					}
				}
			}
		}
	}

	function getUrlPost($arAccount, $response)
	{
		if(isset($response->user->id))
			return 'http://odnoklassniki.com/'.$response->user->id.'/status/'.$response->id;
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
