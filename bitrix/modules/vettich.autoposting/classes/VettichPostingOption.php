<?
IncludeModuleLangFile(__FILE__);

class VettichPostingOption
{
	static public $arModuleParams = false;
	static public $arIBlockIDs = false;
	static public $arProps = false;
	static public $arIBlockTypes = false;
	static public $arIBlocks = false;

	function PageTitle()
	{
		return GetMessage('VCH_POSTS_PAGE_TITLE');
	}

	function EditPageTitle($id=1)
	{
		if($id > 0)
			return GetMessage('VCH_POSTS_EDIT_PAGE_TITLE');
		return GetMessage('VCH_POSTS_ADD_PAGE_TITLE');
	}

	function GetFields()
	{
		return array(
			'id' => 'ID',
			'name' => GetMessage('POST_NAME'),
			'is_enable' => GetMessage('POST_IS_ENABLE'),
			'site_id' => GetMessage('POST_SITE_ID'),
			'iblock_type' => GetMessage('POST_IBLOCK_TYPE'),
			'iblock_id' => GetMessage('POST_IBLOCK_ID'),
		);
	}

	static function ChangeRow(&$row, $values)
	{
		if(empty($row))
			return;
		
		$row->AddInputField("name", array("size"=>20));

		$row->AddViewField('is_enable', $values['is_enable'] == 'Y' ? GetMessage('YES') : GetMessage('NO'));
		$row->AddCheckField('is_enable');
	}

	function getIBlockTypes()
	{
		if(self::$arIBlockTypes !== false)
			return self::$arIBlockTypes;

		self::$arIBlockTypes = array('none' => 'none');
		$rsIBlockType = CIBlockType::GetList();
		while ($arIBlockType = $rsIBlockType->GetNext())
		{
			if($arIBType = CIBlockType::GetByIDLang($arIBlockType["ID"], LANG))
			{
				self::$arIBlockTypes[$arIBlockType['ID']] = '['. $arIBlockType['ID'] .'] '. $arIBType['NAME'];
			}
		}

		return self::$arIBlockTypes;
	}

	function getIBlocks()
	{
		if(self::$arIBlocks !== false)
			return self::$arIBlocks;

		CModule::IncludeModule('iblock');

		self::$arIBlocks = array('none' => 'none');
		self::$arProps = array('none' => 'none');
		$rsIBlocks = CIBlock::GetList(array(), array('ACTIVE'=>'Y'), false);
		self::$arIBlockIDs = array();
		while($arIBlock = $rsIBlocks->GetNext())
		{
			self::$arIBlockIDs[] = $arIBlock['ID'];
			self::$arIBlocks[$arIBlock['IBLOCK_TYPE_ID']][$arIBlock['ID']] = '['. $arIBlock['ID'] .'] '. $arIBlock['NAME'];

			self::$arProps[$arIBlock['ID']] = self::getIBlockProps($arIBlock['ID']);
		}
		return self::$arIBlocks;
	}

	function getIBlockIDs()
	{
		if(self::$arIBlockIDs === false)
			getIBlocks();

		return self::$arIBlockIDs;
	}

	function getProps()
	{
		if(self::$arProps === false)
			getIBlocks();

		return self::$arProps;
	}

