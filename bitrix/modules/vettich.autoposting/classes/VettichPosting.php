<?

class VettichPosting
{
	function OnAfterIblockElementAdd($arFields = array())
	{
		self::ElementPost($arFields);
	}

	function ElementPost($arFields, $_post=array())
	{
		global $module_id;

		if(VOptions::get('is_enable', false, VettichPostingFunc::module_id()) != 'Y')
			return;

		if(empty($arFields)
			or $arFields['ID'] <= 0
			or (isset($arFields['WF_PARENT_ELEMENT_ID'])
				&& $arFields['ID'] != $arFields['WF_PARENT_ELEMENT_ID']))
			return;


		$rsElem = CIBlockElement::GetByID($arFields['ID']);
		// $rsElem = CIBlockElement::GetList(array(), array(
		// 	'IBLOCK_ID' => $arFields['IBLOCK_ID'],
		// 	'IBLOCK_TYPE' => $arFields['IBLOCK_TYPE_ID'],
		// 	'ID' => $arFields['ID'],
		// ), false, false, array());
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
			VettichPostingLogs::addLog('all', $text, 'Error');
			return;
		}

		$rsProp = CIBlockElement::GetProperty($arFields['IBLOCK_ID'], $arFields['ID'], array(), Array());
		while($arProp = $rsProp->GetNext())
		{
			if(!isset($arFields['PROPERTIES'][$arProp['CODE']]))
			{
				$arFields['PROPERTIES'][$arProp['CODE']] = $arProp;
			}
			if($arProp['MULTIPLE'] == 'Y')
			{
				$arFields['PROPERTIES'][$arProp['CODE']]['VALUES'][] = $arProp['VALUE'];
				$arFields['PROPERTIES'][$arProp['CODE']]['~VALUES'][] = $arProp['~VALUE'];
				$arFields['PROPERTIES'][$arProp['CODE']]['VALUES_ENUM'][] = $arProp['VALUE_ENUM'];
				$arFields['PROPERTIES'][$arProp['CODE']]['~VALUES_ENUM'][] = $arProp['~VALUE_ENUM'];
				$arFields['PROPERTIES'][$arProp['CODE']]['VALUES_XML_ID'][] = $arProp['VALUE_XML_ID'];
				$arFields['PROPERTIES'][$arProp['CODE']]['~VALUES_XML_ID'][] = $arProp['~VALUE_XML_ID'];
			}
		}

		$arResult = array();

		if(!empty($_post['post']))
			$post_ids[] = 0;
		elseif(!empty($_post['ids']))
			$post_ids = $_post['ids'];
		else
			$post_ids = self::GetIDs();

