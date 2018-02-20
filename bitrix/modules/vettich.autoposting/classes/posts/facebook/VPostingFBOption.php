<?
IncludeModuleLangFile(__FILE__);

class VPostingFBOption
{
	function get_list()
	{
		$result = array();
		foreach(self::GetIDs() as $i)
		{
			$result[$i] = CVDB::get('fb_accounts['.$i.'][name]');
		}
		return $result;
	}

	function get_default()
	{
		return array();
	}

	function PageTitle()
	{
		return GetMessage('FB_PAGE_TITLE');
	}

	function EditPageTitle($id=1)
	{
		if($id > 0)
			return GetMessage('FB_EDIT_PAGE_TITLE');
		return GetMessage('FB_ADD_PAGE_TITLE');
	}

	function GetFields()
	{
		return array(
			'id' => 'ID',
			'name' => GetMessage('FB_ACCOUNTS_NAME'),
			'is_enable' => GetMessage('FB_IS_ENABLE'),
			'group_id' => GetMessage('FB_GROUP_ID'),
			'app_id' => GetMessage('FB_APP_ID'),
			'app_secret' => GetMessage('FB_APP_SECRET'),
		);
	}

	static function ChangeRow(&$row, $values)
	{
		if(empty($row))
			return;
		
		$row->AddInputField("name", array("size"=>20));
		$row->AddInputField("group_id", array("size"=>18));
		$row->AddInputField("app_id", array("size"=>18));
		$row->AddInputField("app_secret", array("size"=>18));

		$row->AddViewField('is_enable', $values['is_enable'] == 'Y' ? GetMessage('YES') : GetMessage('NO'));
		$row->AddCheckField('is_enable');
	}

	function GetArModuleParamsPosts($index, $iblock_id=false)
	{
		$arProps = VettichPostingOption::getProps();
		if(!$iblock_id)
			$iblock_id = VettichPostingOption::GetByID($index, 'iblock_id');
		$values = isset($arProps[$iblock_id]) ? $arProps[$iblock_id] : array('none' => 'none');
		$arPostParams = array(
			'TABS' => array(
				'FB_TAB' => array(
					'NAME' => GetMessage('FB_TAB_NAME'),
					'TITLE' => GetMessage('FB_TAB_TITLE')
				)
			),
			'PARAMS' => array(
				'fb_publish_date' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('POST_FB_PUBLISH_DATE'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'VALUE' => VettichPostingOption::GetByID($index, 'fb_publish_date'),
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 2010,
					'HELP' => GetMessage('POSTS_FB_PUBLISH_DATE_HELP'),
				),
				'fb_link' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('POST_FB_LINK'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'VALUE' => VettichPostingOption::GetByID($index, 'fb_link'),
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 2020,
					'HELP' => GetMessage('POSTS_FB_LINK_HELP'),
				),
				'fb_photo' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('POST_FB_PHOTO'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'VALUE' => VettichPostingOption::GetByID($index, 'fb_photo'),
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 2030,
					'HELP' => GetMessage('POST_FB_PHOTO_HELP'),
				),
				'fb_name' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('POST_FB_NAME'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'VALUE' => VettichPostingOption::GetByID($index, 'fb_name'),
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 2040,
					'HELP' => GetMessage('POSTS_FB_NAME_HELP'),
				),
				'fb_description' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('POST_FB_DESCRIPTION'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'VALUE' => VettichPostingOption::GetByID($index, 'fb_description'),
					'SIZE' => 0,
					'SORT' => 2050,
					'HELP' => GetMessage('POST_FB_DESCRIPTION_HELP'),
				),
				'fb_message' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('POST_FB_MESSAGE'),
					'TYPE' => 'TEXTAREA',
					'CHOISE' => 'SIMPLE',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUE' => VettichPostingOption::GetByID($index, 'fb_message'),
					'VALUES' => $values,
					'SORT' => 2060,
					'HELP' => GetMessage('POST_FB_MESSAGE_HELP'),
				),
			)
		);

