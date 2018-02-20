<?
namespace Vettich\Autoposting;

class Posting
{
	private static $arIBlockFieldsName = false;
	private static $arUtmCodes = array('UTM_SOURCE', 'UTM_MEDIUM', 'UTM_CAMPAIGN', 'UTM_TERM', 'UTM_CONTENT');

	function OnPageStart()
	{
		$arElem = self::PopPostElemID();
		if(!$arElem)
		{
			self::UnRegPageStart();
			return;
		}

		self::ElementPost(
			array('ID' => $arElem[0], 'IBLOCK_ID' => $arElem[1]),
			'OnAfterIblockElementAdd',
			array('OnPageStart' => true)
		);
	}

	function RegPageStart()
	{
		RegisterModuleDependences('main', 'OnPageStart', PostingFunc::module_id(), get_class(), 'OnPageStart');
	}

	function UnRegPageStart()
	{
		UnRegisterModuleDependences('main', 'OnPageStart', PostingFunc::module_id(), get_class(), 'OnPageStart');
	}

	function PushPostElemID($ID, $IBLOCK_ID)
	{
		$arElems = unserialize(\COption::GetOptionString(PostingFunc::module_id(), 'post_elems', ''));
		if(empty($arElems))
			$arElems = array();
		$arElems[] = array($ID, $IBLOCK_ID);
		\COption::SetOptionString(PostingFunc::module_id(), 'post_elems', serialize($arElems));
	}

	function PopPostElemID()
	{
		$arElems = unserialize(\COption::GetOptionString(PostingFunc::module_id(), 'post_elems', ''));
		if(empty($arElems))
			return null;

		$ret = array_shift($arElems);
		\COption::SetOptionString(PostingFunc::module_id(), 'post_elems', serialize($arElems));
		return $ret;
	}

	function OnAfterIblockElementAdd($arFields = array())
	{
		$ret = self::ElementPost($arFields, 'OnAfterIblockElementAdd');
	}