	function getIBlockProps($IBLOCK_ID)
	{
		$arProps = array(
			'none' => 				'none',
			'ID' => 				GetMessage('PROP_ID'),
			'CODE' => 				GetMessage('PROP_CODE'),
			'XML_ID' => 			GetMessage('PROP_XML_ID'),
			'NAME' => 				GetMessage('PROP_NAME'),
			'IBLOCK_ID' => 			GetMessage('PROP_IBLOCK_ID'),
			'IBLOCK_SECTION_ID' => 	GetMessage('PROP_IBLOCK_SECTION_ID'),
			'IBLOCK_CODE' => 		GetMessage('PROP_IBLOCK_CODE'),
			'ACTIVE' => 			GetMessage('PROP_ACTIVE'),
			'DATE_ACTIVE_FROM' => 	GetMessage('PROP_DATE_ACTIVE_FROM'),
			'DATE_ACTIVE_TO' => 	GetMessage('PROP_DATE_ACTIVE_TO'),
			'SORT' => 				GetMessage('PROP_SORT'),
			'PREVIEW_PICTURE' => 	GetMessage('PROP_PREVIEW_PICTURE'),
			'PREVIEW_TEXT' => 		GetMessage('PROP_PREVIEW_TEXT'),
			'DETAIL_PICTURE' => 	GetMessage('PROP_DETAIL_PICTURE'),
			'DETAIL_TEXT' => 		GetMessage('PROP_DETAIL_TEXT'),
			'DATE_CREATE' => 		GetMessage('PROP_DATE_CREATE'),
			'CREATED_BY' => 		GetMessage('PROP_CREATED_BY'),
			'CREATED_USER_NAME' => 	GetMessage('PROP_CREATED_USER_NAME'),
			'TIMESTAMP_X' => 		GetMessage('PROP_TIMESTAMP_X'),
			'MODIFIED_BY' => 		GetMessage('PROP_MODIFIED_BY'),
			'USER_NAME' => 			GetMessage('PROP_USER_NAME'),
			'LIST_PAGE_URL' => 		GetMessage('PROP_LIST_PAGE_URL'),
			'DETAIL_PAGE_URL' => 	GetMessage('PROP_DETAIL_PAGE_URL'),
			'SHOW_COUNTER' => 		GetMessage('PROP_SHOW_COUNTER'),
			'SHOW_COUNTER_START' => GetMessage('PROP_SHOW_COUNTER_START'),
			'WF_COMMENTS' => 		GetMessage('PROP_WF_COMMENTS'),
			'WF_STATUS_ID' => 		GetMessage('PROP_WF_STATUS_ID'),
			'TAGS' => 				GetMessage('PROP_TAGS'),
		);
		$rsProperties = CIBlockProperty::GetList(Array(), Array('ACTIVE'=>'Y', 'IBLOCK_ID'=>$IBLOCK_ID));
		while ($prop_fields = $rsProperties->GetNext())
		{
			$str = $prop_fields['NAME']. ' [PROPERTY_'. $prop_fields['CODE']. ']';
			$str = str_replace("'", '"', $str);
			$str = str_replace(array("\"", '&quot;', '&#34;'), "'", $str);
			$arProps['PROPERTY_'.$prop_fields['CODE']] = $str;
		}
		return $arProps;
	}

	function GetList($sort=array('id'=>'asc'), $arFields=false)
	{
		if($arFields===false)
			$arFields = self::GetFields();

		if(!CModule::IncludeModule('iblock'))
			return array();

		$arResult = array();
		foreach(self::GetIDs() as $i)
		{
			$arr = array();
			$is_empty_arr = true;
			foreach($arFields as $k=>$v)
			{
				// $arr[$k] = VOptions::getGroup('posts', $k, $i, self::module_id());
				$arr[$k] = CVDB::get('posts['.$i.']['.$k.']', '');
				if($arr[$k] != '')
					$is_empty_arr = false;
			}
			if($is_empty_arr)
				continue;
			$arResult[$i] = $arr;
			if(array_key_exists('id', $arFields))
			{
				$arResult[$i]['id'] = $i;
			}
			if(array_key_exists('iblock_type', $arFields) && !empty($arResult[$i]['iblock_type']))
			{
				$b = CIBlockType::GetByIDLang($arResult[$i]['iblock_type'], LANG);
				$arResult[$i]['iblock_type'] = '['.$arResult[$i]['iblock_type'].'] '.$b['NAME'];
			}
			if(array_key_exists('iblock_id', $arFields) && !empty($arResult[$i]['iblock_id']))
			{
				$b = CIBLock::GetByID($arResult[$i]['iblock_id'])->Fetch();
				$arResult[$i]['iblock_id'] = '['.$arResult[$i]['iblock_id'].'] '.$b['NAME'];
			}
			// if(array_key_exists('is_enable', $arFields))
			// {
			// 	$arResult[$i]['is_enable'] = $arResult[$i]['is_enable'] == 'Y' ? GetMessage('YES') : GetMessage('NO');
			// }
		}
		VettichPostingFunc::_usort($arResult, $sort);
		return $arResult;
	}

	function GetIDs($prefix='posts')
	{
		$count = CVDB::get($prefix, 0);
		$arResult = array();
		for($i=1; $i<=(int)$count; $i++)
		{
			$n = CVDB::get($prefix.'['.$i.'][name]', null);
			if($n != null)
				$arResult[] = $i;
		}
		return $arResult;
	}

