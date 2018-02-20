<?
namespace Vettich\Autoposting;
use \CModule;
use \CIBlock;
use \CIBlockType;
use \CIBlockProperty;
IncludeModuleLangFile(__FILE__);

class PostingOption
{
	static public $arParamsValue = array();
	static public $arModuleParams = false;
	static public $arIBlockIDs = false;
	static public $arProps = false;
	static public $arIBlockTypes = false;
	static public $arIBlocks = false;

	/**
	* return posts page title
	* @return string
	*/
	function PageTitle()
	{
		return GetMessage('VCH_POSTS_PAGE_TITLE');
	}

	/**
	* return edit posts page title, if id=-1 then new record
	* @param integer $id
	* @return string
	*/
	function EditPageTitle($id=1)
	{
		if($id > 0)
			return GetMessage('VCH_POSTS_EDIT_PAGE_TITLE');
		return GetMessage('VCH_POSTS_ADD_PAGE_TITLE');
	}

	/**
	* return fields name
	* @return array of string
	*/
	function GetFields()
	{
		return array(
			'ID' => 'ID',
			'NAME' => GetMessage('POST_NAME'),
			'IS_ENABLE' => GetMessage('POST_IS_ENABLE'),
			'SITE_ID' => GetMessage('POST_SITE_ID'),
			'IBLOCK_TYPE' => GetMessage('POST_IBLOCK_TYPE'),
			'IBLOCK_ID' => GetMessage('POST_IBLOCK_ID'),
		);
	}

	static function ChangeRow(&$row, $values=array())
	{
		if(empty($row))
			return;
		
		$row->AddInputField("NAME", array("size"=>20));

		$row->AddViewField('IS_ENABLE', $values['IS_ENABLE'] == 'Y' ? GetMessage('YES') : GetMessage('NO'));
		$row->AddCheckField('IS_ENABLE');
	}