	function ElementPost($arFields, $event, $arOptionally=array())
	{
		$tmparFields = $arFields;
		unset($tmparFields['DETAIL_TEXT']);
		unset($tmparFields['SEARCHABLE_CONTENT']);
// \VOptions::debugg([$tmparFields, $event, $arOptionally], 'ElementPost');
		global $module_id;
		if(\COption::GetOptionString(PostingFunc::module_id(), 'is_enable', false) != 'Y')
			return;

		if(!empty($arOptionally['post']))
			$_post['post'] = $arOptionally['post'];
		elseif(!empty($arOptionally['ids']))
			$_post['ids'] = $arOptionally['ids'];

		if(empty($arFields)
			or $arFields['ID'] <= 0
			or (!empty($arFields['WF_PARENT_ELEMENT_ID'])
				&& $arFields['ID'] != $arFields['WF_PARENT_ELEMENT_ID']))
			return;

		$isCatalog = false;
		if((\CModule::IncludeModule('catalog')
					&& \CCatalog::GetByID($arFields['IBLOCK_ID']))
			or $event == 'OnProductAdd')
			$isCatalog = true;

		if($isCatalog && $event == 'OnAfterIblockElementAdd'
			&& !$arOptionally['OnPageStart'])
		{
			self::PushPostElemID($arFields['ID'], $arFields['IBLOCK_ID']);
			self::RegPageStart();
			return;
		}

		// event "OnBeginPosts"
		$ev = PostingFunc::event('OnBeginPosts', array(
			'arFields' => $arFields,
			'event' => $event,
			'post' => $_post
		));
		if($ev === false)
			return;

		\CModule::IncludeModule('iblock');
		if($arOptionally['type'] != 'delete')
		{
			$rsElem = \CIBlockElement::GetByID($arFields['ID']);
			if($arElem = $rsElem->GetNext())
				$arFields = $arElem;
			else
			{
				$text = GetMessage('VCH_ERR_NOT_ELEMENT',
					array(
						'#ID#' => $arFields['ID'],
						'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
						'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
					)
				);
				PostingLogs::addLog('all', $text, 'Error');
				return;
			}

			$rsProp = \CIBlockElement::GetProperty($arFields['IBLOCK_ID'], $arFields['ID'], array(), Array());
			while($arProp = $rsProp->GetNext())
			{
				if(!isset($arFields['PROPERTIES'][$arProp['CODE']]))
				{
					$arFields['PROPERTIES'][$arProp['CODE']] = $arProp;
				}
				if($arProp['MULTIPLE'] == 'Y')
				{
					if($arProp['VALUE']) {
						$arFields['PROPERTIES'][$arProp['CODE']]['VALUES'][] = $arProp['VALUE'];
					}
					if($arProp['~VALUE']) {
						$arFields['PROPERTIES'][$arProp['CODE']]['~VALUES'][] = $arProp['~VALUE'];
					}
					$arFields['PROPERTIES'][$arProp['CODE']]['VALUES_ENUM'][] = $arProp['VALUE_ENUM'];
					$arFields['PROPERTIES'][$arProp['CODE']]['~VALUES_ENUM'][] = $arProp['~VALUE_ENUM'];
					$arFields['PROPERTIES'][$arProp['CODE']]['VALUES_XML_ID'][] = $arProp['VALUE_XML_ID'];
					$arFields['PROPERTIES'][$arProp['CODE']]['~VALUES_XML_ID'][] = $arProp['~VALUE_XML_ID'];
				}
			}

			// support module "catalog"
			if($isCatalog)
			{
				$db_res = \CCatalogProduct::GetList(
					array(),
					array("ID" => $arFields['ID']),
					false,
					false,
					array(
						'ID',
						'QUANTITY',
						'WEIGHT',
						'WIDTH',
						'LENGTH',
						'HEIGHT',
					)
				);
				if($ar = $db_res->Fetch())
				{
					$arFields['CATALOG'] = $ar;

					$rs = \CCatalogGroup::GetList(array(), array(), false, false, array('ID', 'NAME_LANG'));
					while($ar = $rs->Fetch())
					{
						$rsPrice = \CPrice::GetListEx(array(), array(
								'PRODUCT_ID' => $arFields['ID'],
								'CATALOG_GROUP_ID' => $ar['ID'],
							),
							false, false, array(
								'ID',
								'PRICE',
								'CURRENCY',
							)
						);
						if($arPrice = $rsPrice->Fetch())
						{
							$arFields['CATALOG']['PRICE_'.$ar['ID']] = $arPrice['PRICE'];
							$arFields['CATALOG']['CURRENCY_'.$ar['ID']] = $arPrice['CURRENCY'];
						}
					}

					$rsDiscount = \CCatalogDiscount::GetList(array(), array(
							'PRODUCT_ID' => $arFields['ID'],
						),
						false, false, array(
							'ID',
							'ACTIVE_FROM',
							'ACTIVE_TO',
							'NAME',
						)
					);
					if($arDiscount = $rsDiscount->Fetch())
					{
						$arFields['CATALOG']['DISCOUNT_NAME'] = $arDiscount['NAME'];
						$arFields['CATALOG']['DISCOUNT_ACTIVE_FROM'] = $arDiscount['ACTIVE_FROM'];
						$arFields['CATALOG']['DISCOUNT_ACTIVE_TO'] = $arDiscount['ACTIVE_TO'];
					}
				}
			}
		}

		$arResult = array();
		if(!empty($_post['post']))
			$arPosts[] = $_post['post'];
		else
		{
			$arFilter = array();
			if(!empty($_post['ids']))
				$arFilter['ID'] = $_post['ids'];
			else
			{
				$arFilter['IBLOCK_ID'] = $arFields['IBLOCK_ID'];
				$arFilter['TYPE'] = PostingFunc::DBTYPEPOSTS;
			}
			$rs = DBTable::GetList(array(
				'filter' => $arFilter,
			));
			while($ar = $rs->Fetch())
				$arPosts[] = $ar;
		}

		$arElemNavChain = false;

		foreach($arPosts as $arPost)
		{
			
			if($arPost['IS_ENABLE'] != 'Y')
				continue;

			$ev = PostingFunc::event('OnBeforePost', array(
				'arFields' => $arFields,
				'event' => $event,
				'arPost' => $arPost,
			));
			if($ev === false)
				continue;

			if($arPost['MANUALLY'] == 'Y'
				&& $event == 'OnAfterIblockElementAdd')
				continue;

			if($arPost['IS_SECTION_ENABLED'] == 'Y')
			{
				if(!$arElemNavChain[$arFields['IBLOCK_ID']])
				{
					$rsSect = \CIBlockSection::GetNavChain(
						IntVal($arFields['IBLOCK_ID']),
						IntVal($arFields['IBLOCK_SECTION_ID']),
						array('ID')
					);
					while($arSect = $rsSect->GetNext())
					{
						$arElemNavChain[$arFields['IBLOCK_ID']][] = $arSect['ID'];
					}
				}
				$isFound = false;
				foreach($arElemNavChain[$arFields['IBLOCK_ID']] as $sectID)
				{
					if(in_array($sectID, $arPost['SECTIONS']))
					{
						$isFound = true;
						break;
					}
				}
				if(!$isFound)
					continue;
			}

			$is_cmp = true;
			for ($i=0; $i < $arPost['FIELD_CMP_GROUP']['count']; $i++)
			{
				$arCmpFields = $arPost['FIELD_CMP_GROUP'][$i];
				if(!empty($arCmpFields['FIELD_1']) && $arCmpFields['FIELD_1'] != 'none')
				{
					if(strpos($arCmpFields['FIELD_1'], 'PROPERTY_') === 0)
						$field_1 = $arFields['PROPERTIES'][substr($arCmpFields['FIELD_1'], strlen('PROPERTY_'))]['VALUE'];
					elseif(strpos($arCmpFields['FIELD_1'], 'CAT_') === 0)
						$field_1 = $arFields['CATALOG'][substr($arCmpFields['FIELD_1'], strlen('CAT_'))];
					else
						$field_1 = $arFields[$arCmpFields['FIELD_1']];
					
					$arCmpFields['FIELD_CMP'] = IntVal($arCmpFields['FIELD_CMP']);
					if($arCmpFields['FIELD_CMP'] == PostingFunc::FIELD_CMP_EQUALLY) // равно
					{
						if($field_1 != $arCmpFields['FIELD_2'])
							$is_cmp = false;
					}
					elseif($arCmpFields['FIELD_CMP'] == PostingFunc::FIELD_CMP_MORE_OR_EQUALLY) // больше или равно
					{
						if($field_1 < $arCmpFields['FIELD_2'])
							$is_cmp = false;
					}
					elseif($arCmpFields['FIELD_CMP'] == PostingFunc::FIELD_CMP_LESS_OR_EQUALLY) // меньше или равно
					{
						if($field_1 > $arCmpFields['FIELD_2'])
							$is_cmp = false;
					}
					elseif($arCmpFields['FIELD_CMP'] == PostingFunc::FIELD_CMP_CONTAINS) // содержит
					{
						if(strpos($field_1, $arCmpFields['FIELD_2']) === false)
							$is_cmp = false;
					}
					elseif($arCmpFields['FIELD_CMP'] == PostingFunc::FIELD_CMP_NOT_CONTAINS) // не содержит
					{
						if(strpos($field_1, $arCmpFields['FIELD_2']) !== false)
							$is_cmp = false;
					}
				}
				if(!$is_cmp)
					break;
			}
			if(!$is_cmp)
				continue;

			$rsSite = \CSite::GetByID($arPost['SITE_ID']);
			if(!$arSite = $rsSite->GetNext())
				continue;

			if(empty($arSite['SERVER_NAME']))
				$arSite['SERVER_NAME'] = $_SERVER['SERVER_NAME'];

			$iblock_id = $arPost['IBLOCK_ID'];
			if(!($iblock_id = unserialize($iblock_id)))
				$iblock_id = $arPost['IBLOCK_ID'];
			if(!is_array($iblock_id))
				$iblock_id = array($iblock_id);

			if(in_array($arFields['IBLOCK_ID'], $iblock_id) or !empty($_post))
			{
				PostingFunc::event('OnPost', array(
					'arFields' => $arFields,
					'arPost' => $arPost,
					'arSite' => $arSite,
					'arOptionally' => $arOptionally['posts'],
					'arResult' => &$arResult,
				));
				// $posts = PostingFunc::__GetPosts();
				// foreach($posts as $post)
				// {
				// 	$post_accounts = ($arPost['ACCOUNT_'.strtoupper($post)]);
				// 	if(is_array($post_accounts) && !empty($post_accounts))
				// 	{
				// 		if(PostingFunc::isModule($post))
				// 		{
				// 			$ar_post = PostingFunc::module2($post);
				// 			if(method_exists($ar_post['posting'], 'post'))
				// 				$arResult[$post] = $ar_post['posting']::post($arFields, $post_accounts, $arPost, $arSite, $arOptionally['posts'][$post]);
				// 		}
				// 	}
				// }
			}
		}
		return $arResult;
	}