	function GetByID($index, $param_name=false, $prefix='posts')
	{
		if(/*$index<=0 or */$param_name === false)
			return '';

		return CVDB::get($prefix.'['.$index.']['.$param_name.']', '');
	}

	function GetArModuleParams($index=0, $refresh=false)
	{
		if(!$refresh && self::$arModuleParams !== false)
			return self::$arModuleParams;

		$arSites = array();
		$rsSites = CSite::GetList($by="sort", $order="desc", Array());
		while($arSite = $rsSites->GetNext())
		{
			$arSites[$arSite['LID']] = '['.$arSite['LID'].'] '.$arSite['SITE_NAME'];
		}

		CModule::IncludeModule('iblock');

		$arIBlocksType = self::getIBlockTypes();

		$arIBlocks = self::getIBlocks();
		$arProps = self::getProps();
		$arIblocksID = self::getIBlockIDs();

		$defaultIBT = self::GetByID($index, 'iblock_type');
		$defaultIBID = self::GetByID($index, 'iblock_id');

		$id = $index;
		// if($id <= 0)
		// 	$id = 1 + IntVal(VOptions::get('posts', 0, self::module_id()));
		$arModuleParams = array(
			'FORM' => array(
				'PARAMS' => array(
					'ID' => $id,
				),
				'TYPE' => array('WITH_PARAMS', 'NOT_DEFAULT'),
			),
			'TABS' => array(
				'TAB1' => array(
					'NAME' => GetMessage('POSTS'),
					'TITLE' => GetMessage('POSTS_SETTINGS'),
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
					'TAB' => 'TAB1',
					'NAME' => GetMessage('POST_NAME'),
					'DEFAULT' => self::GetByID($index, 'name'),
					'TYPE' => 'STRING',
					'REQUIRED' => 'Y',
					'SORT' => 100,
				),
				'is_enable' => array(
					'TAB' => 'TAB1',
					'NAME' => GetMessage('POST_IS_ENABLE'),
					'TYPE' => 'CHECKBOX',
					'VALUE' => self::GetByID($index, 'is_enable'),
					'SORT' => 101,
				),
				'site_id' => array(
					'TAB' => 'TAB1',
					'NAME' => GetMessage('POST_SITE_ID'),
					'DEFAULT' => self::GetByID($index, 'site_id'),
					'TYPE' => 'LIST',
					'VALUES' => $arSites,
					'SORT' => 105,
				),
				'domain_name' => array(
					'TAB' => 'TAB1',
					'TYPE' => 'STRING',
					'NAME' => GetMessage('POSTS_DOMAIN_NAME'),
					'PLACEHOLDER' => GetMessage('POSTS_DOMAIN_NAME_PLACEHOLDER'),
					'VALUE' => self::GetByID($index, 'domain_name'),
					'SORT' => 107,
					'HELP' => GetMessage('POSTS_DOMAIN_NAME_HELP'),
				),
				'iblock_type' => array(
					'TAB' => 'TAB1',
					'NAME' => GetMessage('POST_IBLOCK_TYPE'),
					'DEFAULT' => $defaultIBT,
					'TYPE' => 'LIST',
					'VALUES' => $arIBlocksType,
					'SORT' => 110,
				),
				'iblock_id' => array(
					'TAB' => 'TAB1',
					'NAME' => GetMessage('POST_IBLOCK_ID'),
					'TYPE' => 'LIST',
					'BIND' => 'iblock_type',
					'BIND_VALUES' => $arIBlocks,
					'VALUES' => isset($arIBlocks[$defaultIBT]) ? $arIBlocks[$defaultIBT] : array('none' => 'none'),
					'DEFAULT' => self::GetByID($index, 'iblock_id'),
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 120,
					'HELP' => GetMessage('POST_IBLOCK_ID_HELP'),
				),
				// 'cmp' => array(
				// 	'TAB' => 'TAB1',
				// 	'NAME' => 'Uslovija',
				// 	'TYPE' => 'GROUP',
				// 	'SORT' => 130,
				// 	'VALUES' => array(
				// 	),
				// ),
						'field_1' => array(
							'TAB' => 'TAB1',
							'NAME' => GetMessage('POST_FIELD_1'),
							'TYPE' => 'LIST',
							'BIND' => 'iblock_id',
							'BIND_VALUES' => $arProps,
							'VALUES' => isset($arProps[$defaultIBID]) ? $arProps[$defaultIBID] : array('none' => 'none'),
							'DEFAULT' => self::GetByID($index, 'field_1'),
							'MULTIPLE' => 'N',
							'SIZE' => 0,
							'SORT' => 130,
							'DISPLAY' => 'inline',
							'HELP' => GetMessage('POST_FIELD_1_HELP')
						),
						'field_cmp' => array(
							'TAB' => 'TAB1',
							'TYPE' => 'LIST',
							'VALUES' => array('1'=>GetMessage('POST_FIELD_CMP_1'), '2'=>GetMessage('POST_FIELD_CMP_2'), '3'=>GetMessage('POST_FIELD_CMP_3')),
							'DEFAULT' => self::GetByID($index, 'field_cmp'),
							'MULTIPLE' => 'N',
							'SIZE' => 0,
							'SORT' => 140,
							'DISPLAY' => 'inline',
						),
						'field_2' => array(
							'TAB' => 'TAB1',
							'TYPE' => 'string',
							'SIZE' => 6,
							'DEFAULT' => self::GetByID($index, 'field_2'),
							'SORT' => 150,
							'DISPLAY' => 'inline',
						),
				'protocol' => array(
					'TAB' => 'TAB1',
					'TYPE' => 'LIST',
					'NAME' => GetMessage('POSTS_PROTOCOL'),
					'DEFAULT' => self::GetByID($index, 'protocol'),
					'SORT' => 160,
					'VALUES' => array(
						'' => GetMessage('POSTS_PROTOCOL_DEFAULT'),
						'http' => 'HTTP',
						'https' => 'HTTPS'
					),
					'HELP' => GetMessage('POSTS_PROTOCOL_HELP'),
				),
			),
		);

		$posts = VettichPostingFunc::__GetPosts();
		$_sort = 500;
		$arModuleParams['PARAMS']['account_note2'] = array(
			'TAB' => 'TAB1',
			'TEXT' => GetMessage('ACCOUNT_NOTE2_TEXT'),
			'TYPE' => 'NOTE',
			'SORT' => $_sort++,
		);
		foreach($posts as $post)
		{
			if(VettichPostingFunc::IncludeModule($post))
			{
				$arPost = VettichPostingFunc::module($post);
				if(method_exists($arPost['option'], "GetArModuleParamsPosts"))
					$arModuleParams = array_merge_recursive($arModuleParams, $arPost['option']::GetArModuleParamsPosts($index));
				if(method_exists($arPost['option'], "get_list") && method_exists($arPost['func'], "get_name"))
				{
					$_vals = $arPost['option']::get_list();
					$_html = '<table width="100%"><tr><td width="30%" align="right"><b>'.$arPost['func']::get_name().':</b></td><td width="70%"><table>';
					if(empty($_vals))
					{
						$_html .= '<tr><td>'.GetMessage('VCH_ACC_EMPTY', array('#ADD_URL#'=>'vettich_autoposting_posts_edit_'.$post.'.php?lang='.LANG, '#ACC_NAME#'=>$arPost['func']::get_name())).'<td><tr>';
					}
					else
					{
						$_def_vals = unserialize(self::GetByID($index, 'account_'.$post));
						foreach($_vals as $k=>$v)
						{
							$checked = '';
							if(in_array($k, $_def_vals))
								$checked = 'checked="checked"';
							$_html .= '<tr><td><input type="checkbox" '.$checked.' value="'.$k.'" name="account_'.$post.'[]" id="account_'.$post.$k.'"> <label for="account_'.$post.$k.'">'.$v.'</label></td></tr>';
						}
					}
					$_html .= '</table></td></tr></table>';
					$arModuleParams['PARAMS']['account_'.$post] = array(
						'TAB' => 'TAB1',
						'TEXT' => $_html,
						'TYPE' => 'NOTE',
						'SORT' => $_sort++,
					);
				}
			}
		}

		$hlp = VettichPostingFunc::vettich_service('get_url', 'url=autoposting.video_help');
		if(!empty($hlp['url']))
		{
			$arModuleParams['TABS']['GENERAL_TAB_VIDEO'] = array(
				'NAME' => GetMessage('GENERAL_TAB_VIDEO_NAME'),
				'TITLE' => GetMessage('GENERAL_TAB_VIDEO_TITLE')
			);
			$arModuleParams['PARAMS']['video_help'] = array(
				'TAB' => 'GENERAL_TAB_VIDEO',
				'TYPE' => 'NOTE',
				'TEXT' => VettichPostingFunc::get_youtube_frame($hlp['url']),
			);
		}

		return $arModuleParams;
	}