		return $arPostParams;
	}

	function GetArModuleParams($index)
	{
		$arModuleParams = array(
			'TAB_CONTROL_POSTFIX' => 'facebook',
			'FORM' => array(
				'PARAMS' => array(
					'ID' => $index,
				),
				'TYPE' => array('WITH_PARAMS', 'NOT_DEFAULT'),
			),
			'TABS' => array(
				'FB_TAB' => array(
					'NAME' => GetMessage('FB_TAB_NAME'),
					'TITLE' => GetMessage('FB_TAB_TITLE')
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
					'TAB' 			=> 'FB_TAB',
					'NAME' 			=> GetMessage('FB_ACCOUNTS_NAME'),
					'DESCRIPTION' 	=> GetMessage('FB_ACCOUNTS_NAME_DESCRIPTION'),
					'VALUE' 		=> self::GetByID($index, 'name'),
					'TYPE' 			=> 'STRING',
					'REQUIRED' 		=> 'Y',
				),
				'is_enable' => array(
					'TAB' 		=> 'FB_TAB',
					'NAME' 		=> GetMessage('FB_IS_ENABLE'),
					'TYPE' 		=> 'CHECKBOX',
					'VALUE' 	=> self::GetByID($index, 'is_enable'),
				),
				'group_id' => array(
					'TAB' 			=> 'FB_TAB',
					'NAME' 			=> GetMessage('FB_GROUP_ID'),
					'DESCRIPTION' 	=> GetMessage('FB_GROUP_ID_DESCRIPTION'),
					'VALUE' 		=> self::GetByID($index, 'group_id'),
					'TYPE' 			=> 'STRING',
					'HELP'			=> GetMessage('FB_GROUP_ID_HELP'),
				),
				'app_id' => array(
					'TAB' 		=> 'FB_TAB',
					'NAME' 		=> GetMessage('FB_APP_ID'),
					'VALUE' 	=> self::GetByID($index, 'app_id'),
					'TYPE' 		=> 'STRING',
				),
				'app_secret' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('FB_APP_SECRET'),
					'DESCRIPTION' => GetMessage(''),
					'VALUE' 	=> self::GetByID($index, 'app_secret'),
					'TYPE' => 'STRING',
				),
				'access_token' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('FB_ACCOUNTS_ACCESS_TOKEN'),
					'VALUE' 	=> self::GetByID($index, 'access_token'),
					'TYPE' => 'STRING',
				),
				'access_token_button' => array(
					'TAB' => 'FB_TAB',
					'NAME' => '',
					'TYPE' => 'CUSTOM',
					'HTML' => GetMessage('FB_access_token_button').
						'<script>ALERT_NOT_APP_ID="'.GetMessage('ALERT_NOT_APP_ID').'"</script>',
				),
				'fb_help1' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('FB_HELP1_NAME'),
					'TYPE' => 'NOTE',
					'TEXT' => GetMessage('FB_HELP1_TEXT'),
				),
			)
		);

		$hlp = VettichPostingFunc::vettich_service('get_url', 'url=autoposting.facebook.video_help');
		if(!empty($hlp['url']))
		{
			$arModuleParams['TABS']['FB_TAB_VIDEO'] = array(
				'NAME' => GetMessage('FB_TAB_VIDEO_NAME'),
				'TITLE' => GetMessage('FB_TAB_VIDEO_TITLE')
			);
			$arModuleParams['PARAMS']['video_help'] = array(
				'TAB' => 'FB_TAB_VIDEO',
				'TYPE' => 'NOTE',
				'TEXT' => VettichPostingFunc::get_youtube_frame($hlp['url']),
			);
		}

		global $arIncludeJS;
		$arIncludeJS[] = '/bitrix/js/vettich.autoposting/fb_options.js';

		return $arModuleParams;
	}

	function SaveParams($index=0, $arParams=false)
	{
		if($arParams===false)
		{
			$arParams = self::GetArModuleParams($index);
			$arParams = $arParams['PARAMS'];
		}

		VettichPostingOption::SaveParams($index, $arParams, 'fb_accounts');
	}

	function GetIDs()
	{
		return VettichPostingOption::GetIDs('fb_accounts');
	}

	function GetByID($index, $param_name=false)
	{
		return VettichPostingOption::GetByID($index, $param_name, 'fb_accounts');
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
				$arResult[$i][$k] = CVDB::get('fb_accounts['.$i.']['.$k.']', '');
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
		VettichPostingOption::Save($id, $arFields, 'fb_accounts');
	}

	function SaveFields($fields)
	{
		VettichPostingOption::SaveFields($fields, 'fb_accounts');
	}

	function Delete($id)
	{
		VettichPostingOption::Delete($id, 'fb_accounts');
	}
}