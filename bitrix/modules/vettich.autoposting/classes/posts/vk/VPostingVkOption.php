<?
IncludeModuleLangFile(__FILE__);

class VPostingVkOption
{
	function get_list()
	{
		$result = array();
		foreach(self::GetIDs() as $i)
		{
			$result[$i] = CVDB::get('vk_accounts['.$i.'][name]');
		}
		return $result;
	}

	function get_default()
	{
		return array();
	}

	function PageTitle()
	{
		return GetMessage('VK_PAGE_TITLE');
	}

	function EditPageTitle($id=1)
	{
		if($id > 0)
			return GetMessage('VK_EDIT_PAGE_TITLE');
		return GetMessage('VK_ADD_PAGE_TITLE');
	}

	function GetFields()
	{
		return array(
			'id' => 'ID',
			'name' => GetMessage('VK_ACCOUNTS_NAME'),
			'is_enable' => GetMessage('VK_IS_ENABLE'),
			'is_group_publish' => GetMessage('IS_GROUP_PUBLISH'),
			'group_publish_id' => GetMessage('GROUP_PUBLISH_ID'),
			'group_id' => GetMessage('VK_ACCOUNTS_GROUP_ID'),
		);
	}

	static function ChangeRow(&$row)
	{
		if(empty($row))
			return;
		
		$row->AddInputField("name", array("size"=>20));
		$row->AddInputField("group_publish_id", array("size"=>18));
		$row->AddInputField("group_id", array("size"=>18));

		$row->AddViewField('is_enable', $row->arRes['is_enable'] == 'Y' ? GetMessage('YES') : GetMessage('NO'));
		$row->AddViewField('is_group_publish', $row->arRes['is_enable'] == 'Y' ? GetMessage('YES') : GetMessage('NO'));
		$row->AddCheckField('is_enable');
		$row->AddCheckField('is_group_publish');
	}

	function GetArModuleParamsPosts($index, $iblock_id=false)
	{
		$arProps = VettichPostingOption::getProps();
		if(!$iblock_id)
			$iblock_id = VettichPostingOption::GetByID($index, 'iblock_id');
		$values = isset($arProps[$iblock_id]) ? $arProps[$iblock_id] : array('none' => 'none');
		$arPostParams = array(
			'TABS' => array(
				'VK_TAB' => array(
					'NAME' => GetMessage('VK_TAB_NAME'),
					'TITLE' => GetMessage('VK_TAB_TITLE')
				)
			),
			'PARAMS' => array(
				'vk_publish_date' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('POST_VK_PUBLISH_DATE'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'VALUE' => VettichPostingOption::GetByID($index, 'vk_publish_date'),
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1010,
					'HELP' => GetMessage('POST_VK_PUBLISH_DATE_HELP'),
				),
				'vk_photo' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('POST_VK_PHOTO'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'VALUE' => VettichPostingOption::GetByID($index, 'vk_photo'),
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1011,
					'HELP' => GetMessage('POST_VK_PHOTO_HELP'),
				),
				'vk_photos' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('POST_VK_PHOTOS'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'VALUE' => VettichPostingOption::GetByID($index, 'vk_photos'),
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1020,
					'HELP' => GetMessage('POST_VK_PHOTOS_HELP'),
				),
				'vk_link' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('POST_VK_LINK'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'VALUE' => VettichPostingOption::GetByID($index, 'vk_link'),
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1030,
					'HELP' => GetMessage('POST_VK_LINK_HELP'),
				),
				'vk_message' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('POST_VK_MESSAGE'),
					'TYPE' => 'TEXTAREA',
					'VALUE' => VettichPostingOption::GetByID($index, 'vk_message'),
					'CHOISE' => 'SIMPLE',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'SORT' => 1050,
					'HELP' => GetMessage('POST_VK_MESSAGE_HELP'),
				),
			),
		);