		foreach($post_ids as $post_id)
		{
			if($post_id == 0)
				$arPost = $_post['post'];
			else
				$arPost = self::paramValues('posts', $post_id);
			
			if($arPost['is_enable'] != 'Y')
				continue;

			if(!empty($arPost['field_1']) && $arPost['field_1'] != 'none')
			{
				if(strpos($arPost['field_1'], 'PROPERTY_') === 0)
					$field_1 = $arFields['PROPERTIES'][substr($arPost['field_1'], strlen('PROPERTY_'))]['VALUE'];
				else
					$field_1 = $arFields[$arPost['field_1']];
				
				if($arPost['field_cmp'] == '1') // равно
				{
					if($field_1 != $arPost['field_2'])
						continue;
				}
				elseif($arPost['field_cmp'] == '2') // больше или равно
				{
					if($field_1 < $arPost['field_2'])
						continue;
				}
				elseif($arPost['field_cmp'] == '3') // меньше или равно
				{
					if($field_1 > $arPost['field_2'])
						continue;
				}
			}

			$rsSite = CSite::GetByID($arPost['site_id']);
			if(!$arSite = $rsSite->GetNext())
				return;

			if(empty($arSite['SERVER_NAME']))
				$arSite['SERVER_NAME'] = $_SERVER['SERVER_NAME'];

			$iblock_id = $arPost['iblock_id'];
			if(!($iblock_id = unserialize($iblock_id)))
				$iblock_id = $arPost['iblock_id'];
			if(!is_array($iblock_id))
				$iblock_id = array($iblock_id);

			if(in_array($arFields['IBLOCK_ID'], $iblock_id) or !empty($_post))
			{
				$posts = VettichPostingFunc::__GetPosts();
				foreach($posts as $post)
				{
					$post_accounts = unserialize($arPost['account_'.$post]);
					if(is_array($post_accounts) && !empty($post_accounts))
					{
						if(VettichPostingFunc::IncludeModule($post))
						{
							$ar_post = VettichPostingFunc::module($post);
							if(method_exists($ar_post['class'], 'post'))
								$ar_post['class']::post($arFields, $post_accounts, $arPost, $arSite);
						}
					}
				}
			}
		}
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
		foreach(self::getIBlockFieldsName() as $name)
		{
			$_name = "#$name#";
			if($name == 'PREVIEW_PICTURE' or $name == 'DETAIL_PICTURE')
			{
				$imgPath = CFile::GetPath($arFields[$name]);
				$message = str_replace($_name, $imgPath, $message);
			}
			elseif($name == 'LIST_PAGE_URL' or $name == 'DETAIL_PAGE_URL')
			{
				$link = VettichPosting::getServerURL($arSite, $arPost);
				if(strpos($arFields[$name], '/') === 0)
				{
					$link .= substr($arFields[$name], 1);
				}
				else
				{
					$link .= $arFields[$name];
				}
				$message = str_replace($_name, $link, $message);
			}
			else
			{
				$message = str_replace($_name, $arFields[$name], $message);
			}
		}
		foreach($arFields['PROPERTIES'] as $name=>$arProp)
		{
			$_name = "#PROPERTY_".$arProp['CODE']."#";
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
							$str .= VettichPosting::getServerURL($arSite, $arPost);
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
						$str .= VettichPosting::getServerURL($arSite, $arPost);
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
			elseif($arProp['PROPERTY_TYPE'] == 'E' or $arProp['PROPERTY_TYPE'] == 'G')
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
					$rsElem = CIBlockElement::GetList(
						array(), 
						array(
							'ID' => $arProp['VALUE'],
							'IBLOCK_ID' => $arProp['LINK_IBLOCK_ID'],
						),
						false,
						false,
						$arElemProps
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
						$arElemProps
					);
					if($arSection = $rsSection->GetNext())
					{
						$arElemFields = $arSection;
					}
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
							$imgPath = CFile::GetPath($arElemFields[$value]);
							$message = str_replace($_name, $imgPath, $message);
						}
						elseif($value == 'LIST_PAGE_URL' or $value == 'DETAIL_PAGE_URL')
						{
							$link = VettichPosting::getServerURL($arSite, $arPost);
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
		return $message;
	}

	function getStringFromProperty($name, $arFields, $arSite, $arPost)
	{
		$result = '';
		if(strpos($name, 'PROPERTY_') === false)
		{
			if($name == 'PREVIEW_PICTURE' or $name == 'DETAIL_PICTURE')
			{
				$imgPath = CFile::GetPath($arFields[$name]);
				$result = $imgPath;
			}
			elseif($name == 'LIST_PAGE_URL' or $name == 'DETAIL_PAGE_URL')
			{
				$link = VettichPosting::getServerURL($arSite, $arPost);
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
						foreach($arProp['VALUES'] as $value)
						{
							$str .= VettichPosting::getServerURL($arSite, $arPost);
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
						$str .= VettichPosting::getServerURL($arSite, $arPost);
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
				$result = $str;
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
				$result = $str;
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
						$imgPath = CFile::GetPath($arElemFields[$value]);
						$result = $imgPath;
					}
					elseif($value == 'LIST_PAGE_URL' or $value == 'DETAIL_PAGE_URL')
					{
						$link = VettichPosting::getServerURL($arSite, $arPost);
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

	function getServerURL($arSite=array(), $arPost=array())
	{
		$ret = '';
		if(!empty($arPost['domain_name']))
		{
			$ret = $arPost['domain_name'];
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

		if($arPost['protocol'] == 'https' && strpos($ret, 'http://')===0)
			$ret = str_replace('http', 'https', $ret);
		elseif($arPost['protocol'] == 'http' && strpos($ret, 'https')===0)
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
			$params = VOptions::_json_decode(CVDB::get($opt_name.'[params]', false));
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

	function getIBlockFieldsName()
	{
		return array(
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
	}
}
