<?
IncludeModuleLangFile(__FILE__);

class VPostingInstOption
{
	function get_list()
	{
		$result = array();
		foreach(self::GetIDs() as $i)
		{
			$result[$i] = CVDB::get('instagram_accounts['.$i.'][name]');
		}
		return $result;
	}

	function get_default()
	{
		return array();
	}

	function PageTitle()
	{
		return GetMessage('INSTAGRAM_PAGE_TITLE');
	}

	function EditPageTitle($id=1)
	{
		if($id > 0)
			return GetMessage('INSTAGRAM_EDIT_PAGE_TITLE');
		return GetMessage('INSTAGRAM_ADD_PAGE_TITLE');
	}

	function GetFields()
	{
		return array(
			'id' => 'ID',
			'name' => GetMessage('INSTAGRAM_ACCOUNTS_NAME'),
			'is_enable' => GetMessage('INSTAGRAM_IS_ENABLE'),
			'login' => GetMessage('INSTAGRAM_LOGIN'),
			'password' => GetMessage('INSTAGRAM_PASSWORD'),
		);
	}

	static function ChangeRow(&$row, $values)
	{
		if(empty($row))
			return;
		
		$row->AddInputField("name", array("size"=>20));
		$row->AddInputField("login", array("size"=>20));
		$row->AddEditField("password", '<input type="password" value="'.$row->arRes['password'].'" name="FIELDS['.$row->arRes['id'].'][password]">');

		$row->AddViewField('is_enable', $row->arRes['is_enable'] == 'Y' ? GetMessage('YES') : GetMessage('NO'));
		$row->AddViewField('password', str_repeat('*', strlen($row->arRes['password'])));
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
				'INSTAGRAM_TAB' => array(
					'NAME' => GetMessage('INSTAGRAM_TAB_NAME'),
					'TITLE' => GetMessage('INSTAGRAM_TAB_TITLE')
				)
			),
			'PARAMS' => array(
				'instagram_photo' => array(
					'TAB' => 'INSTAGRAM_TAB',
					'NAME' => GetMessage('POST_INSTAGRAM_PHOTO'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'VALUE' => VettichPostingOption::GetByID($index, 'instagram_photo'),
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1011,
					'HELP' => GetMessage('POST_INSTAGRAM_PHOTO_HELP'),
				),
				'instagram_photo_other' => array(
					'TAB' => 'INSTAGRAM_TAB',
					'NAME' => GetMessage('POST_INSTAGRAM_PHOTO_OTHER'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'VALUE' => VettichPostingOption::GetByID($index, 'instagram_photo_other'),
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1020,
					'HELP' => GetMessage('POST_INSTAGRAM_PHOTO_OTHER_HELP'),
				),
				'instagram_message' => array(
					'TAB' => 'INSTAGRAM_TAB',
					'NAME' => GetMessage('POST_INSTAGRAM_MESSAGE'),
					'TYPE' => 'TEXTAREA',
					'VALUE' => VettichPostingOption::GetByID($index, 'instagram_message'),
					'CHOISE' => 'SIMPLE',
					'BIND' => 'iblock_id',
					'BIND_VALUES' => $arProps,
					'VALUES' => $values,
					'SORT' => 1050,
					'HELP' => GetMessage('POST_INSTAGRAM_MESSAGE_HELP'),
				),
			),
		);

		return $arPostParams;
	}

	function GetArModuleParams($index)
	{
		$arModuleParams = array(
			'TAB_CONTROL_POSTFIX' => 'instagram',
			'FORM' => array(
				'PARAMS' => array(
					'ID' => $index,
				),
				'TYPE' => array('WITH_PARAMS', 'NOT_DEFAULT'),
			),
			'TABS' => array(
				'INSTAGRAM_TAB' => array(
					'NAME' => GetMessage('INSTAGRAM_TAB_NAME'),
					'TITLE' => GetMessage('INSTAGRAM_TAB_TITLE')
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
					'TAB' => 'INSTAGRAM_TAB',
					'NAME' => GetMessage('INSTAGRAM_ACCOUNTS_NAME'),
					'DESCRIPTION' => GetMessage('INSTAGRAM_ACCOUNTS_NAME_DESCRIPTION'),
					'VALUE' 		=> self::GetByID($index, 'name'),
					'TYPE' => 'STRING',
					'REQUIRED' => 'Y',
				),
				'is_enable' => array(
					'TAB' => 'INSTAGRAM_TAB',
					'NAME' => GetMessage('INSTAGRAM_IS_ENABLE'),
					'VALUE' => self::GetByID($index, 'is_enable'),
					'TYPE' => 'CHECKBOX',
				),
				'login' => array(
					'TAB' => 'INSTAGRAM_TAB',
					'NAME' => GetMessage('INSTAGRAM_LOGIN'),
					'VALUE' => self::GetByID($index, 'login'),
					'TYPE' => 'STRING',
				),
				'password' => array(
					'TAB' => 'INSTAGRAM_TAB',
					'NAME' => GetMessage('INSTAGRAM_PASSWORD'),
					'DESCRIPTION' => GetMessage('INSTAGRAM_PASSWORD_DESCRIPTION'),
					'VALUE' => self::GetByID($index, 'password'),
					'TYPE' => 'PASSWORD',
				),
			)
		);

		$hlp = VettichPostingFunc::vettich_service('get_url', 'url=autoposting.instagram.video_help');
		if(!empty($hlp['url']))
		{
			$arModuleParams['TABS']['INSTAGRAM_TAB_VIDEO'] = array(
				'NAME' => GetMessage('INSTAGRAM_TAB_VIDEO_NAME'),
				'TITLE' => GetMessage('INSTAGRAM_TAB_VIDEO_TITLE')
			);
			$arModuleParams['PARAMS']['video_help'] = array(
				'TAB' => 'INSTAGRAM_TAB_VIDEO',
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

		VettichPostingOption::SaveParams($index, $arParams, 'instagram_accounts');
	}

	function GetIDs()
	{
		return VettichPostingOption::GetIDs('instagram_accounts');
	}

	function GetByID($index, $param_name=false)
	{
		return VettichPostingOption::GetByID($index, $param_name, 'instagram_accounts');
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
				$arResult[$i][$k] = CVDB::get('instagram_accounts['.$i.']['.$k.']', '');
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
		VettichPostingOption::Save($id, $arFields, 'instagram_accounts');
	}

	function SaveFields($fields)
	{
		VettichPostingOption::SaveFields($fields, 'instagram_accounts');
	}

	function Delete($id)
	{
		VettichPostingOption::Delete($id, 'instagram_accounts');
	}
}