		return $arPostParams;
	}

	function GetArModuleParams($index)
	{
		$arModuleParams = array(
			'TAB_CONTROL_POSTFIX' => 'vk',
			'FORM' => array(
				'PARAMS' => array(
					'ID' => $index,
				),
				'TYPE' => array('WITH_PARAMS', 'NOT_DEFAULT'),
			),
			'TABS' => array(
				'VK_TAB' => array(
					'NAME' => GetMessage('VK_TAB_NAME'),
					'TITLE' => GetMessage('VK_TAB_TITLE')
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
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('VK_ACCOUNTS_NAME'),
					'DESCRIPTION' => GetMessage('VK_ACCOUNTS_NAME_DESCRIPTION'),
					'VALUE' 		=> self::GetByID($index, 'name'),
					'TYPE' => 'STRING',
					'REQUIRED' => 'Y',
				),
				'is_enable' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('VK_IS_ENABLE'),
					'VALUE' => self::GetByID($index, 'is_enable'),
					'TYPE' => 'CHECKBOX',
				),
				'is_group_publish' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('IS_GROUP_PUBLISH'),
					'VALUE' => self::GetByID($index, 'is_group_publish'),
					'TYPE' => 'CHECKBOX',
				),
				'group_publish_id' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('GROUP_PUBLISH_ID'),
					'VALUE' => self::GetByID($index, 'group_publish_id'),
					'TYPE' => 'STRING',
				),
				'group_publish' => array(
					'TAB' => 'VK_TAB',
					'NAME' => '',
					'TYPE' => 'RADIO',
					'DESCRIPTION' => GetMessage('GROUP_PUBLISH_ID_DESCRIPTION'),
					'VALUE' => self::GetByID($index, 'group_publish'),
					'VALUES' => array(
						'Y' => GetMessage('GROUP_PUBLISH_GROUP'),
						'N' => GetMessage('GROUP_PUBLISH_USER'),
					)
				),
				'group_id_std' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('VK_ACCOUNTS_GROUP_ID_STD'),
					'VALUE' => self::GetByID($index, 'group_id_std'),
					'TYPE' => 'CHECKBOX',
				),
				'group_id' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('VK_ACCOUNTS_GROUP_ID'),
					'PLACEHOLDER' => GetMessage('VK_ACCOUNTS_GROUP_ID_PLACEHOLDER'),
					'DESCRIPTION' => GetMessage('VK_ACCOUNTS_GROUP_ID_DESCRIPTION'),
					'VALUE' => self::GetByID($index, 'group_id'),
					'TYPE' => 'STRING',
				),
				'access_token' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('VK_ACCOUNTS_ACCESS_TOKEN'),
					'VALUE' => self::GetByID($index, 'access_token'),
					'TYPE' => 'STRING',
				),
				'access_token_button' => array(
					'TAB' => 'VK_TAB',
					'NAME' => '',
					'DESCRIPTION' => GetMessage('VK_ACCOUNTS_ACCESS_TOKEN_DESCRIPTION'),
					'TYPE' => 'CUSTOM',
					'HTML' => GetMessage('VK_access_token_button_HTML').
						"<script>ALERT_NOT_GROUP_ID='".GetMessage('ALERT_NOT_GROUP_ID')."';".
						"ALERT_NOT_ACCESS_TOKEN='".GetMessage('ALERT_NOT_ACCESS_TOKEN')."'</script>",
				),
			)
		);

		$hlp = VettichPostingFunc::vettich_service('get_url', 'url=autoposting.vk.video_help');
		if(!empty($hlp['url']))
		{
			$arModuleParams['TABS']['VK_TAB_VIDEO'] = array(
				'NAME' => GetMessage('VK_TAB_VIDEO_NAME'),
				'TITLE' => GetMessage('VK_TAB_VIDEO_TITLE')
			);
			$arModuleParams['PARAMS']['video_help'] = array(
				'TAB' => 'VK_TAB_VIDEO',
				'TYPE' => 'NOTE',
				'TEXT' => VettichPostingFunc::get_youtube_frame($hlp['url']),
			);
		}

		global $arIncludeJS;
		$arIncludeJS[] = '/bitrix/js/vettich.autoposting/vk_options.js';

		return $arModuleParams;
	}

	function SaveParams($index=0, $arParams=false)
	{
		if($arParams===false)
		{
			$arParams = self::GetArModuleParams($index);
			$arParams = $arParams['PARAMS'];
		}

		VettichPostingOption::SaveParams($index, $arParams, 'vk_accounts');
	}

	function GetIDs()
	{
		return VettichPostingOption::GetIDs('vk_accounts');
	}

	function GetByID($index, $param_name=false)
	{
		return VettichPostingOption::GetByID($index, $param_name, 'vk_accounts');
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
				$arResult[$i][$k] = CVDB::get('vk_accounts['.$i.']['.$k.']', '');
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
		VettichPostingOption::Save($id, $arFields, 'vk_accounts');
	}

	function SaveFields($fields)
	{
		VettichPostingOption::SaveFields($fields, 'vk_accounts');
	}

	function Delete($id)
	{
		VettichPostingOption::Delete($id, 'vk_accounts');
	}
}