	function GetArModuleParamsPopup($iblock_id, $elem_ids)
	{
		$arSites = array();
		$rsSites = CSite::GetList($by="sort", $order="desc", Array());
		while($arSite = $rsSites->GetNext())
		{
			$arSites[$arSite['LID']] = '['.$arSite['LID'].'] '.$arSite['SITE_NAME'];
		}

		CModule::IncludeModule('iblock');

		$rsIBlocks = CIBlock::GetByID($iblock_id);
		$arIBlock = $rsIBlocks->GetNext();
		self::$arProps[$iblock_id] = self::getIBlockProps($iblock_id);

		$index = 'popup';
		CVDB::set('posts['.$index.'][iblock_id]', $iblock_id);
		$arModuleParams = array(
			'FORM' => array(
				'URL' => '',
				'TYPE' => array('NOT_DEFAULT'),
			),
			'TABS' => array(
				'TAB1' => array(
					'NAME' => GetMessage('POSTS'),
					'TITLE' => GetMessage('VCH_POST_IBLOCK_SINGLE_TITLE'),
				)
			),
			'BUTTONS' => array(
				'SAVE' => array(
					'NAME' => GetMessage('VCH_POST_IBLOCK_BUTTON_SEND'),
				),
				'APPLY' => array(
					'NAME' => GetMessage('VCH_POST_IBLOCK_BUTTON_CANCEL'),
				),
				'RESTORE_DEFAULTS' => array(
					'ENABLE' => 'N',
				)
			),
			'PARAMS' => array(
				'is_enable' => array(
					'TAB' => 'TAB1',
					'TYPE' => 'HIDDEN',
					'VALUE' => 'Y',
					'SORT' => 101,
				),
				'site_id' => array(
					'TAB' => 'TAB1',
					'NAME' => GetMessage('POST_SITE_ID'),
					'DEFAULT' => self::GetByID($index, 'site_id'),
					'TYPE' => 'LIST',
					'VALUES' => $arSites,
					'SORT' => 105,
				),
				'domain_name' => array(
					'TAB' => 'TAB1',
					'TYPE' => 'STRING',
					'NAME' => GetMessage('POSTS_DOMAIN_NAME'),
					'PLACEHOLDER' => GetMessage('POSTS_DOMAIN_NAME_PLACEHOLDER'),
					'VALUE' => self::GetByID($index, 'domain_name'),
					'SORT' => 107,
					'HELP' => GetMessage('POSTS_DOMAIN_NAME_HELP'),
				),
				'iblock_id' => array(
					'TAB' => 'TAB1',
					'NAME' => GetMessage('POST_IBLOCK_ID'),
					'TYPE' => 'LIST',
					'VALUES' => array('['.$arIBlock['ID'].'] '.$arIBlock['NAME']),
					'VALUE' => $iblock_id,
					'DISABLED' => 'Y',
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 120,
				),
				'protocol' => array(
					'TAB' => 'TAB1',
					'TYPE' => 'LIST',
					'NAME' => GetMessage('POSTS_PROTOCOL'),
					'VALUE' => self::GetByID($index, 'protocol'),
					'SORT' => 160,
					'VALUES' => array(
						'' => GetMessage('POSTS_PROTOCOL_DEFAULT'),
						'http' => 'HTTP',
						'https' => 'HTTPS'
					),
				),
			),
		);

		$posts = VettichPostingFunc::__GetPosts();
		$_sort = 500;
		$arModuleParams['PARAMS']['account_note2'] = array(
			'TAB' => 'TAB1',
			'TEXT' => GetMessage('ACCOUNT_NOTE2_TEXT'),
			'TYPE' => 'NOTE',
			'SORT' => $_sort++,
		);
		foreach($posts as $post)
		{
			if(VettichPostingFunc::IncludeModule($post))
			{
				$arPost = VettichPostingFunc::module($post);
				if(method_exists($arPost['option'], "GetArModuleParamsPosts"))
					$arModuleParams = array_merge_recursive($arModuleParams, $arPost['option']::GetArModuleParamsPosts($index));
				if(method_exists($arPost['option'], "get_list") && method_exists($arPost['func'], "get_name"))
				{
					$_vals = $arPost['option']::get_list();
					$_html = '<table width="100%"><tr><td width="30%" align="right"><b>'.$arPost['func']::get_name().':</b></td><td width="70%"><table>';
					if(empty($_vals))
					{
						$_html .= '<tr><td>'.GetMessage('VCH_ACC_EMPTY', array('#ADD_URL#'=>'vettich_autoposting_posts_edit_'.$post.'.php?lang='.LANG, '#ACC_NAME#'=>$arPost['func']::get_name())).'<td><tr>';
					}
					else
					{
						$_def_vals = unserialize(self::GetByID($index, 'account_'.$post));
						foreach($_vals as $k=>$v)
						{
							$checked = '';
							if(in_array($k, $_def_vals))
								$checked = 'checked="checked"';
							$_html .= '<tr><td><input type="checkbox" '.$checked.' value="'.$k.'" name="account_'.$post.'[]" id="account_'.$post.$k.'"> <label for="account_'.$post.$k.'">'.$v.'</label></td></tr>';
						}
					}
					$_html .= '</table></td></tr></table>';
					$arModuleParams['PARAMS']['account_'.$post] = array(
						'TAB' => 'TAB1',
						'TEXT' => $_html,
						'TYPE' => 'NOTE',
						'SORT' => $_sort++,
					);
				}
			}
		}

		return $arModuleParams;
	}