	/**
	* Замена макросов в шаблоне сообщения
	*
	* @param string $message
	* @param array $arFields
	* @param array $arProps
	* @param array $arSite
	* @return string message
	*/
	function replaceMacros($message, $arFields, $arSite, $arPost)
	{
		foreach(self::getIBlockFieldsName($arFields['IBLOCK_ID']) as $name)
		{
			if(!isset($arFields[$name]))
				continue;
			$_name = '#'.$name.'#';
			if($name == 'PREVIEW_PICTURE' or $name == 'DETAIL_PICTURE')
			{
				$imgPath = \CFile::GetPath($arFields[$name]);
				$message = str_replace($_name, self::getLinkFromString($imgPath, $arSite, $arPost), $message);
			}
			elseif($name == 'LIST_PAGE_URL' or $name == 'DETAIL_PAGE_URL')
			{
				$message = str_replace($_name, self::getLinkFromString($arFields[$name], $arSite, $arPost), $message);
			}
			else
			{
				$message = str_replace($_name, $arFields[$name], $message);
			}
		}
		foreach($arFields['PROPERTIES'] as $name=>$arProp)
		{
			$_name = '#PROPERTY_'.$arProp['CODE'].'#';
			$str = '';
			if($arProp['PROPERTY_TYPE'] == 'S')
			{
				if($arProp['USER_TYPE'] == 'HTML')
				{
					if($arProp['MULTIPLE'] == 'Y')
					{
						foreach($arProp['~VALUES'] as $value)
						{
							$str .= $value['TEXT'];
							$str .= "\n";
						}
					}
					else
					{
						$str = $arProp['~VALUE']['TEXT'];
					}
				}
				elseif($arProp['USER_TYPE'] == 'video')
				{
					if($arProp['MULTIPLE'] == 'Y')
					{
						foreach($arProp['VALUES'] as $value)
						{
							$str .= self::getServerURL($arSite, $arPost);
							if(strpos($value['path'], '/') === 0)
							{
								$str .= substr($value['path'], 1);
							}
							else
							{
								$str .= $value['path'];
							}
							$str .= ', ';
						}
						$str .= substr($str, 0, -2);
					}
					else
					{
						$str .= self::getServerURL($arSite, $arPost);
						if(strpos($arProp['VALUE']['path'], '/') === 0)
						{
							$str .= substr($arProp['VALUE']['path'], 1);
						}
						else
						{
							$str .= $arProp['VALUE']['path'];
						}
					}
				}
				elseif(!is_array($arProp['VALUE']))
				{
					if($arProp['MULTIPLE'] == 'Y')
					{
						foreach($arProp['VALUES'] as $value)
						{
							$str .= $value;
							$str .= ', ';
						}
						$str .= substr($str, 0, -2);
					}
					else
					{
						$str .= $arProp['VALUE'];
					}
				}
				$message = str_replace($_name, $str, $message);
			}
			elseif($arProp['PROPERTY_TYPE'] == 'N')
			{
				if(!is_array($arProp['VALUE']))
				{
					if($arProp['MULTIPLE'] == 'Y')
					{
						foreach($arProp['VALUES'] as $value)
						{
							$str .= $value;
							$str .= ', ';
						}
						$str .= substr($str, 0, -2);
					}
					else
					{
						$str .= $arProp['VALUE'];
					}
				}
				$message = str_replace($_name, $str, $message);
			}
			elseif($arProp['PROPERTY_TYPE'] == 'L')
			{
				$str = '';
				if($arProp['MULTIPLE'] == 'Y')
				{
					foreach($arProp['VALUES_ENUM'] as $key=>$value)
					{
						if(empty($str))
						{
							$str = $value;
						}
						else
						{
							$str .= ', '.$value;
						}
					}
				}
				else
				{
					$str = $arProp['VALUE_ENUM'];
				}
				$message = str_replace($_name, $str, $message);
			}
			elseif(($arProp['PROPERTY_TYPE'] == 'E' or $arProp['PROPERTY_TYPE'] == 'G')
				&& !empty($arProp['VALUES']) && empty($arProp['VALUE']))
			{
				$offset = 0;
				$arElemProps = array();
				while(preg_match('/#PROPERTY_\w+[\.]{0,1}(\w+){0,1}#/', $message, $matches, PREG_OFFSET_CAPTURE, $offset))
				{
					if(isset($matches[1][0]) && !empty($matches[1][0]))
					{
						$arElemProps[] = $matches[1][0];
					}
					$offset = $matches[0][1] + strlen($matches[0][0]);
				}
				if(!in_array('NAME', $arElemProps))
					$arElemProps[] = 'NAME';

				$arElemFields = array();
				if($arProp['PROPERTY_TYPE'] == 'E')
				{
					$rsElem = \CIBlockElement::GetList(
						Array('sort'=>'asc'),
						array(
							'ID' => $arProp['VALUES'] ?: $arProp['VALUE'],
							'IBLOCK_ID' => $arProp['LINK_IBLOCK_ID'],
						),
						false,
						false,
						$arElemProps
					);
					while($arElem = $rsElem->GetNext())
					{
						foreach ($arElem as $key => $value) {
							$arElemFields[$key][] = $value;
						}
						// $arElemFields[] = $arElem;
					}
				}
				else
				{
					$rsSection = \CIBlockSection::GetList(
						Array('sort'=>'asc'),
						array(
							'ID' => $arProp['VALUES'] ?: $arProp['VALUE'],
							'IBLOCK_ID' => $arProp['LINK_IBLOCK_ID'],
						),
						false,
						$arElemProps
					);
					while($arSection = $rsSection->GetNext())
					{
						foreach ($arSection as $key => $value) {
							$arElemFields[$key][] = $value;
						}
						// $arElemFields[] = $arSection;
					}
				}
				foreach ($arElemFields as $key => $value) {
					$arElemFields[$key] = implode(', ', $value);
				}

				foreach($arElemProps as $value)
				{
					if(strpos($value, 'PROPERTY_') === 0 && !is_array($arElemFields['PROPERTY_'.$value.'_VALUE']))
					{
						$_name = '#PROPERTY_'.$name.'.PROPERTY_'.$value.'#';
						$message = str_replace($_name, $arElemFields['PROPERTY_'.$value.'_VALUE'], $message);
					}
					else
					{
						$_name = '#PROPERTY_'.$name.'.'.$value.'#';
						if($value == 'PREVIEW_PICTURE' or $value == 'DETAIL_PICTURE' or $value == 'PICTURE')
						{
							$imgPath = \CFile::GetPath($arElemFields[$value]);
							$message = str_replace($_name, $imgPath, $message);
						}
						elseif($value == 'LIST_PAGE_URL' or $value == 'DETAIL_PAGE_URL')
						{
							$link = self::getServerURL($arSite, $arPost);
							if(strpos($arElemFields[$value], '/') === 0)
							{
								$link .= substr($arElemFields[$value], 1);
							}
							else
							{
								$link .= $arElemFields[$value];
							}
							$message = str_replace($_name, $link, $message);
						}
						else
						{
							$message = str_replace($_name, $arElemFields[$value], $message);
							if($value == 'NAME')
							{
								$message = str_replace('#PROPERTY_'.$name.'#', $arElemFields[$value], $message);
							}
						}
					}
				}
			}
		}
		foreach($arFields['CATALOG'] as $k => $cat)
		{
			$message = str_replace('#CAT_'.$k.'#', $cat, $message);
		}
		return $message;
	}