	function getIBlockTypes()
	{
		if(self::$arIBlockTypes !== false)
			return self::$arIBlockTypes;

		self::$arIBlockTypes = array('' => GetMessage('vettich.autoposting_select_iblock_type'));
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

	static function isIBlockToType($iblock_type, $iblock_id)
	{
		if(empty($iblock_type) or empty($iblock_id))
			return false;
		$rs = CIBlock::GetByID($iblock_id);
		if(($ar = $rs->GetNext()) && $ar['IBLOCK_TYPE_ID'] == $iblock_type)
		{
			return true;
		}
		return false;
	}

	function getIBlocks($type_id=0, $id=0)
	{
		if(self::$arIBlocks !== false)
			return self::$arIBlocks;

		self::$arIBlocks = array('none' => array('' => GetMessage('vettich.autoposting_before_select_iblock_type')));
		self::$arProps = array('none' => array('' => GetMessage('vettich.autoposting_before_select_iblock_id')));
		if(!$type_id)
			return self::$arIBlocks;
		$arFilter['TYPE'] = $type_id;
		$arFilter = array('ACTIVE'=>'Y');
		$rsIBlocks = CIBlock::GetList(array(), $arFilter, false);
		while($arIBlock = $rsIBlocks->GetNext())
		{
			if(!isset(self::$arIBlocks[$arIBlock['IBLOCK_TYPE_ID']]))
				self::$arIBlocks[$arIBlock['IBLOCK_TYPE_ID']] = array('' => GetMessage('vettich.autoposting_select_iblock_id'));
			self::$arIBlocks[$arIBlock['IBLOCK_TYPE_ID']][$arIBlock['ID']] = '['. $arIBlock['ID'] .'] '. $arIBlock['NAME'];
			if($arIBlock['ID'] == $id)
				self::$arProps[$arIBlock['ID']] = self::getIBlockProps($arIBlock['ID']);
		}
		return self::$arIBlocks;
	}

	function getIBlockIDs()
	{
		if(self::$arIBlockIDs === false)
			self::getIBlocks();

		return self::$arIBlockIDs;
	}

	function getProps($iblock_id=0)
	{
		if(self::$arProps === false)
			self::getIBlocks(0, $iblock_id);
		elseif($iblock_id && !isset(self::$arProps[$iblock_id]))
			self::$arProps[$iblock_id] = self::getIBlockProps($iblock_id);

		return self::$arProps;
	}

	function getIBlockSections($iblock_id)
	{
		if(empty($iblock_id))
			return array();

		$arFilter = array('IBLOCK_ID' => $iblock_id, 'ACTIVE' => 'Y'); 
		$arSelect = array('ID', 'NAME', 'DEPTH_LEVEL');
		$rsSection = \CIBlockSection::GetTreeList($arFilter, $arSelect);
		$arSections = array('' => GetMessage('vettich.autoposting_section_select'));
		while($ar = $rsSection->Fetch())
			$arSections[$ar['ID']] = str_repeat('- ', $ar['DEPTH_LEVEL']).$ar['NAME'] .' ['.$ar['ID'].']';
		return $arSections;
	}

	function getIBlockProps($IBLOCK_ID)
	{
		$arProps = array(
			'none' => 				GetMessage('vettich.autoposting_select_iblock_property'),
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
		if(CModule::IncludeModule('catalog')
			&& \CCatalog::GetByID($IBLOCK_ID))
		{
			$arProps['CAT_QUANTITY'] = GetMessage('PROP_CAT_QUANTITY');
			$arProps['CAT_WEIGHT'] = GetMessage('PROP_CAT_WEIGHT');
			$arProps['CAT_WIDTH'] = GetMessage('PROP_CAT_WIDTH');
			$arProps['CAT_LENGTH'] = GetMessage('PROP_CAT_LENGTH');
			$arProps['CAT_HEIGHT'] = GetMessage('PROP_CAT_HEIGHT');

			$rs = \CCatalogGroup::GetList(array(), array(), false, false, array('ID', 'NAME_LANG'));
			while($ar = $rs->Fetch())
			{
				$arProps['CAT_PRICE_'.$ar['ID']] = GetMessage('PROP_CAT_PRICE', array(
					'#TYPE#' => $ar['NAME_LANG'],
					'#PRICE_ID#' => $ar['ID'],
				));
				$arProps['CAT_CURRENCY_'.$ar['ID']] = GetMessage('PROP_CAT_CURRENCY', array(
					'#TYPE#' => $ar['NAME_LANG'],
					'#PRICE_ID#' => $ar['ID'],
				));
			}

			$arProps['CAT_DISCOUNT_NAME'] = GetMessage('PROP_CAT_DISCOUNT_NAME');
			$arProps['CAT_DISCOUNT_ACTIVE_FROM'] = GetMessage('PROP_CAT_DISCOUNT_ACTIVE_FROM');
			$arProps['CAT_DISCOUNT_ACTIVE_TO'] = GetMessage('PROP_CAT_DISCOUNT_ACTIVE_TO');
		}
		return $arProps;
	}

	function GetList($sort=array('ID'), $arFields=false)
	{
		if($arFields===false)
			$arFields = array_keys(self::GetFields());

		if(!CModule::IncludeModule('iblock'))
			return array();

		$arResult = array();
		$rs = DBTable::GetList(array(
			'select'=>($arFields), 
			'order'=>$sort,
			'filter' => array('TYPE' => PostingFunc::DBTYPEPOSTS),
		));
		while($ar = $rs->Fetch())
		{
			if(!empty($ar['IBLOCK_TYPE']))
			{
				$b = CIBlockType::GetByIDLang($ar['IBLOCK_TYPE'], LANG);
				$ar['IBLOCK_TYPE'] = '['.$ar['IBLOCK_TYPE'].'] '.$b['NAME'];
			}
			if(!empty($ar['IBLOCK_ID']))
			{
				$b = CIBLock::GetByID($ar['IBLOCK_ID'])->Fetch();
				$ar['IBLOCK_ID'] = '['.$ar['IBLOCK_ID'].'] '.$b['NAME'];
			}
			$arResult[$ar['ID']] = $ar;
		}
		return $arResult;
	}

	function GetPopupID()
	{
		$db = PostingFunc::DBTABLE;
		$ar = $db::GetList(array(
			'select' => array('ID'),
			'filter' => array('TYPE' => PostingFunc::DBTYPEPOSTPOPUP),
			'limit' => 1
		))->Fetch();
		$ID = 0;
		if($ar)
			$ID = $ar['ID'];
		return $ID;
	}

	/**
	* @param int $index
	* @param string $param_name
	* @param string $dbtable
	* @global array $_POST
	* @return string|mixed
	*/
	function GetByID($index, $param_name=false, $dbtable = PostingFunc::DBTABLE)
	{
		if($param_name === false)
			return '';

		if(isset($_POST[$param_name]))
			return $_POST[$param_name];

		if($index < 0)
			return '';

		if(!isset(self::$arParamsValue[$index]))
		{
			$ar = $dbtable::GetRowById($index);
			if(is_array($ar))
				self::$arParamsValue[$ar['ID']] = $ar;
		}
		return self::$arParamsValue[$index][$param_name] ?: '';
	}

	function GetArModuleParams($ID=0, $refresh=false)
	{
		if(!$refresh && self::$arModuleParams !== false)
			return self::$arModuleParams;

		$arSites = array();
		$rsSites = \CSite::GetList($by='sort', $order='desc', Array());
		while($arSite = $rsSites->GetNext())
		{
			$arSites[$arSite['LID']] = '['.$arSite['LID'].'] '.$arSite['SITE_NAME'];
		}

		$defaultIBT = self::GetByID($ID, 'IBLOCK_TYPE');
		$defaultIBID = self::GetByID($ID, 'IBLOCK_ID');
		if(!self::isIBlockToType($defaultIBT, $defaultIBID))
			$defaultIBID = 0;

		CModule::IncludeModule('iblock');
		$arIBlocksType = self::getIBlockTypes();
		$arIBlocks = self::getIBlocks($defaultIBT, $defaultIBID);
		$arProps = self::getProps($defaultIBID);

		if($ID <= 0)
		{
			$ar = DBTable::GetList(array(
				'order' => array('ID' => 'DESC'),
				'select'=>array('ID'),
				'limit' => 1
			))->Fetch();
			$index = $ar['ID'];
		}
		$arModuleParams = array(
			'FORM' => array(
				'PARAMS' => array(
					'ID' => $ID,
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
				'TYPE' => array(
					'TAB' => 'TAB1',
					'TYPE' => 'HIDDEN',
					'VALUE' => PostingFunc::DBTYPEPOSTS,
					'SORT' => 100,
				),
				'NAME' => array(
					'TAB' => 'TAB1',
					'NAME' => GetMessage('POST_NAME'),
					'VALUE' => self::GetByID($ID, 'NAME') ?: 'Autoposting ['.PostingFunc::GetNextIdDB().']',
					'TYPE' => 'STRING',
					'REQUIRED' => 'Y',
					'SORT' => 100,
				),
				'IS_ENABLE' => array(
					'TAB' => 'TAB1',
					'NAME' => GetMessage('POST_IS_ENABLE'),
					'TYPE' => 'CHECKBOX',
					'VALUE' => self::GetByID($ID, 'IS_ENABLE') ?: 'Y',
					'SORT' => 101,
				),
				'MANUALLY' => array(
					'TAB' => 'TAB1',
					'NAME' => GetMessage('POST_MANUALLY'),
					'HELP' => GetMessage('POST_MANUALLY_HELP'),
					'TYPE' => 'CHECKBOX',
					'VALUE' => self::GetByID($ID, 'MANUALLY') ?: 'N',
					'SORT' => 102,
				),
				'SITE_ID' => array(
					'TAB' => 'TAB1',
					'NAME' => GetMessage('POST_SITE_ID'),
					'DEFAULT' => self::GetByID($ID, 'SITE_ID'),
					'TYPE' => 'LIST',
					'VALUES' => $arSites,
					'SORT' => 105,
				),
				'DOMAIN_NAME' => array(
					'TAB' => 'TAB1',
					'TYPE' => 'STRING',
					'NAME' => GetMessage('POSTS_DOMAIN_NAME'),
					'PLACEHOLDER' => GetMessage('POSTS_DOMAIN_NAME_PLACEHOLDER'),
					'VALUE' => self::GetByID($ID, 'DOMAIN_NAME'),
					'SORT' => 107,
					'HELP' => GetMessage('POSTS_DOMAIN_NAME_HELP'),
				),
				'IBLOCK_TYPE' => array(
					'TAB' => 'TAB1',
					'NAME' => GetMessage('POST_IBLOCK_TYPE'),
					'DEFAULT' => $defaultIBT,
					'TYPE' => 'LIST',
					'REFRESH' => 'Y',
					'VALUES' => $arIBlocksType,
					'SORT' => 110,
				),
				'IBLOCK_ID' => array(
					'TAB' => 'TAB1',
					'NAME' => GetMessage('POST_IBLOCK_ID'),
					'TYPE' => 'LIST',
					'VALUES' => $arIBlocks[$defaultIBT] ?: $arIBlocks['none'],
					'DEFAULT' => self::GetByID($ID, 'IBLOCK_ID'),
					'MULTIPLE' => 'N',
					'REFRESH' => 'Y',
					'SIZE' => 0,
					'SORT' => 120,
					'HELP' => GetMessage('POST_IBLOCK_ID_HELP'),
				),
				'IS_SECTION_ENABLED' => array(
					'TAB' => 'TAB1',
					'NAME' => GetMessage('POST_IS_SECTION_ENABLED'),
					'TYPE' => 'CHECKBOX',
					'VALUE' => self::GetByID($ID, 'IS_SECTION_ENABLED'),
					'MULTIPLE' => 'N',
					'REFRESH' => 'Y',
					'SIZE' => 0,
					'SORT' => 124,
					'HELP' => GetMessage('POST_IS_SECTION_ENABLED_HELP'),
				),
				'FIELD_CMP_GROUP' => array(
					'TAB' => 'TAB1',
					'TYPE' => 'GROUP',
					'NAME' => GetMessage('POST_FIELD_CMP_GROUP_TITLE'),
					'ADDBUTTON' => GetMessage('POST_FIELD_CMP_GROUP_ADD_BUTTON'),
					'VALUES' => array(
						'FIELD_1' => array(
							'TAB' => 'TAB1',
							'NAME' => GetMessage('POST_FIELD_1'),
							'TYPE' => 'LIST',
							'VALUES' => $arProps[$defaultIBID] ?: $arProps['none'],
							'DEFAULT' => self::GetByID($ID, 'FIELD_1'),
							'MULTIPLE' => 'N',
							'SIZE' => 0,
							'SORT' => 130,
							'DISPLAY' => 'inline',
							'HELP' => GetMessage('POST_FIELD_1_HELP')
						),
						'FIELD_CMP' => array(
							'TAB' => 'TAB1',
							'TYPE' => 'LIST',
							'VALUES' => array(
								PostingFunc::FIELD_CMP_EQUALLY => GetMessage('POST_FIELD_CMP_1'),
								PostingFunc::FIELD_CMP_MORE_OR_EQUALLY => GetMessage('POST_FIELD_CMP_2'),
								PostingFunc::FIELD_CMP_LESS_OR_EQUALLY => GetMessage('POST_FIELD_CMP_3'),
								PostingFunc::FIELD_CMP_CONTAINS => GetMessage('POST_FIELD_CMP_CONTAINS'),
								PostingFunc::FIELD_CMP_NOT_CONTAINS => GetMessage('POST_FIELD_CMP_NOT_CONTAINS'),
							),
							'DEFAULT' => self::GetByID($ID, 'FIELD_CMP'),
							'MULTIPLE' => 'N',
							'SIZE' => 0,
							'SORT' => 140,
							'DISPLAY' => 'inline',
						),
						'FIELD_2' => array(
							'TAB' => 'TAB1',
							'TYPE' => 'string',
							'SIZE' => 6,
							'DEFAULT' => self::GetByID($ID, 'FIELD_2'),
							'SORT' => 150,
							'DISPLAY' => 'inline',
						),
					),
					// 'VALUE' => array(
					// 	'count' => 2,
					// ),
					'VALUE' => self::GetByID($ID, 'FIELD_CMP_GROUP'),
				),
				'PROTOCOL' => array(
					'TAB' => 'TAB1',
					'TYPE' => 'LIST',
					'NAME' => GetMessage('POSTS_PROTOCOL'),
					'DEFAULT' => self::GetByID($ID, 'PROTOCOL'),
					'SORT' => 160,
					'VALUES' => array(
						'' => GetMessage('POSTS_PROTOCOL_DEFAULT'),
						'http' => 'HTTP',
						'https' => 'HTTPS'
					),
					'HELP' => GetMessage('POSTS_PROTOCOL_HELP'),
				),
				'IS_UTM_ENABLE' => array(
					'TAB' => 'TAB1',
					'TYPE' => 'CHECKBOX',
					'NAME' => GetMessage('POSTS_IS_UTM_ENABLE'),
					'DEFAULT' => self::GetByID($ID, 'IS_UTM_ENABLE'),
					'SORT' => 170,
					'HELP' => GetMessage('POSTS_IS_UTM_ENABLE_HELP'),
				),
			),
		);

		if(self::GetByID($ID, 'IS_SECTION_ENABLED') == 'Y')
		{
			$arSections = self::getIBlockSections($defaultIBID);
			$arModuleParams['PARAMS']['SECTIONS'] = array(
				'TAB' => 'TAB1',
				'NAME' => GetMessage('POST_SECTIONS'),
				'TYPE' => 'LIST',
				'VALUES' => $arSections ?: $arProps['none'],
				'VALUE' => self::GetByID($ID, 'SECTIONS'),
				'MULTIPLE' => 'Y',
				'REFRESH' => 'N',
				// 'SIZE' => 0,
				'SORT' => 125,
				'HELP' => GetMessage('POST_SECTIONS_HELP'),
			);
		}

		$posts = PostingFunc::__GetPosts();
		$_sort = 500;
		$_hiddenEmpty = 'Y' == \COption::GetOptionString(PostingFunc::module_id(), 'show_empty_acc', 'Y');
		$arModuleParams['PARAMS']['account_note2'] = array(
			'TAB' => 'TAB1',
			'TEXT' => GetMessage('ACCOUNT_NOTE2_TEXT'),
			'TYPE' => 'NOTE',
			'SORT' => $_sort++,
		);
		foreach($posts as $post)
		{
			if(PostingFunc::isModule($post))
			{
				$arPost = PostingFunc::module2($post);
				if(method_exists($arPost['option'], "GetArModuleParamsPosts")
					&& method_exists($arPost['option'], "get_list")
					&& method_exists($arPost['func'], "get_name"))
				{
					$ac_id = 'ACCOUNT_'.strtoupper($post);
					if(!PostingFunc::isHiddenPost($post)) {
						$arModuleParams = array_merge_recursive($arModuleParams, $arPost['option']::GetArModuleParamsPosts($ID));
					}
					$_vals = $arPost['option']::get_list();
					$_html = '<table width="100%"><tr><td width="30%" align="right"><b>'.$arPost['func']::get_name().':</b></td><td width="70%"><table>';
					if(empty($_vals))
					{
						if($_hiddenEmpty) {
							continue;
						}
						$_html .= '<tr><td>'.GetMessage('VCH_ACC_EMPTY', array('#ADD_URL#'=>'vettich_autoposting_posts_edit_'.$post.'.php?lang='.LANG, '#ACC_NAME#'=>$arPost['func']::get_name())).'<td><tr>';
					}
					else
					{
						$_def_vals = (self::GetByID($ID, $ac_id));
						foreach($_vals as $k=>$v)
						{
							$checked = '';
							if(is_array($_def_vals) && in_array($k, $_def_vals))
								$checked = 'checked="checked"';
							$_html .= '<tr><td><input type="checkbox" '.$checked.' value="'.$k.'" name="'.$ac_id.'[]" id="'.$ac_id.$k.'"> <label for="'.$ac_id.$k.'">'.$v.'</label></td></tr>';
						}
					}
					$_html .= '</table></td></tr></table>';
					$arModuleParams['PARAMS'][$ac_id] = array(
						'TAB' => 'TAB1',
						'TEXT' => $_html,
						'TYPE' => 'NOTE',
						'SORT' => $_sort++,
					);
				}
			}
		}

		PostingFunc::event('OnBuildPostsParams', array('arModuleParams'=>&$arModuleParams));

		$hlp = PostingFunc::vettich_service('get_url', 'url=autoposting.video_help');
		if(!empty($hlp['url']))
		{
			$arModuleParams['TABS']['GENERAL_TAB_VIDEO'] = array(
				'NAME' => GetMessage('GENERAL_TAB_VIDEO_NAME'),
				'TITLE' => GetMessage('GENERAL_TAB_VIDEO_TITLE')
			);
			$arModuleParams['PARAMS']['video_help'] = array(
				'TAB' => 'GENERAL_TAB_VIDEO',
				'TYPE' => 'NOTE',
				'TEXT' => PostingFunc::get_youtube_frame($hlp['url']),
			);
		}
// \VOptions::debugg($arModuleParams);
		return $arModuleParams;
	}

	function GetArModuleParamsPopup($iblock_id)
	{
		$arSites = array();
		$rsSites = \CSite::GetList($by="sort", $order="desc", Array());
		while($arSite = $rsSites->GetNext())
		{
			$arSites[$arSite['LID']] = '['.$arSite['LID'].'] '.$arSite['SITE_NAME'];
		}

		\CModule::IncludeModule('iblock');

		$rsIBlocks = \CIBlock::GetByID($iblock_id);
		$arIBlock = $rsIBlocks->GetNext();
		self::$arProps[$iblock_id] = self::getIBlockProps($iblock_id);

		$ID = self::GetPopupID();
		$_POST['IBLOCK_ID'] = $iblock_id;
		// CVDB::set('posts['.$ID.'][iblock_id]', $iblock_id);
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
				'TYPE' => array(
					'TAB' => 'TAB1',
					'TYPE' => 'HIDDEN',
					'VALUE' => PostingFunc::DBTYPEPOSTPOPUP,
					'SORT' => 100,
				),
				'IS_ENABLE' => array(
					'TAB' => 'TAB1',
					'TYPE' => 'HIDDEN',
					'VALUE' => 'Y',
					'SORT' => 101,
				),
				'SITE_ID' => array(
					'TAB' => 'TAB1',
					'NAME' => GetMessage('POST_SITE_ID'),
					'DEFAULT' => self::GetByID($ID, 'SITE_ID'),
					'TYPE' => 'LIST',
					'VALUES' => $arSites,
					'SORT' => 105,
				),
				'DOMAIN_NAME' => array(
					'TAB' => 'TAB1',
					'TYPE' => 'STRING',
					'NAME' => GetMessage('POSTS_DOMAIN_NAME'),
					'PLACEHOLDER' => GetMessage('POSTS_DOMAIN_NAME_PLACEHOLDER'),
					'VALUE' => self::GetByID($ID, 'DOMAIN_NAME'),
					'SORT' => 107,
					'HELP' => GetMessage('POSTS_DOMAIN_NAME_HELP'),
				),
				'IBLOCK_ID' => array(
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
				'PROTOCOL' => array(
					'TAB' => 'TAB1',
					'TYPE' => 'LIST',
					'NAME' => GetMessage('POSTS_PROTOCOL'),
					'VALUE' => self::GetByID($ID, 'PROTOCOL'),
					'SORT' => 160,
					'VALUES' => array(
						'' => GetMessage('POSTS_PROTOCOL_DEFAULT'),
						'http' => 'HTTP',
						'https' => 'HTTPS'
					),
				),
				'IS_UTM_ENABLE' => array(
					'TAB' => 'TAB1',
					'TYPE' => 'CHECKBOX',
					'NAME' => GetMessage('POSTS_IS_UTM_ENABLE'),
					'DEFAULT' => self::GetByID($ID, 'IS_UTM_ENABLE'),
					'SORT' => 170,
					'HELP' => GetMessage('POSTS_IS_UTM_ENABLE_HELP'),
				),
			),
		);

		$posts = PostingFunc::__GetPosts();
		$_sort = 500;
		$_hiddenEmpty = 'Y' == \COption::GetOptionString(PostingFunc::module_id(), 'show_empty_acc', 'Y');
		$arModuleParams['PARAMS']['account_note2'] = array(
			'TAB' => 'TAB1',
			'TEXT' => GetMessage('ACCOUNT_NOTE2_TEXT'),
			'TYPE' => 'NOTE',
			'SORT' => $_sort++,
		);
		foreach($posts as $post)
		{
			if(PostingFunc::isModule($post))
			{
				$arPost = PostingFunc::module2($post);
				if(method_exists($arPost['option'], "GetArModuleParamsPosts")
					&& method_exists($arPost['option'], "get_list")
					&& method_exists($arPost['func'], "get_name"))
				{
					$ac_id = 'ACCOUNT_'.strtoupper($post);
					if(!PostingFunc::isHiddenPost($post)) {
						$arModuleParams = array_merge_recursive($arModuleParams, $arPost['option']::GetArModuleParamsPosts($ID));
					}
					$_vals = $arPost['option']::get_list();
					$_html = '<table width="100%"><tr><td width="30%" align="right"><b>'.$arPost['func']::get_name().':</b></td><td width="70%"><table>';
					if(empty($_vals))
					{
						if($_hiddenEmpty) {
							continue;
						}
						$_html .= '<tr><td>'.GetMessage('VCH_ACC_EMPTY', array('#ADD_URL#'=>'vettich_autoposting_posts_edit_'.$post.'.php?lang='.LANG, '#ACC_NAME#'=>$arPost['func']::get_name())).'<td><tr>';
					}
					else
					{
						$_def_vals = (self::GetByID($ID, $ac_id));
						foreach($_vals as $k=>$v)
						{
							$checked = '';
							if(is_array($_def_vals) && in_array($k, $_def_vals))
								$checked = 'checked="checked"';
							$_html .= '<tr><td><input type="checkbox" '.$checked.' value="'.$k.'" name="'.$ac_id.'[]" id="'.$ac_id.$k.'"> <label for="'.$ac_id.$k.'">'.$v.'</label></td></tr>';
						}
					}
					$_html .= '</table></td></tr></table>';
					$arModuleParams['PARAMS'][$ac_id] = array(
						'TAB' => 'TAB1',
						'TEXT' => $_html,
						'TYPE' => 'NOTE',
						'SORT' => $_sort++,
					);
				}
			}
		}

		PostingFunc::event('OnBuildPostsParams', array('arModuleParams'=>&$arModuleParams));

		return $arModuleParams;
	}

	function SaveParams($ID=0, $dbtable = PostingFunc::DBTABLE, $is_redirect=true)
	{
		if(!isset($_POST['VOPTIONS_SUBMIT']) or $_POST['VOPTIONS_SUBMIT'] != 'Y')
			return;

		$arFields = PostingFunc::GetFieldsDBTableFromPost($dbtable);
		if($ID > 0)
		{
			$arFields['ID'] = $ID;
			$rs = $dbtable::update($ID, $arFields);
			if(!$rs->getAffectedRowsCount()
				&& null == $dbtable::GetRow(array('filter' => array('ID' => $ID), 'select' => array('ID'))))
			{
				$rs = $dbtable::add($arFields);
			}
		}
		else
		{
			unset($arFields['ID']);
			$rs = $dbtable::add($arFields);
			if($rs->isSuccess())
				$ID = $rs->getId();
		}
		if($ID > 0 && $dbtable == PostingFunc::DBTABLE)
			PostingFunc::event('OnSavePostsParams', array('ID'=>$ID));

		if($is_redirect)
		{
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
				$url .= '?ID='.$ID.'&lang='.LANG;
				LocalRedirect($url);
			}
		}
	}

	function SaveParamsPopup()
	{
		self::SaveParams(self::GetPopupID(), PostingFunc::DBTABLE, false);
	}

	function Save($id, $arFields, $dbtable=PostingFunc::DBTABLE)
	{
		if($id <= 0)
			return;
		$rs = $dbtable::update($id, $arFields);
		return $rs->isSuccess();
	}

	function SaveFields($fields, $dbtable=PostingFunc::DBTABLE)
	{
		foreach($fields as $id=>$arFields)
		{
			self::Save(IntVal($id), $arFields, $dbtable);
		}
	}

	function Delete($ID, $dbtable = PostingFunc::DBTABLE)
	{
		if($ID <= 0)
			return;
		$rs = $dbtable::delete($ID);
		if($dbtable == PostingFunc::DBTABLE)
			PostingFunc::event('OnDeletePosts', array('ID'=>$ID));
		return $rs->isSuccess();
	}
}