	function SaveParams($index=0, $arParams=false, $prefix='posts')
	{
		if(!isset($_POST['VOPTIONS_SUBMIT']))
			return;

		if($arParams===false)
		{
			$arParams = self::GetArModuleParams($index);
			$arParams = $arParams['PARAMS'];
		}

		if($index <= 0)
		{
			$index = CVDB::get($prefix, 0);
			CVDB::set($prefix, ++$index);
		}
		$arParamsKeys = VOptions::_json_decode(CVDB::get($prefix.'[params]', array()));
		foreach($arParams as $k=>$v)
			if(!in_array($k, $arParamsKeys))
				$arParamsKeys[] = $k;
		CVDB::set($prefix.'[params]', VOptions::_json_encode($arParamsKeys));

		foreach($arParams as $key=>$arParam)
		{
			$val = @$_POST[$key];
			if(strtoupper($arParam['TYPE']) == 'CHECKBOX' && $val != 'Y')
			{
				$val = 'N';
			}
			CVDB::set($prefix."[$index][$key]", $val);
		}

		if(isset($_POST['Save']) && trim($_POST['Save']) != '')
		{
			$url = 'vettich_autoposting_posts.php';
			if(isset($_GET['acc']) && $_GET['acc'] != '')
				$url = 'vettich_autoposting_posts_'.$_GET['acc'].'.php';
			$url .= '?lang='.LANG;
			LocalRedirect($url);
		}
		else
		{
			$url = 'vettich_autoposting_posts_edit.php';
			if(isset($_GET['acc']) && $_GET['acc'] != '')
				$url = 'vettich_autoposting_posts_edit_'.$_GET['acc'].'.php';
			$url .= '?ID='.$index.'&lang='.LANG;
			LocalRedirect($url);
		}
	}

	function Save($id, $arFields, $prefix='posts')
	{
		if($id <= 0)
			return;
		foreach($arFields as $k=>$v)
		{
			CVDB::set($prefix.'['.$id.']['.$k.']', $v);
		}
	}

	function SaveFields($fields, $prefix='posts')
	{
		foreach($fields as $id=>$arFields)
		{
			self::Save(IntVal($id), $arFields, $prefix);
		}
	}

	function Delete($id, $prefix='posts')
	{
		if($id <= 0)
			return;

		$params = VOptions::_json_decode(CVDB::get($prefix.'[params]', ''));
		foreach($params as $param)
		{
			CVDB::rem($prefix.'['.$id.']['.$param.']');
		}
	}
}