	function getStringFromProperty($name, $arFields, $arSite, $arPost)
	{
		$result = '';
		if(strpos($name, 'PROPERTY_') === false)
		{
			if($name == 'PREVIEW_PICTURE' or $name == 'DETAIL_PICTURE')
			{
				$imgPath = \CFile::GetPath($arFields[$name]);
				$result = $imgPath;
			}
			elseif($name == 'LIST_PAGE_URL' or $name == 'DETAIL_PAGE_URL')
			{
				$link = self::getServerURL($arSite, $arPost);
				if(strpos($arFields[$name], '/') === 0)
				{
					$link .= substr($arFields[$name], 1);
				}
				else
				{
					$link .= $arFields[$name];
				}
				$result = $link;
			}
			else
			{
				$result = $arFields[$name];
			}
		}
		else
		{
			$prop_name = substr($name, strlen('PROPERTY_'));
			if(!isset($arFields['PROPERTIES'][$prop_name]))
				return '';

			$arProp = $arFields['PROPERTIES'][$prop_name];

			if($arProp['PROPERTY_TYPE'] == 'S')
			{
				if($arProp['USER_TYPE'] == 'HTML')
				{
					if($arProp['MULTIPLE'] == 'Y')
					{
						foreach($arProp['~VALUES'] as $value)
						{
							$str .= $value['TEXT'];
							$str .= "\n";
						}
					}
					else
					{
						$str = $arProp['~VALUE']['TEXT'];
					}
				}
				elseif($arProp['USER_TYPE'] == 'video')
				{
					if($arProp['MULTIPLE'] == 'Y')
					{
						$ar = array();
						foreach($arProp['VALUES'] as $value)
						{
							$s .= self::getServerURL($arSite, $arPost);
							if(strpos($value['path'], '/') === 0)
							{
								$s .= substr($value['path'], 1);
							}
							else
							{
								$s .= $value['path'];
							}
							$ar[] = $s;
						}
						$str .= implode(', ', $ar);
					}
					else
					{
						$str .= self::getServerURL($arSite, $arPost);
						if(strpos($arProp['VALUE']['path'], '/') === 0)
						{
							$str .= substr($arProp['VALUE']['path'], 1);
						}
						else
						{
							$str .= $arProp['VALUE']['path'];
						}
					}
				}
				elseif(!is_array($arProp['VALUE']))
				{
					if($arProp['MULTIPLE'] == 'Y')
					{
						$ar = array();
						foreach($arProp['VALUES'] as $value)
						{
							$ar[] = $value;
						}
						$str .= implode(', ', $ar);
					}
					else
					{
						$str .= $arProp['VALUE'];
					}
				}
				$result = $str;
			}
			elseif($arProp['PROPERTY_TYPE'] == 'N')
			{
				if(!is_array($arProp['VALUE']))
				{
					if($arProp['MULTIPLE'] == 'Y')
					{
						$ar = array();
						foreach($arProp['VALUES'] as $value)
						{
							$ar[] = $value;
						}
						$str .= implode(', ', $ar);
					}
					else
					{
						$str .= $arProp['VALUE'];
					}
				}
				$result = $str;
			}
			elseif($arProp['PROPERTY_TYPE'] == 'L')
			{
				$str = '';
				if($arProp['MULTIPLE'] == 'Y')
				{
					$ar = array();
					foreach($arProp['VALUES_ENUM'] as $key=>$value)
					{
						$ar[] = $value;
					}
					$str = implode(', ', $ar);
				}
				else
				{
					$str = $arProp['VALUE_ENUM'];
				}
				$result = $str;
			}
			elseif($arProp['PROPERTY_TYPE'] == 'E' or $arProp['PROPERTY_TYPE'] == 'G')
			{
				$offset = 0;
				$arSelect = array('NAME');

				$arElemFields = array();
				if($arProp['PROPERTY_TYPE'] == 'E')
				{
					$rsElem = CIBlockElement::GetList(
						array(), 
						array(
							'ID' => $arProp['VALUE'],
							'IBLOCK_ID' => $arProp['LINK_IBLOCK_ID'],
						),
						false,
						false,
						$arSelect
					);
					if($arElem = $rsElem->GetNext())
					{
						$arElemFields = $arElem;
					}
				}
				else
				{
					$rsSection = CIBlockSection::GetList(
						Array('sort'=>'asc'),
						array(
							'ID' => $arProp['VALUE'],
							'IBLOCK_ID' => $arProp['LINK_IBLOCK_ID'],
						),
						false,
						$arSelect
					);
					if($arSection = $rsSection->GetNext())
					{
						$arElemFields = $arSection;
					}
				}

				$value = 'NAME';
				if(strpos($value, 'PROPERTY_') === 0 && !is_array($arElemFields['PROPERTY_'.$value.'_VALUE']))
				{
					$_name = '#PROPERTY_'.$name.'.PROPERTY_'.$value.'#';
					$result = $arElemFields['PROPERTY_'.$value.'_VALUE'];
				}
				else
				{
					$_name = '#PROPERTY_'.$name.'.'.$value.'#';
					if($value == 'PREVIEW_PICTURE' or $value == 'DETAIL_PICTURE' or $value == 'PICTURE')
					{
						$imgPath = \CFile::GetPath($arElemFields[$value]);
						$result = $imgPath;
					}
					elseif($value == 'LIST_PAGE_URL' or $value == 'DETAIL_PAGE_URL')
					{
						$link = self::getServerURL($arSite, $arPost);
						if(strpos($arElemFields[$value], '/') === 0)
						{
							$link .= substr($arElemFields[$value], 1);
						}
						else
						{
							$link .= $arElemFields[$value];
						}
						$result = $link;
					}
					else
					{
						$result = $arElemFields[$value];
					}
				}
			}
		}
		return $result;
	}

