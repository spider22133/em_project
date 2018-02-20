<?
IncludeModuleLangFile(__FILE__);

class VPostingOKOption
{
	function get_list()
	{
		$result = array();
		foreach(self::GetIDs() as $i)
		{
			$result[$i] = CVDB::get('odnoklassniki_accounts['.$i.'][name]');
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

	function EditPageTitle($id=1)
	{
		if($id > 0)
			return GetMessage('ODNOKLASSNIKI_EDIT_PAGE_TITLE');
		return GetMessage('ODNOKLASSNIKI_ADD_PAGE_TITLE');
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
		$arProps = VettichPostingOption::getProps();
		$iblock_id = VettichPostingOption::GetByID($index, 'iblock_id');
		$values = isset($arProps[$iblock_id]) ? $arProps[$iblock_id] : array('none' => 'none');
		$arPostParams = array(
			'TABS' => array(
				'ODNOKLASSNIKI_TAB' => array(
					'NAME' => GetMessage('ODNOKLASSNIKI_TAB_NAME'),
					'TITLE' => GetMessage('ODNOKLASSNIKI_TAB_TITLE')
				)
			),
			'PARAMS' => array(
				'odnoklassniki_publish_date' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('POST_ODNOKLASSNIKI_PUBLISH_DATE'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'VALUE' => VettichPostingOption::GetByID($index, 'odnoklassniki_publish_date'),
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1001,
					'HELP' => GetMessage('POST_ODNOKLASSNIKI_PUBLISH_DATE_HELP'),
				),
				'odnoklassniki_photo' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('POST_ODNOKLASSNIKI_PHOTO'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'VALUE' => VettichPostingOption::GetByID($index, 'odnoklassniki_photo'),
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1011,
					'HELP' => GetMessage('POST_ODNOKLASSNIKI_PHOTO_HELP'),
				),
				'odnoklassniki_photo_other' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('POST_ODNOKLASSNIKI_PHOTO_OTHER'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'VALUE' => VettichPostingOption::GetByID($index, 'odnoklassniki_photo_other'),
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1012,
					'HELP' => GetMessage('POST_ODNOKLASSNIKI_PHOTO_OTHER_HELP'),
				),
				'odnoklassniki_link' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('POST_ODNOKLASSNIKI_LINK'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'VALUE' => VettichPostingOption::GetByID($index, 'odnoklassniki_link'),
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1020,
					'HELP' => GetMessage('POST_ODNOKLASSNIKI_LINK_HELP'),
				),
				'odnoklassniki_message' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('POST_ODNOKLASSNIKI_MESSAGE'),
					'DESCRIPTION' => GetMessage('POST_ODNOKLASSNIKI_MESSAGE_DESCRIPTION'),
					'TYPE' => 'TEXTAREA',
					'VALUE' => VettichPostingOption::GetByID($index, 'odnoklassniki_message'),
					'CHOISE' => 'SIMPLE',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'SORT' => 1050,
					'HELP' => GetMessage('POST_ODNOKLASSNIKI_MESSAGE_HELP'),
				),
			),
		);

		return $arPostParams;
	}

	function GetArModuleParams($index)
	{
		$arModuleParams = array(
			'TAB_CONTROL_POSTFIX' => 'odnoklassniki',
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
					'VALUE' 		=> self::GetByID($index, 'name'),
					'TYPE' => 'STRING',
					'REQUIRED' => 'Y',
				),
				'is_enable' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_IS_ENABLE'),
					'VALUE' => self::GetByID($index, 'is_enable'),
					'TYPE' => 'CHECKBOX',
				),
				'is_group_publish' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_IS_GROUP_PUBLISH'),
					'VALUE' => self::GetByID($index, 'is_group_publish'),
					'TYPE' => 'CHECKBOX',
				),
				'group_id' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_GROUP_ID'),
					'VALUE' => self::GetByID($index, 'group_id'),
					'TYPE' => 'STRING',
				),
				'api_id' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_API_ID'),
					'VALUE' => self::GetByID($index, 'api_id'),
					'TYPE' => 'STRING',
				),
				'api_public_key' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_API_PUBLIC_KEY'),
					'VALUE' => self::GetByID($index, 'api_public_key'),
					'TYPE' => 'STRING',
				),
				'api_secret_key' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_API_SECRET_KEY'),
					'VALUE' => self::GetByID($index, 'api_secret_key'),
					'TYPE' => 'STRING',
				),
				'access_token' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_ACCESS_TOKEN'),
					'VALUE' => self::GetByID($index, 'access_token'),
					'TYPE' => 'STRING',
				),
				'note1' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_NOTE'),
					'TEXT' => GetMessage('ODNOKLASSNIKI_NOTE_TEXT'),
					'VALUE' => '',
					'TYPE' => 'NOTE',
				),
			)
		);

		$hlp = VettichPostingFunc::vettich_service('get_url', 'url=autoposting.odnoklassniki.video_help');
		if(!empty($hlp['url']))
		{
			$arModuleParams['TABS']['ODNOKLASSNIKI_TAB_VIDEO'] = array(
				'NAME' => GetMessage('ODNOKLASSNIKI_TAB_VIDEO_NAME'),
				'TITLE' => GetMessage('ODNOKLASSNIKI_TAB_VIDEO_TITLE')
			);
			$arModuleParams['PARAMS']['video_help'] = array(
				'TAB' => 'ODNOKLASSNIKI_TAB_VIDEO',
				'TYPE' => 'NOTE',
				'TEXT' => VettichPostingFunc::get_youtube_frame($hlp['url']),
			);
		}

		return $arModuleParams;
	}

	function SaveParams($index=0, $arParams=false)
	{
		if($arParams===false)
		{
			$arParams = self::GetArModuleParams($index);
			$arParams = $arParams['PARAMS'];
		}

		VettichPostingOption::SaveParams($index, $arParams, 'odnoklassniki_accounts');
	}

	function GetIDs()
	{
		return VettichPostingOption::GetIDs('odnoklassniki_accounts');
	}

	function GetByID($index, $param_name=false)
	{
		return VettichPostingOption::GetByID($index, $param_name, 'odnoklassniki_accounts');
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
				$arResult[$i][$k] = CVDB::get('odnoklassniki_accounts['.$i.']['.$k.']', '');
			}
			if(array_key_exists('id', $arFields))
			{
				$arResult[$i]['id'] = $i;
			}
		}
		VettichPostingFunc::_usort($arResult, $sort);
		return $arResult;
	}

	function Save($id, $arFields)
	{
		VettichPostingOption::Save($id, $arFields, 'odnoklassniki_accounts');
	}

	function SaveFields($fields)
	{
		VettichPostingOption::SaveFields($fields, 'odnoklassniki_accounts');
	}

	function Delete($id)
	{
		VettichPostingOption::Delete($id, 'odnoklassniki_accounts');
	}
}