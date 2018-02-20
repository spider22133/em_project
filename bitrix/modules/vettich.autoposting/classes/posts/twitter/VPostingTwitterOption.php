<?
IncludeModuleLangFile(__FILE__);

class VPostingTwitterOption
{
	function get_list()
	{
		$result = array();
		foreach(self::GetIDs() as $i)
		{
			$result[$i] = CVDB::get('twitter_accounts['.$i.'][name]');
		}
		return $result;
	}

	function get_default()
	{
		return array();
	}

	function PageTitle()
	{
		return GetMessage('TWITTER_PAGE_TITLE');
	}

	function EditPageTitle($id=1)
	{
		if($id > 0)
			return GetMessage('TWITTER_EDIT_PAGE_TITLE');
		return GetMessage('TWITTER_ADD_PAGE_TITLE');
	}

	function GetFields()
	{
		return array(
			'id' => 'ID',
			'name' => GetMessage('TWITTER_ACCOUNTS_NAME'),
			'is_enable' => GetMessage('TWITTER_IS_ENABLE'),
			'api_key' => GetMessage('TWITTER_API_KEY'),
			'api_secret' => GetMessage('TWITTER_API_SECRET'),
			'access_token' => array('content'=>GetMessage('TWITTER_ACCESS_TOKEN'), 'default'=>false),
			'access_token_secret' => array(GetMessage('TWITTER_ACCESS_TOKEN_SECRET'), 'default'=>false),
		);
	}

	static function ChangeRow(&$row)
	{
		if(empty($row))
			return;
		
		$row->AddInputField("name", array("size"=>20));
		$row->AddInputField("api_key", array("size"=>18));
		$row->AddInputField("api_secret", array("size"=>18));
		$row->AddInputField("api_secret", array("size"=>18));
		$row->AddInputField("access_token", array("size"=>18));
		$row->AddInputField("access_token_secret", array("size"=>18));

		$row->AddViewField('is_enable', $row->arRes['is_enable'] == 'Y' ? GetMessage('YES') : GetMessage('NO'));
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
				'TWITTER_TAB' => array(
					'NAME' => GetMessage('TWITTER_TAB_NAME'),
					'TITLE' => GetMessage('TWITTER_TAB_TITLE')
				)
			),
			'PARAMS' => array(
				'twitter_photo' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('POST_TWITTER_PHOTO'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'VALUE' => VettichPostingOption::GetByID($index, 'twitter_photo'),
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1011,
					'HELP' => GetMessage('POST_TWITTER_PHOTO_HELP'),
				),
				'twitter_photos' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('POST_TWITTER_PHOTOS'),
					'DESCRIPTION' => GetMessage('POST_TWITTER_PHOTOS_DESCRIPTION'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'VALUE' => VettichPostingOption::GetByID($index, 'twitter_photos'),
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1020,
					'HELP' => GetMessage('POST_TWITTER_PHOTOS_HELP'),
				),
				'twitter_link' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('POST_TWITTER_LINK'),
					'DESCRIPTION' => GetMessage('POST_TWITTER_LINK_DESCRIPTION'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'VALUE' => VettichPostingOption::GetByID($index, 'twitter_link'),
					'SIZE' => 0,
					'SORT' => 1030,
					'HELP' => GetMessage('POST_TWITTER_LINK_HELP'),
				),
				'twitter_message_sep' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('POST_TWITTER_MESSAGE_SEP'),
					// 'DESCRIPTION' => GetMessage('POST_TWITTER_LINK_DESCRIPTION'),
					'TYPE' => 'CHECKBOX',
					'VALUE' => VettichPostingOption::GetByID($index, 'twitter_message_sep'),
					'SORT' => 1040,
					'HELP' => GetMessage('POST_TWITTER_MESSAGE_SEP_HELP'),
				),
				'twitter_message' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('POST_TWITTER_MESSAGE'),
					'DESCRIPTION' => GetMessage('POST_TWITTER_MESSAGE_DESCRIPTION'),
					'TYPE' => 'TEXTAREA',
					'VALUE' => VettichPostingOption::GetByID($index, 'twitter_message'),
					'CHOISE' => 'SIMPLE',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'SORT' => 1050,
					'HELP' => GetMessage('POST_TWITTER_MESSAGE_HELP'),
				),
			),
		);

		return $arPostParams;
	}

	function GetArModuleParams($index)
	{
		$arModuleParams = array(
			'TAB_CONTROL_POSTFIX' => 'twitter',
			'FORM' => array(
				'PARAMS' => array(
					'ID' => $index,
				),
				'TYPE' => array('WITH_PARAMS', 'NOT_DEFAULT'),
			),
			'TABS' => array(
				'TWITTER_TAB' => array(
					'NAME' => GetMessage('TWITTER_TAB_NAME'),
					'TITLE' => GetMessage('TWITTER_TAB_TITLE')
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
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('TWITTER_ACCOUNTS_NAME'),
					'DESCRIPTION' => GetMessage('TWITTER_ACCOUNTS_NAME_DESCRIPTION'),
					'VALUE' 		=> self::GetByID($index, 'name'),
					'TYPE' => 'STRING',
					'REQUIRED' => 'Y',
				),
				'is_enable' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('TWITTER_IS_ENABLE'),
					'VALUE' => self::GetByID($index, 'is_enable'),
					'TYPE' => 'CHECKBOX',
				),
				'api_key' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('TWITTER_API_KEY'),
					'VALUE' => self::GetByID($index, 'api_key'),
					'TYPE' => 'STRING',
				),
				'api_secret' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('TWITTER_API_SECRET'),
					'VALUE' => self::GetByID($index, 'api_secret'),
					'TYPE' => 'STRING',
				),
				'access_token' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('TWITTER_ACCESS_TOKEN'),
					'VALUE' => self::GetByID($index, 'access_token'),
					'TYPE' => 'STRING',
				),
				'access_token_secret' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('TWITTER_ACCESS_TOKEN_SECRET'),
					'DESCRIPTION' => GetMessage('TWITTER_ACCESS_TOKEN_SECRET_DESCRIPTION'),
					'VALUE' => self::GetByID($index, 'access_token_secret'),
					'TYPE' => 'STRING',
				),
			)
		);

		$hlp = VettichPostingFunc::vettich_service('get_url', 'url=autoposting.twitter.video_help');
		if(!empty($hlp['url']))
		{
			$arModuleParams['TABS']['TWITTER_TAB_VIDEO'] = array(
				'NAME' => GetMessage('TWITTER_TAB_VIDEO_NAME'),
				'TITLE' => GetMessage('TWITTER_TAB_VIDEO_TITLE')
			);
			$arModuleParams['PARAMS']['video_help'] = array(
				'TAB' => 'TWITTER_TAB_VIDEO',
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

		VettichPostingOption::SaveParams($index, $arParams, 'twitter_accounts');
	}

	function GetIDs()
	{
		return VettichPostingOption::GetIDs('twitter_accounts');
	}

	function GetByID($index, $param_name=false)
	{
		return VettichPostingOption::GetByID($index, $param_name, 'twitter_accounts');
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
				$arResult[$i][$k] = CVDB::get('twitter_accounts['.$i.']['.$k.']', '');
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
		VettichPostingOption::Save($id, $arFields, 'twitter_accounts');
	}

	function SaveFields($fields)
	{
		VettichPostingOption::SaveFields($fields, 'twitter_accounts');
	}

	function Delete($id)
	{
		VettichPostingOption::Delete($id, 'twitter_accounts');
	}
}