	function getFilesFromProperty($sProp, $arFields)
	{
		$arResult = array();
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
						$arResult[] = $_SERVER['DOCUMENT_ROOT'].\CFile::GetPath($arValue);
					}
				}
				else
				{
					$arResult[] = $_SERVER['DOCUMENT_ROOT'].\CFile::GetPath($arProp['VALUE']);
				}
			}
		}
		else
		{
			$img_path = \CFile::GetPath($arFields[$sProp]);
			if($img_path != '')
			{
				$arResult[] = $_SERVER['DOCUMENT_ROOT'].$img_path;
			}
		}
		return $arResult;
	}

	function getLinkFromProperty($sProp, $arFields, $arPost, $arSite)
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
						$result = self::getLinkFromString($value, $arSite, $arPost);
						break;
					}
				}
				else
				{
					$result = self::getLinkFromString($arProp['VALUE'], $arSite, $arPost);
				}
			}
		}
		else
		{
			if($sProp == 'DETAIL_PAGE_URL'
				or $sProp == 'LIST_PAGE_URL')
			{
				$result = self::getLinkFromString($arFields[$sProp],  $arSite, $arPost);
			}
			elseif($sProp == 'DETAIL_PICTURE'
				or $sProp == 'PREVIEW_PICTURE')
			{
				$imgPath = \CFile::GetPath($arFields[$sProp]);
				$result = self::getLinkFromString($imgPath,  $arSite, $arPost);
			}
		}
		return $result;
	}

	function getLinkFromString($link, $arSite, $arPost)
	{
		$link = trim($link);
		if(empty($link))
			return '';

		if($arPost['IS_UTM_ENABLE'] == 'Y' && !empty($arPost['arAccount']))
		{
			$utm_url = array();
			foreach(self::$arUtmCodes as $utmCode)
			{
				if($utm = trim($arPost['arAccount'][$arPost['ACCPREFIX'].'_'.$utmCode]))
				{
					$utm_url[strtolower($utmCode)] = $utm;
				}
				$utms[] = $utm;
			}
			if(!empty($utm_url))
			{
				$link .= (strpos($link, '?') !== false ? '&' : '?')
					.http_build_query($utm_url);
			}
		}

		if(strpos($link, 'http') === 0)
		{
			return $link;
		}

		$result = self::getServerURL($arSite, $arPost);

		if(strpos($link, '/') === 0)
		{
			return $result.substr($link, 1);
		}
		return $result.$link;
	}

	function getServerURL($arSite=array(), $arPost=array())
	{
		$ret = '';
		if(!empty($arPost['DOMAIN_NAME']))
		{
			$ret = $arPost['DOMAIN_NAME'];
			if(substr($ret, -1) != '/')
				$ret .= '/';
		}
		elseif(!empty($arSite) && !empty($arSite['SERVER_NAME']))
			$ret = $arSite['SERVER_NAME'].$arSite['DIR'];

		if(empty($ret))
			$ret = $_SERVER['SERVER_NAME'].SITE_DIR;

		if(strpos($ret, 'http') !== 0)
		{
			if($_SERVER['HTTPS'])
				$ret = 'https://'.$ret;
			else
				$ret = 'http://'.$ret;
		}

		if($arPost['PROTOCOL'] == 'https' && strpos($ret, 'http://')===0)
			$ret = str_replace('http', 'https', $ret);
		elseif($arPost['PROTOCOL'] == 'http' && strpos($ret, 'https')===0)
			$ret = str_replace('https', 'http', $ret);
		
		return $ret;
	}

	function GetUrlPostAcc($post, $id, $name)
	{
		return '<a href="/bitrix/admin/vettich_autoposting_posts_edit_'.$post.'.php?ID='.$id.'">'.$name.'</a>';
	}

	function paramValues($opt_name, $index=0, $default ='')
	{
		$return = array();
		if(CVDB::get($opt_name.'[params]', false) !== false)
		{
			$params = \VOptions::_json_decode(CVDB::get($opt_name.'[params]', false));
			if($index<=0)
			{
				$cnt = CVDB::get($opt_name, 0);
				for($i=1; $i<=$cnt; $i++)
				{
					foreach($params as $param)
					{
						$return[$i][$param] = self::paramValues($opt_name."[$i][$param]", 0, $default);
					}
				}
			}
			else
			{
				foreach($params as $param)
				{
					$return[$param] = self::paramValues($opt_name."[$index][$param]", 0, $default);
				}
			}
		}
		else
			return CVDB::get($opt_name, $default);
		return $return;
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

	function getCurlFilename($filename)
	{
		if (version_compare(PHP_VERSION, '5.6.0', '<'))
		{
			return '@'.$filename;
		}
		else
		{
			return new \CURLFile($filename);
		}
	}

	static function getIBlockFieldsName($IBLOCK_ID)
	{
		if(!!self::$arIBlockFieldsName)
			return self::$arIBlockFieldsName;

		self::$arIBlockFieldsName = array(
			'ID',
			'CODE',
			'XML_ID',
			'NAME',
			'IBLOCK_ID',
			'IBLOCK_SECTION_ID',
			'IBLOCK_CODE',
			'ACTIVE',
			'DATE_ACTIVE_FROM',
			'DATE_ACTIVE_TO',
			'SORT',
			'PREVIEW_PICTURE',
			'PREVIEW_TEXT',
			'DETAIL_PICTURE',
			'DETAIL_TEXT',
			'DATE_CREATE',
			'CREATED_BY',
			'CREATED_USER_NAME',
			'TIMESTAMP_X',
			'MODIFIED_BY',
			'USER_NAME',
			'LIST_PAGE_URL',
			'DETAIL_PAGE_URL',
			'SHOW_COUNTER',
			'SHOW_COUNTER_START',
			'WF_COMMENTS',
			'WF_STATUS_ID',
			'TAGS',
		);
		return self::$arIBlockFieldsName;
	}
}
