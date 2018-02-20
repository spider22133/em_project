<?IncludeModuleLangFile(__FILE__);
global $APPLICATION;

/*
*
var $arModuleParams - параметры модуля
	array(
		'TABS' => array(
			'название группы настроек' => array(
				'NAME' => 'имя вкладки',
				'DIV' => 'индекс вкладки', // необязательный ключ
				'TITLE' => 'всплывающий заголовок', // необязательный ключ
				'ICON' => 'название иконки' // необязательный ключ
			)
		),
		'PARAMS' => array(
			'название настройки' => array(
				'TAB' => 'название группы настроек',
				'NAME' => 'имя настройки',
				'TYPE' => 'тип настройки',
				'DEFAULT' => 'значение по умолчанию', // необязательный ключ
				'REFRESH' => 'перегружать настройки или нет после выбора (N/Y)', // необязательный ключ
				'REQUIRED' => 'обязательное поле или нет (N/Y)', // необязательный ключ
			)
		)
	)

'TYPE' - варианты типов настроек

	TEXT | STRING - строка
		Дополнительные параметры:
			MAXLENGTH - длина строки
			SIZE - количество символов в поле

	TEXTAREA - 
		Дополнительные параметры:
			COLS - количество столбцов символов
			ROWS - количество строк

	NUMBER - для ввода цифр
		Дополнительные параметры:
			MIN - минимальное значение
			MAX - максимальное значение
			STEP - шаг изменения числа

	EMAIL - для ввода эмайл
		Дополнительные параметры:
			MAXLENGTH - длина строки
			SIZE - количество символов в поле

	PASSWORD - для ввода пароля
		Дополнительные параметры:
			MAXLENGTH - длина строки
			SIZE - количество символов в поле

	CHECKBOX - флажки
		Дополнительные параметры:
			VALUES - список значений флажков

	RADIO - переключатели
		Дополнительные параметры:
			VALUES - список значений переключателя

	COLOR | COLORPICKER - для выбора цвета
		Дополнительные параметры (если браузер не поддерживает HTML5):
			MAXLENGTH - длина строки
			SIZE - количество символов в поле

	SELECT - список
		Дополнительные параметры:
			MULTIPLE - множественный список (Y | N) (по умолчанию - N)

	NOTE - заметка
		Дополнительные параметры:
			DEFAULT - текст заметки

	HIDDEN - скрытое поле
		Дополнительные параметры:
			нету

	FILE - для выбора файла
		Дополнительные параметры:
			MULTIPLE - множественный выбор файлов (Y | N) (по умолчанию - Y)
			FILE_SIZE - размер файла в байтах (по умолчанию неограничен)
			FILE_TYPE - тип файла, A - все, F - файлы, I - картинки (по умолчанию - A)
			FILE_EXT - дополнительное расширения для файлов типа F, например "*.zip,*rar" (по умолчанию любые)

	GROUP - группа опций
		Дополнительный параметры:
			VALUES - массив параметров
			ADDBUTON - текст кнопки "Добавить"

*	var $module_id - ID модуля
*/

/*
string module_id - идентификатор модуля

array arModuleParams - массив настроек модуля
array arIncludeJS - массив подключаемых js файлов (содержит полный путь до js файлов)
*/
class VOptions
{
	var $defaultModuleID = 'vettich.options';

	var $arModuleParams = false;
	var $arTabs = false;
	var $arParams = false;

	var $moduleID = '';

	var $html5 = true;
	var $isSubmitForm;
	var $isRestoreDefaults = false;
	var $isSave = false;
	var $isUpdate = false;

	var $formID;
	var $formContainerID;

	var $group_separator = '-';
	var $is_parse_request = false;

	function __construct()
	{
		global $module_id, $APPLICATION;
		$this->formID = 'VOptionsForm';
		$this->formContainerID = 'VOptionsFormContainer';

		$_POST = $APPLICATION->ConvertCharsetArray($_POST, 'UTF-8', SITE_CHARSET);
		if(isset($_POST['VOPTIONS_SUBMIT']) && $_POST['VOPTIONS_SUBMIT'] == 'Y')
		{
			$this->isSubmitForm = true;
		}
		else
		{
			$this->isSubmitForm = false;
		}

		if(isset($module_id))
			$this->moduleID = $module_id;
		else
			$this->moduleID = $this->defaultModuleID;
	}

	function init()
	{
		$this->init_module_params();
		$this->parseRequest(true);
		$this->show();
	}

	function init_module_params()
	{
		global $arModuleParams, $module_id;

		if(isset($arModuleParams))
		{
			$this->arModuleParams = $arModuleParams;
			$this->_generateTabs($arModuleParams);
			$this->_generateParams($arModuleParams);
		}
	}

	function _generateTabs($arModuleParams)
	{
		if(!isset($arModuleParams['TABS']))
			return false;

		$arTabs = array();
		foreach($arModuleParams['TABS'] as $key=>$arTab)
		{
			$curTabCount = count($this->arTabs);
			$tab = array(
				'DIV' => (isset($arTab['DIV'])? $arTab['DIV'] : $key),
				'TAB' => (isset($arTab['NAME'])? $arTab['NAME'] : 'Tab '. ($curTabCount + 1)),
				'TITLE' => (isset($arTab['TITLE'])? $arTab['TITLE'] : ''),
			);

			if(isset($arTab['ICON']))
				$tab['ICON'] = $arTab['ICON'];

			$arTabs[] = $tab;
		}
		$this->setArTabs($arTabs);
	}

	function _generateParams($arModuleParams)
	{
		if(!isset($arModuleParams['PARAMS']))
			return false;

		$this->arParams = $arModuleParams['PARAMS'];
	}

	function setModuleID($_moduleID)
	{
		$this->moduleID = $_moduleID;
	}

	function setArTabs($arTabs)
	{
		$this->arTabs = $arTabs;
	}

	function enableHTML5()
	{
		$this->html5 = true;
	}

	function disableHTML5()
	{
		$this->html5 = false;
	}

	function addOptions($arOptions, $tabIndex)
	{
		foreach($arOptions as $key=>$value)
		{
			$this->arTabs[$tabIndex]['OPTIONS'][$key] = $value;
		}
	}

	function remOptions($options, $tabIndex)
	{
		if(is_array($options))
		{
			foreach($options as $value)
			{
				unset($this->arTabs[$tabIndex]['OPTIONS'][$value]);
			}
		}
		elseif(!empty($options))
		{
			unset($this->arTabs[$tabIndex]['OPTIONS'][$options]);
		}
	}

	function getCurrentValue($sOption, $default = '')
	{
		if($sOption=='')
			return $default;

		if(isset($_POST[$sOption]))
		{
			return $_POST[$sOption];
		}
		else
		{
			return COption::GetOptionString($this->moduleID, $sOption, $default);
		}
	}

	function setCurrentValue($sOption, $value)
	{
		$_POST[$sOption] = $value;
	}

	function getOption($optName)
	{
		foreach($this->arTabs as $tab)
		{
			foreach($tab['OPTIONS'] as $name => $arOption)
			{
				if($name == $optName)
				{
					return $arOption;
				}
			}
		}
		return array();
	}

	function getOptionValue($optName, $default='')
	{
		return COption::GetOptionString($this->moduleID, $optName, $default);
	}

	function setOptionValue($optName, $value = '')
	{
		COption::SetOptionString($this->moduleID, $optName, $value);
	}

	function getGroupOptionsValue($optName, $index=-1)
	{
		$arOptions = $this->getOption($optName);
		$arResult = array();
		if($index < 0)
		{
			for($i=0; $i<$this->getOptionValue($optName); $i++)
			{
				$arTResult = array();
				foreach($arOptions['VALUES'] as $name => $arOption)
				{
					$default = isset($arOption['DEFAULT'])? $arOption['DEFAULT'] : '';
					$arTResult[$name] = $this->getOptionValue($optName .'_'. $name .'_'. $i, $default);
				}
				if(!empty($arTResult))
					$arResult[] = $arTResult;
			}
		}
		elseif($index>=0 && $index<$this->getOptionValue($optName))
		{
			foreach($arOptions as $name=>$option)
			{
				$default = isset($arOption['DEFAULT'])? $arOption['DEFAULT'] : '';
				$arResult[$name] = $this->getOptionValue($optName .'_'. $name .'_'. $index, $default);
			}
		}
		return $arResult;
	}

	function _sortTabParams(&$arTabParams)
	{
		// function _sort_tmp($a, $b)
		// {
		// 	$asort = (isset($a['SORT']) ? $a['SORT'] : '500');
		// 	$bsort = (isset($b['SORT']) ? $b['SORT'] : '500');
		// 	return strcmp($asort, $bsort);
		// }
		foreach($arTabParams as $key => $arTab)
		{
			$this->_sortArParams($arTabParams[$key]);
			// uasort($arTabParams[$key], '_sort_tmp');
		}
	}

	function _sortArParams(&$arParams)
	{
		$sort_ind = 500;
		foreach ($arParams as $key => $value)
			if(!isset($value['SORT']))
				$arParams[$key]['SORT'] = $sort_ind++;

		$_sort_tmp = function($a, $b)
		{
			$asort = (isset($a['SORT']) ? intval($a['SORT']) : 500);
			$bsort = (isset($b['SORT']) ? intval($b['SORT']) : 500);
			return $asort > $bsort ? 1 : ($asort < $bsort ? -1 : 0);
		};
		uasort($arParams, $_sort_tmp);
	}

	function showCSS()
	{
		$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/css/vettich.autoposting/VOptions.css');
	}

	function showJS($params=array())
	{
		global $arIncludeJS;
		CJSCore::Init(array('ajax'));
		CJSCore::Init(array("jquery"));
		$url = (empty($_SERVER['HTTPS'])?'http://':'https://').
				$_SERVER['SERVER_NAME'].
				'/bitrix/admin/settings.php?lang='.LANG.
				'&mid='.$this->moduleID.
				'&mid_menu=1';
		$AddButton = GetMessage('ADDBUTTON');
		?>
		<script type="text/javascript">
			var VOptionsParams = <?=self::_json_encode($this->arParams);?>;
			var VOptionsGroupSeparator = <?=self::_json_encode($this->group_separator);?>;
			var VOptionsGroupAddButtonText = "<?=$AddButton;?>";
			var VOptionsServerUri = "<?=$_SERVER['SERVER_NAME']?>";
			var TEXTAREA_SHOW_CHOISE = "<?=GetMessage('TEXTAREA_SHOW_CHOISE')?>";
			<?foreach($params as $k => $param):?>
				var <?=$k?> = <?=self::_json_encode($param);?>;
			<?endforeach;?>

			// var VOptionRefresh = function()
			// {
			// 	BX.adjust(BX('VOPTIONS_SUBMIT'), {props: {value: 'N'}});
			// 	var url = "<?=$url?>";
			// 	var form_id = "<?=$this->formID?>";
			// 	var result_id = "<?=$this->formContainerID?>";
			// 	var data = jQuery("#"+form_id).serialize() + '&Save=Save';
			// 	jQuery.ajax({
   //                  url:     	url,
   //                  type:     	"POST",
   //                  data: 		data,
	  //               success: function(response) {
	  //               	$('#' + result_id).html($(response).find('#' + result_id).html());
	  //               },
		 //            error: function(response) {
		 //                document.getElementById(result_id).innerHTML = "Error pri send form";
	  //               }
   //      		});
			// };

			var VOptionsAddGroup = function(id, params)
			{
				id = id || false;
				params = params || false;

				if(!id && !params)
					return false;

				var count = parseInt($('#' + id).val());
				if(!count)
					count = 1;

				var container = $('#' + id + '_container');
				var group = container.children(':first').clone();
				var binds = [];
				$.each(params, function(param, _default){
					var p_id = param.replace(/\[|\]/g, '-');
					var find = group.find('#'+p_id+'-0-');
					if(find.length == 0)
						return;

					console.log(find);
					find.attr('id', p_id+'-'+count+'-').attr('name', param+'['+count+']').val(_default);

					var bind = find.attr('bind');
					if(bind != undefined && bind != '')
					{
						bind = bind.replace(/(-[\d]+-)$/, '-' + count + '-');
						eval('bind_values=' + find.attr('bind_values'));
						binds.push([bind, find.attr('id'), bind_values]);
					}
				});

				count++;
				container.append(group);
				$('#'+id).val(count);

				$.each(binds, function(i, bind){
					VOptionsBind(bind[0], bind[1], bind[2]);
				});
			}
		</script>
		<?
		$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/js/vettich.autoposting/VOptions.js');
		// $GLOBALS['APPLICATION']->AddHeadScript('/bitrix/js/vettich.autoposting/jquery.meio.mask.js');
		$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/js/vettich.autoposting/jquery.maskedinput.min.js');
		if(isset($arIncludeJS))
			foreach($arIncludeJS as $js_file)
			{
				$GLOBALS['APPLICATION']->AddHeadScript($js_file);
			}
	}

	function show()
	{
		global $APPLICATION;
		if($_REQUEST['voptions_ajax'] == 'Y')
			 $APPLICATION->RestartBuffer();

		$tbCtrl = 'tabControl';
		if(!empty($this->arModuleParams['TAB_CONTROL_POSTFIX']))
			$tbCtrl .= $this->arModuleParams['TAB_CONTROL_POSTFIX'];
		$tabControl = new CAdminTabControl($tbCtrl, $this->arTabs, true, true);
		$tabControl->Begin();

		$required_is = false;
		$refresh_is = false;

		$button_save = 'Save';
		$button_apply = 'Apply';
		$button_restore_defaults = 'Restore Defaults';
		$button_back = 'Cancel';
		if(isset($this->arModuleParams['BUTTONS']['SAVE']))
		{
			$but_s = $this->arModuleParams['BUTTONS']['SAVE'];
			if(isset($but_s['ENABLE']) && $but_s['ENABLE'] != 'Y')
			{
				$button_save = false;
			}
			elseif(isset($but_s['NAME']))
			{
				$button_save = $but_s['NAME'];
			}
		}
		if(isset($this->arModuleParams['BUTTONS']['APPLY']))
		{
			$but_s = $this->arModuleParams['BUTTONS']['APPLY'];
			if(isset($but_s['ENABLE']) && $but_s['ENABLE'] != 'Y')
			{
				$button_apply = false;
			}
			elseif(isset($but_s['NAME']))
			{
				$button_apply = $but_s['NAME'];
			}
		}
		if(isset($this->arModuleParams['BUTTONS']['RESTORE_DEFAULTS']))
		{
			$but_s = $this->arModuleParams['BUTTONS']['RESTORE_DEFAULTS'];
			if(isset($but_s['ENABLE']) && $but_s['ENABLE'] != 'Y')
			{
				$button_restore_defaults = false;
			}
			elseif(isset($but_s['NAME']))
			{
				$button_restore_defaults = $but_s['NAME'];
			}
		}
		if(isset($this->arModuleParams['BUTTONS']['BACK']))
		{
			$but_s = $this->arModuleParams['BUTTONS']['BACK'];
			if(isset($but_s['ENABLE']) && $but_s['ENABLE'] == 'Y')
			{
				$button_back = false;
			}
			elseif(isset($but_s['NAME']))
			{
				$button_back = $but_s['NAME'];
			}
		}

		$arTabParams = array();
		foreach($this->arTabs as $key=>$arTab)
			$arTabParams[$arTab['DIV']] = array();
		$sort_index = 500;
		// self::debugg($this->arParams);
		foreach($this->arParams as $key=>$arParam)
		{
			if(!isset($arParam['SORT']))
			{
				$arParam['SORT'] = $sort_index;
			}
			$sort_index = (int)$arParam['SORT'] + 10;

			$arTabParams[$arParam['TAB']][$key] = $arParam;
		}

		$this->_sortTabParams($arTabParams);

		$this->showCSS();
		$this->showJS(array(
			'tabControlName' => $tbCtrl,
		));

		$form_url = '';
		if(isset($this->arModuleParams['FORM']['URL']))
			$form_url = $this->arModuleParams['FORM']['URL'];
		else
			$form_url = $APPLICATION->GetCurPage();

		if(!is_array($this->arModuleParams['FORM']['TYPE'])
			or !in_array('NOT_DEFAULT', $this->arModuleParams['FORM']['TYPE']))
		{
			if(strpos($form_url, '?') === false)
				$form_url .= '?';
			else
				$form_url .= '&';
			$form_url .= "lang=".LANGUAGE_ID."&mid=".urlencode($this->moduleID)."&mid_menu=1";
		}

		if(is_array($this->arModuleParams['FORM']['TYPE'])
			&& in_array('WITH_PARAMS', $this->arModuleParams['FORM']['TYPE']))
		{
			$amp = '&';
			if(strpos($form_url, '?') === false)
				$amp = '?';
			foreach($this->arModuleParams['FORM']['PARAMS'] as $k=>$v)
			{
				$form_url .= $amp.$k.'='.$v;
				if($amp != '&')
					$amp = '&';
			}
		}
		?>
		<div id='<?=$this->formContainerID?>'>
			<form method="post" action="<?=$form_url?>" id="<?=$this->formID?>" enctype="multipart/form-data">
			<?foreach($arTabParams as $aTab)
			{
				$tabControl->BeginNextTab();
				foreach($aTab as $name => $arOption)
				{
					$this->showOption($name, $arOption);

					if(!$required_is && isset($arOption['REQUIRED']) && $arOption['REQUIRED'] == 'Y')
						$required_is = true;

					if(!$refresh_is && isset($arOption['REFRESH']) && $arOption['REFRESH'] == 'Y')
						$refresh_is = true;
				}
			}
			if(strtolower($arOption['DISPLAY']) == 'inline')
			{
				?></tr></table></td></tr><?
			}

			/////////////////
			// for note area
			////////////////
			/*if($required_is)
			{
				?><tr><td colspan="2"><?
				echo BeginNote();
				echo "* - required option";
				echo EndNote();
				?></td></tr><?
			}*/
			/////////////////

			$tabControl->Buttons();
				?>
				<?if($button_save !== false):?>
					<input type="submit" name="Save" value="<?=$button_save?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>" class="adm-btn-save">
				<?endif?>

				<?if($button_apply !== false):?>
					<input type="submit" name="Apply" value="<?=$button_apply?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
				<?endif?>

				<input type='hidden' name='VOPTIONS_SUBMIT' id="VOPTIONS_SUBMIT" value='Y'>
				<?if($button_back !== false && strlen($_REQUEST["back_url_settings"])>0):?>
					<input type="button" name="Cancel" value="<?=$button_back?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
					<input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST["back_url_settings"])?>">
				<?endif?>

				<?if($button_restore_defaults !== false):?>
					<input type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="return confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?=$button_restore_defaults?>">
				<?endif?>
				<?=bitrix_sessid_post();?>
			<?$tabControl->End();?>
			</form>
		</div>
		<?
	}

	function showOption($name, $arOption, $arGroup = array())
	{
		global $APPLICATION;
		static $display_type;
		if(empty($arOption))
			return;

		if (isset($_POST[$name])
			&& !$this->isRestoreDefaults
			/*&& !($arOption['REQUEST_VALUE_NOT_USE_IS_EMPTY'] == 'Y')
				&& empty($_POST[$name])*/)
		{
			$val = $_POST[$name];
		}
		elseif(isset($arOption['VALUE']))
			$val = $arOption['VALUE'];
		elseif(isset($arOption['DEFAULT']))
			$val = self::get($name, $arOption['DEFAULT']);
		else
			$val = self::get($name);

		$type = strtolower($arOption['TYPE']);

		$elem_id = str_replace(array('][', '['), $this->group_separator, $name);
		$elem_id = str_replace(']', '', $elem_id);

		$is_multiple = (isset($arOption['MULTIPLE']) && $arOption['MULTIPLE'] == 'Y');
		if($is_multiple && !is_array($val))
			$val = unserialize($val);

		$display = 'block';
		if(isset($arOption['DISPLAY']))
		{
			$arOption['DISPLAY'] = strtolower($arOption['DISPLAY']);
			if($arOption['DISPLAY'] == 'inline')
				$display = $arOption['DISPLAY'];
		}

		$required = (isset($arOption['REQUIRED']) && $arOption['REQUIRED']=='Y')? 'required' : '';
		$disabled = (isset($arOption['DISABLED']) && $arOption['DISABLED']=='Y')? 'disabled="disabled"' : '';
		$readonly = (isset($arOption['READONLY']) && $arOption['READONLY']=='Y')? 'readonly="readonly"' : '';
		$extOptions = $required .' '. $disabled .' '. $readonly;


		if(isset($arOption['BIND']) && isset($arOption['BIND_VALUES']) && !empty($arOption['BIND']))
		{
			if(is_array($arOption['BIND']))
			{
				$arOption['BIND_ARRAY'] = $arOption['BIND'];

				$bind = '';
				foreach($arOption['BIND'] as $k=>$v)
				{
					if(isset($arGroup[$k]) && $arGroup[$k][0] == $v)
					{
						$bind .= $v .$this->group_separator. $arGroup[$k][1] .$this->group_separator;
						if($optname == '')
						{
							$optname = $v.'['.$arGroup[$k][1].']';
						}
						else
						{
							$optname .= '['.$v.']['.$arGroup[$k][1].']';
						}
					}
					else
					{
						$bind .= $v;
						if($optname == '')
						{
							$optname = $v;
						}
						else
						{
							$optname .= '['.$v.']';
						}
						break;
					}
				}
				$arOption['BIND_NAME'] = $optname;
				$arOption['BIND'] = $bind;
			}
			$optvalues = $this->get($arOption['BIND_NAME']);
			if($optvalues && isset($arOption['BIND_VALUES'][$optvalues]))
			{
				$arOption['VALUES'] = $arOption['BIND_VALUES'][$optvalues];
			}
			// foreach($arOption['BIND_VALUES'] as $k => $v)
			// {
			// 	if(is_array($v))
			// 	{
			// 		if(isset($v[$val]))
			// 		{
			// 			$arOption['VALUES'] = $v;
			// 			break;
			// 		}
			// 		elseif(is_array($val))
			// 		{
			// 			$flag = true;
			// 			foreach($val as $v2)
			// 			{
			// 				if(!isset($v[$v2]))
			// 				{
			// 					$flag = false;
			// 					break;
			// 				}
			// 			}
			// 			if($flag)
			// 			{
			// 				$arOption['VALUES'] = $v;
			// 				break;
			// 			}
			// 		}
			// 	}
			// 	if($k == $val)
			// 	{
			// 		$arOption['VALUES'] = array($k => $v);
			// 		break;
			// 	}
			// }

			$bind = 'bind="'. $arOption['BIND'] .'"';
			// $bind_values = "data-bind-values='". $this->bindValues($arOption['BIND_VALUES']) ."'";
			$bind_values = "data-bind-values='". self::_json_encode($arOption['BIND_VALUES']) ."'";

			$extOptions .= ' '. $bind .' '. $bind_values;
		}

		$refresh = false;

		if(isset($arOption['REFRESH']) && $arOption['REFRESH'] == 'Y')
			$refresh = true;

		if(!$refresh_is && $refresh)
			$refresh_is = true;

		if($required != '')
			$required_is = true;

		if(!$this->html5)
		{
			$html_tag_replace = array(
				'color',
				'date',
				'number',
				'email',
			);
			$type = str_replace($html_tag_replace, 'text', $type);
		}

		if($display_type != $display)
		{
			if($display_type == 'inline')
			{
				?></tr></table></td></tr><?
			}
			if($display == 'block')
			{
				?><tr><?
			}
			if($display == 'inline')
			{
				?><tr><td colspan="2"><table><tr><?
			}
		}

		if($type=='hidden')
		{
			?>
			<td>
				<input type='hidden' name='<?=htmlspecialcharsbx($name)?>' value="<?=htmlspecialcharsbx($val)?>" id="<?=$elem_id?>"/>
			</td>
			<?
		}
		elseif($type=='group')
		{
			?>
			<td colspan="2" >
				<?
				$val['count'] = intval($val['count']);
				if($val['count'] <= 0)
					$val['count'] = 1;

				$group_name = preg_replace('/^[\w-.]+-/', '', $elem_id);
				$group_name = preg_replace('/-\d+-/', '', $group_name);
				?>
				<input type="group" name="<?=htmlspecialcharsbx($name)?>[count]" id="<?=$elem_id?>" value="<?=intval(htmlspecialcharsbx($val['count']))?>">
				<div class="voptions-group-title">
					<?=$arOption['NAME'];?>
				</div>
				<?if(isset($arOption['DESCRIPTION'])):?>
					<div class="voptions-group-description">
						<?=$arOption['DESCRIPTION']?>
					</div>
				<?endif?>
				<?$this->_sortArParams($arOption['VALUES']);?>
				<div id="<?=$elem_id?>_container" class="voptions-group-container">
					<?for($i=0; $i<$val['count']; $i++):?>
						<?$_arGroup = $arGroup + array(array($group_name, $i));?>
						<div class="voptions-group-div" rel="<?=$i?>">
						<table class="voptions-group-table">
							<?foreach($arOption['VALUES'] as $grname => $grOption):
								// self::debugg($grOption, 'voptions');
								// if(isset($grOption['BIND']) && isset($grOption['BIND_VALUES']))
								// {
								// 	if(array_key_exists($grOption['BIND'], $arOption['VALUES']))
								// 	{
								// 		$grOption['BIND'] = $name.$this->group_separator.$grOption['BIND'] ."-$i-";
								// 	}
								// }
								$grOption['VALUE'] = $val[$i][$grname];
								$this->showOption($name."[$i][".$grname."]", $grOption, $_arGroup);
							endforeach;
							if($grOption['DISPLAY'] == 'inline'):
								?></tr></table></td></tr><?
								$display_type = 'block';
							endif;?>
						</table>
						</div>
					<?endfor;?>
				</div>
				<?
				$arGroup[] = $group_name;

				$jsoptions = '{';
				foreach($arOption['VALUES'] as $key => $value)
					$jsoptions .= "'"/*.$name.'_'*/.$key."':'".$value['DEFAULT']."',";
				$jsoptions .= '}';
				?>
				<div class="voptions-add-button" onclick='VOptionsAddGroupParams(<?=self::_json_encode($arGroup)?>);'>
					<?=(isset($arOption['ADDBUTTON']) && !empty($arOption['ADDBUTTON'])) ? $arOption['ADDBUTTON'] : GetMessage('ADDBUTTON');?>
				</div>
			</td>
			<?
		}
		elseif($type=='note')
		{
			?>
			<td colspan="2" align="center" class="voptions-description"><?
			if(isset($arOption['NAME']) && !empty($arOption['NAME']))
			{
				echo $arOption['NAME'], '<br/><br/>';
			}
			echo $arOption['TEXT'];
			?></td><?
		}
		else
		{
			?>
			<?if($display != 'inline' or !empty($arOption['NAME'])):?>
				<td width="20%" class="adm-detail-content-cell-l adm-detail-valign-top">
					<label for="<?=htmlspecialcharsbx($name)?>"><?=$arOption['NAME']?><?=$required != ''?'*':''?></label>
					<?if(!empty($arOption['HELP'])):?>
						<div class="voptions-help">
							<div class="voptions-help-btn">
							</div>
							<div class="voptions-help-text"><?=$arOption['HELP']?></div>
						</div>
					<?endif;?>
				</td>
			<?endif?>
				<td class="adm-detail-content-cell-r">
					<?if($type=="checkbox"):?>
						<input type="hidden" name="<?=htmlspecialcharsbx($name)?>" value="N">
						<input type="checkbox" name="<?=htmlspecialcharsbx($name)?>" id="<?=$elem_id?>" <?=$extOptions?> value="Y"<?if($val=="Y")echo" checked";?> <?=$refresh?'onchange="VOptionRefresh();"':''?>>

					<?elseif($type=="text" or $type=="string"):?>
						<?$maxlength = (isset($arOption['MAXLENGTH'])? $arOption['MAXLENGTH'] : '255');?>
						<?$size = (isset($arOption['SIZE'])? $arOption['SIZE'] : '80');?>
						<?$placeholder = (!empty($arOption['PLACEHOLDER'])? 'placeholder="'.$arOption['PLACEHOLDER'].'"' : '');?>
						<input type="text" size="<?=$size?>" maxlength="<?=$maxlength?>" value="<?=htmlspecialcharsbx($val)?>" name="<?=htmlspecialcharsbx($name)?>" <?=$placeholder?> id="<?=$elem_id?>" <?=$extOptions?>>
						<?if($refresh):?>
							<button onclick="VOptionRefresh();">OK</button>
						<?endif?>

					<?elseif($type=="number"):?>
						<?$maxlength = (isset($arOption['MAXLENGTH'])? $arOption['MAXLENGTH'] : '255');?>
						<?$size = (isset($arOption['SIZE'])? $arOption['SIZE'] : '80');?>
						<?$min = (isset($arOption['MIN'])? $arOption['MIN'] : '0');?>
						<?$max = (isset($arOption['MAX'])? $arOption['MAX'] : '');?>
						<?$step = (isset($arOption['STEP'])? $arOption['STEP'] : '');?>
						<input type="number" size="<?=$size?>" maxlength="<?=$maxlength?>" min="<?=$min?>" max="<?=$max?>" step="<?=$step?>" value="<?=htmlspecialcharsbx($val)?>" name="<?=htmlspecialcharsbx($name)?>" id="<?=$elem_id?>" <?=$extOptions?>>
						<?if($refresh):?>
							<button onclick="VOptionRefresh();">OK</button>
						<?endif?>

					<?elseif($type=="password"):?>
						<?$maxlength = (isset($arOption['MAXLENGTH'])? $arOption['MAXLENGTH'] : '255');?>
						<?$size = (isset($arOption['SIZE'])? $arOption['SIZE'] : '80');?>
						<input type="password" size="<?=$size?>" maxlength="<?=$maxlength?>" value="<?=htmlspecialcharsbx($val)?>" name="<?=htmlspecialcharsbx($name)?>" id="<?=$elem_id?>" <?=$extOptions?>>
						<?if($refresh):?>
							<button onclick="VOptionRefresh();">OK</button>
						<?endif?>

					<?elseif($type=="email"):?>
						<?$maxlength = (isset($arOption['MAXLENGTH'])? $arOption['MAXLENGTH'] : '255');?>
						<?$size = (isset($arOption['SIZE'])? $arOption['SIZE'] : '80');?>
						<input type="email" size="<?=$size?>" maxlength="<?=$maxlength?>" value="<?echo htmlspecialcharsbx($val)?>" name="<?echo htmlspecialcharsbx($name)?>" id="<?=$elem_id?>" <?=$extOptions?>>
						<?if($refresh):?>
							<button onclick="VOptionRefresh();">OK</button>
						<?endif?>

					<?elseif($type=="color" or $type=="colorpicker"):?>
						<?$size = (isset($arOption['SIZE'])? $arOption['SIZE'] : '40')?>
						<input type="color" size="<?=$size?>" value="<?=htmlspecialcharsbx($val)?>" name="<?echo htmlspecialcharsbx($name)?>" id="<?=$elem_id?>" <?=$extOptions?>>
						<?if($refresh):?>
							<button onclick="VOptionRefresh();">OK</button>
						<?endif?>

					<?elseif($type=="date"):?>
						<?$maxlength = (isset($arOption['MAXLENGTH'])? $arOption['MAXLENGTH'] : '255');?>
						<?$size = (isset($arOption['SIZE'])? $arOption['SIZE'] : '80');?>
						<?$min = (isset($arOption['MIN'])? $arOption['MIN'] : '');?>
						<?$max = (isset($arOption['MAX'])? $arOption['MAX'] : '');?>
						<input type="date" size="<?=$size?>" maxlength="<?=$maxlength?>" min="<?=$min?>" max="<?=$max?>" value="<?echo htmlspecialcharsbx($val)?>" name="<?echo htmlspecialcharsbx($name)?>" id="<?=$elem_id?>" <?=$extOptions?>>
						<?if($refresh):?>
							<button onclick="VOptionRefresh();">OK</button>
						<?endif?>

					<?elseif($type=="time"):?>
						<?$maxlength = (isset($arOption['MAXLENGTH'])? $arOption['MAXLENGTH'] : '255');?>
						<?$size = (isset($arOption['SIZE'])? $arOption['SIZE'] : '10');?>
						<input type="time" size="<?=$size?>" maxlength="<?=$maxlength?>" value="<?echo htmlspecialcharsbx($val)?>" name="<?echo htmlspecialcharsbx($name)?>" id="<?=$elem_id?>" <?=$extOptions?>>
						<?if($refresh):?>
							<button onclick="VOptionRefresh();">OK</button>
						<?endif?>
						<script type="text/javascript">
						$(document).ready(function(){
							// $("#<?=$elem_id?>").setMask('time').val('<?=$val?>');
							$("#<?=$elem_id?>").mask("99:99");
						})
						</script>
						<?//$APPLICATION->AddHeadScript('https://raw.githubusercontent.com/fabiomcosta/jquery-meiomask/08b92c411a60ace9131de697179fda433178b8d9/jquery.meio.mask.js')?>
						<?$APPLICATION->AddHeadScript('/bitrix/js/vettich.autoposting/jquery.maskedinput.min.js')?>

					<?elseif($type=="calendar"):?>
						<?echo CAdminCalendar::CalendarDate($name, $val, 19, true)?>
						<?if($refresh):?>
							<button onclick="VOptionRefresh();">OK</button>
						<?endif?>

					<?elseif($type=="textarea"):?>
						<?$cols = (isset($arOption['COLS'])? $arOption['COLS'] : '80')?>
						<?$rows = (isset($arOption['ROWS'])? $arOption['ROWS'] : '10')?>
						<textarea rows="<?=$rows?>" cols="<?=$cols?>" name="<?=htmlspecialcharsbx($name)?>" id="<?=$elem_id?>" <?=$extOptions?>><?=htmlspecialcharsbx($val)?></textarea>
						<?if($refresh):?>
							<button onclick="VOptionRefresh();">OK</button>
						<?endif?>
						<?if(isset($arOption['CHOISE']) && isset($arOption['VALUES'])):?>
							<?switch(strtoupper($arOption['CHOISE']))
							{
								case 'SIMPLE':
									?>
									<br/>
									<input type="checkbox" class="voptions-textarea-choise-checkbox" id="<?=$elem_id?>_choise_checkbox">
									<label class="voptions-textarea-choise-checkbox-label" for="<?=$elem_id?>_choise_checkbox"><?=GetMessage('TEXTAREA_SHOW_CHOISE')?></label>
									<div id="<?=$elem_id?>_choise" class="voptions-textarea-choise">
									<?
									foreach($arOption['VALUES'] as $key=>$value)
									{
										?>
										<div class="voptions-textarea-choise-simple">
											<a href="javascript:undefined" onclick="voptions_textarea_choise('<?=$elem_id?>', '#<?=$key?>#')">
												<?=$value?>
											</a>
										</div>
										<?
									}
									?>
									</div>
									<?
									break;

								case 'BX_MENU':
									$param = array();
									foreach($arOption['VALUES'] as $key=>$value)
									{
										$param[] = (object)array(
											'ONCLICK' => "voptions_textarea_choise('".$elem_id."', '#".$key."#')",
											'TITLE' => $value,
											'TEXT' => $value,
										);
									}
									?>
									<br/>
									<?//VOptions::debugg($arOption['VALUES'])?>
									<script type="text/javascript">
										function VOptions_<?=$elem_id?>_onclick(elem)
										{BX.adminShowMenu(elem, <?=self::_json_encode($param)?>, "");}
									</script>
									<input type="button" value="..." onclick='VOptions_<?=$elem_id?>_onclick(this)'>
									<?
									break;
							}?>
						<?endif;?>
					<?elseif($type=="select" or $type=="list"):?>
						<?$multiple = (isset($arOption['MULTIPLE']) && $arOption['MULTIPLE']=='Y')? 'multiple="multiple"' : '';?>
						<?if(isset($arOption['SIZE']))
							$size = $arOption['SIZE'];
						elseif($multiple)
						{
							if(count($arOption['VALUES'])<10)
								$size = count($arOption['VALUES'])?:1;
							else
								$size = 10;
						}
						else
							$size = 1;
						?>
						<select <?=$multiple?> size="<?=$size?>" name="<?=htmlspecialcharsbx($name)?><?=$multiple != ''? '[]': ''?>" id="<?=$elem_id?>" <?=$extOptions?> <?=$refresh?'onchange="VOptionRefresh();"':''?>>
						<?if(!$is_multiple && !is_array($val))
							$val = array($val);
						foreach($arOption['VALUES'] as $key => $value):
							$selected = in_array($key, $val) ? ' selected' : ''?>
							<option value="<?=htmlspecialcharsbx($key)?>"<?=$selected?>><?=htmlspecialcharsbx(htmlspecialcharsback($value))?></option>
						<?endforeach?>
						</select>

					<?elseif($type=="checkboxlist"):?>
						<?if(!is_array($val)) $val = array($val);?>
						<?foreach($arOption['VALUES'] as $key => $value):?>
							<?$ischecked = in_array($key, $val);?>
							<div>
								<input type="checkbox" name="<?=htmlspecialcharsbx($name)?>[]" id="<?=$elem_id.$key?>" <?=$extOptions?> value="<?=htmlspecialcharsbx($key)?>"<?if($ischecked)echo" checked";?> <?=$refresh?'onchange="VOptionRefresh();"':''?>>
								<label for="<?=$elem_id.$key?>"><?=$value?></label>
							</div>
						<?endforeach?>

					<?elseif($type=="radio"):?>
						<?foreach($arOption['VALUES'] as $key => $value):?>
							<?$checked = ($key == $val)?>
							<div>
								<input type="radio" name="<?=htmlspecialcharsbx($name)?>" id="<?=$elem_id.$key?>" value="<?=htmlspecialcharsbx($key)?>" <?if ($checked) echo ' checked="checked"'?> <?=$required?'required':''?> <?=$refresh?'onchange="VOptionRefresh();"':''?> <?if($disabled)echo 'disabled';?>/>
								<label for="<?=$elem_id.$key?>"><?=htmlspecialcharsEx($value)?></label>
							</div>
						<?endforeach?>
					<?elseif($type == 'file'):?>
						<?
						$fsize = (isset($arOption['FILE_SIZE'])? $arOption['FILE_SIZE'] : '');
						$multiple = (isset($arOption['MULTIPLE']) ? $arOption['MULTIPLE'] : 'Y');
						$ftype = (isset($arOption['FILE_TYPE']) ? $arOption['FILE_TYPE'] : 'A');
						$ftypeext = (isset($arOption['FILE_TYPE_EXT']) ? $arOption['FILE_TYPE_EXT'] : '');
						if($multiple=='Y')
							$fvalues = /*unserialize*/($val);
						else
							$fvalues = array($val);

						// $APPLICATION->IncludeComponent("bitrix:main.file.input", "drag_n_drop",
						//    array(
						//       "INPUT_NAME" => htmlspecialcharsbx($name),
						//       "MULTIPLE" => $multiple,
						//       "MODULE_ID" => $this->moduleID,
						//       "MAX_FILE_SIZE" => $fsize,
						//       "ALLOW_UPLOAD" => $ftype, 
						//       "ALLOW_UPLOAD_EXT" => $ftypeext,
						//       'INPUT_VALUE' => $fvalues,
						//    ),
						//    false
						// );
						?>
						<input type="file" name="<?=$name?>">
					<?elseif($type == 'custom'):?>
						<?=$arOption['HTML']?>
					<?endif?>

					<?if(isset($arOption['DESCRIPTION']) && !empty($arOption['DESCRIPTION'])):?>
						<div class="voptions-description">
							<?=$arOption['DESCRIPTION'];?>
						</div>
					<?endif?>

					<?
					if(isset($arOption['BIND']) && isset($arOption['BIND_VALUES']) && !empty($arOption['BIND']))
					{
						?>
						<script type="text/javascript">
							VOptionsBind("<?=$arOption['BIND']?>", "<?=$elem_id?>");
						</script>
						<?
					}
					?>
				</td>
			<?
		}
		if($display == 'block')
		{
			?></tr><?
		}
		$display_type = $display;
	}

	function bindValues($arValues)
	{
		if(empty($arValues))
			return '';

		$return = "{";
		foreach($arValues as $key=>$value)
		{
			$return .= '"'. $key .'":';
			if(is_array($value))
				$return .= $this->bindValues($value);
			else
				$return .= '"'. $value .'"';
			$return .= ",";
		}
		$return .= "}";

		return $return;
	}

	function saveOptions($arParams = false, $prefix = '')
	{
		if(!$this->isSubmitForm or $this->isRestoreDefaults)
		{
			return;
		}

		if($this->moduleID == '')
		{
			return;
		}

		if($arParams === false)
			$arParams = $this->arParams;

		foreach($arParams as $optname => $arOption)
		{
			$this->saveOption($optname, $arOption);
		}
	}

	function saveOption($optname, $arOption, $_post = false, $exArg = array())
	{
		if(empty($arOption))
			return;

		$result = false;

		if($_post === false)
			$_post = $_POST;

		$val = isset($_post[$optname]) ? $_post[$optname] : '';

		$optname_orig = $optname;
		if(!empty($exArg))
		{
			$optname = '';
			foreach($exArg as $arr)
			{
				if($optname == '')
				{
					$optname = $arr[0].'['.$arr[1].']';
				}
				else
				{
					$optname .= '['.$arr[0].']['.$arr[1].']';
				}
			}
			if(empty($optname))
			{
				$optname = $optname_orig;
			}
			else
			{
				$optname .= '['.$optname_orig.']';
			}
		}

		$type = strtolower($arOption['TYPE']);
		
		if($type == 'group')
		{
			$cnt = $val['count'];
			$values = array();

			// сохраняем список параметров группы
			$params = array();
			foreach($arOption['VALUES'] as $key => $option)
			{
				$params[] = $key;
			}
			self::set($optname.'[params]', self::_json_encode($params));

			for($i = 0; $i < $cnt; $i++)
			{
				$is_required = false;
				$is_required_change = true;
				$is_change = false;
				$values = $val[$i];
				foreach($arOption['VALUES'] as $key => $option)
				{
					if($option['TYPE'] == 'GROUP')
					{
						$is_change = $this->saveOption(
							$key, 
							$option, 
							array($key => $_post[$optname_orig][$i][$key]), 
							$exArg + array(array($optname_orig, $i))
						);
						continue;
					}

					if(!isset($values[$key]))
						$values[$key] = false;
					elseif(is_array($values[$key]))
						$values[$key] = serialize($values[$key]);

					$is_required = false;
					if(isset($option['REQUIRED']) && ($option['REQUIRED'] == 'Y' or $option['REQUIRED'] == 'R'))
					{
						$is_required = true;
					}

					if(isset($option['DEFAULT']))
					{
						if($values[$key] != $option['DEFAULT'] && $values[$key] != '')
							$is_change = true;
					}
					elseif($values[$key] != '')
					{
						$is_change = true;
					}
					elseif($is_required)
					{
						$is_change = false;
						break;
					}
				}

				if($is_change)
				{
					foreach($arOption['VALUES'] as $key => $option)
					{
						if($option['TYPE'] != 'GROUP')
						{
							self::set($optname.'['.$i.']['.$key.']', $values[$key]);
						}
					}
				}
				else
				{
					foreach($arOption['VALUES'] as $key => $option)
					{
						self::del($optname.'['.$i.']['.$key.']');
					}
					$val['count']--;
				}
			}
			if($val['count'] > 0)
				$result = true;

			$val = $val['count'];
			$this->setCurrentValue($optname.'[count]', $val);
			self::set($optname, $val);
		}
		elseif($type == 'checkbox' && $val != 'Y')
		{
			$val = 'N';
		}

		$this->setCurrentValue($optname, $val);
		self::set($optname, $val);

		return $result;
	}

	function parseRequest($skip_is_parse_request = false)
	{
		if($skip_is_parse_request && $this->is_parse_request)
			return;

		$this->is_parse_request = true;

		if(strlen($_REQUEST['RestoreDefaults'])>0)
		{
			$this->isRestoreDefaults = true;
			COption::RemoveOption($this->moduleID);
		}

		$this->saveOptions();
	}

	function _empty($var)
	{
		if(is_array($var) && count($var)==0)
			return true;
		if($var == '' or $var == NULL)
			return true;
		return false;
	}

	function debugg($param, $filename=false, $backtrace=false)
	{
		define('DEBUG', true);
		self::debug($param, $filename, $backtrace);
	}

	function debug($param, $filename=false, $backtrace=false)
	{
		if(defined('DEBUG') && !!DEBUG)
		{
			if(is_array($param) or is_object($param))
				$message = print_r($param, true);
			else
				$message = $param;
			if($backtrace)
				$message = print_r(array('debug_backtrace'=>debug_backtrace(), 'message'=>$message), true); 
			if($filename !== false)
			{
				if(!is_dir($debugPath = $_SERVER['DOCUMENT_ROOT'].'/debug/'))
					@mkdir($debugPath, 0775);
				error_log('<pre>'.date('Y/m/d H:i:s')."\n".$message.'</pre>' . "\n", 3, $debugPath.$filename.'.html');
			}
			else
			{
				?>
				<pre>
				<?
				if(is_array($param) or is_object($param))
				{
					print_r($param);
				}
				else
				{
					print($param);
				}
				?>
				</pre>
				<?
			}
		}
	}

	function get($opt_name, $default='', $_module_id=false, $from_post=false)
	{
		global $module_id;
		if(!$_module_id)
			$_module_id = $module_id;
		if(!isset($_module_id))
			return false;

		if($from_post)
			return isset($_POST[$opt_name])? $_POST[$opt_name] : COption::GetOptionString($_module_id, $opt_name, $default);

		return COption::GetOptionString($_module_id, $opt_name, $default);
	}

	function getGroup($group_name, $opt_name, $index=0, $_module_id=false, $from_post=false)
	{
		$sParam = $group_name .'['. $index .']'.(!empty($opt_name)?'['. $opt_name .']':'');
		if(!$from_post)
			return self::get($sParam, '', $_module_id, $from_post);

		if(isset($_POST[$group_name][$index][$opt_name]))
			return $_POST[$group_name][$index][$opt_name];
		elseif(empty($opt_name) && isset($_POST[$group_name][$index]))
			return $_POST[$group_name][$index];
		
		return self::get($sParam, '', $_module_id);
	}

	function getValues($opt_name, $index=-1, $default ='', $_module_id=false, $from_post=false)
	{
		$return = array();
		if(self::get($opt_name.'[params]', false, false, $_module_id, $from_post) !== false)
		{
			$params = self::_json_decode(self::get($opt_name.'[params]', false, false, $_module_id, $from_post));
			if($index<=0)
			{
				$cnt = self::get($opt_name, 0);
				for($i=1; $i<=$cnt; $i++)
				{
					foreach($params as $param)
					{
						$return[$i][$param] = self::getValues($opt_name."[$i][$param]", $default, $_module_id, $from_post);
					}
				}
			}
			else
			{
				foreach($params as $param)
				{
					$return[$param] = self::getValues($opt_name."[$index][$param]", $default, $_module_id, $from_post);
				}
			}
		}
		else
			return self::get($opt_name, $default, $_module_id, $from_post);
		return $return;
	}

	function set($opt_name, $value, $_module_id=false)
	{
		global $module_id;
		if(!$_module_id)
			$_module_id = $module_id;
		if(!isset($_module_id))
			return;

		if(is_array($value) or is_object($value))
			$value = serialize($value);

		COption::SetOptionString($_module_id, $opt_name, $value);
	}

	function del($opt_name, $_module_id=false)
	{
		global $module_id;
		if(!$_module_id)
			$_module_id = $module_id;
		if(!isset($_module_id))
			return;
		COption::RemoveOption($_module_id, $opt_name);
	}

	function showAllOptions($echo = true, $arParams = array())
	{
		if(empty($arParams))
		{
			$arParams = $this->$arParams;
		}

		$arResult = array();
		foreach($this->arParams as $key => $arParam)
		{
			$ar = $this->showOneOption($key, $arParam);
			$arResult[$key] = $ar[$key];
		}

		if($echo)
			$this->debug($arResult);
		return $arResult;
	}

	function showOneOption($optname, $arOption, $exArg = array())
	{
		if(empty($arOption))
			return NULL;

		$optname_orig = $optname;
		if(!empty($exArg))
		{
			$optname = '';
			foreach($exArg as $arr)
			{
				if($optname == '')
				{
					$optname += $arr[0].'['.$arr[1].']';
				}
				else
				{
					$optname += '['.$arr[0].']['.$arr[1].']';
				}
			}
			if($optname != '')
			{
				$optname += '['.$optname_orig.']';
			}
		}

		if($arOption['TYPE'] == 'GROUP')
		{
			$arReturn[$optname]['count'] = intval($this->get($optname));
			for($i=0; $i < $arReturn[$optname]['VALUE']; $i++)
			{
				foreach($arOption['VALUES'] as $key => $option)
				{
					$ar = $this->showOneOption($key, $option, $exArg + array(array($optname, $i)));
					$arReturn[$optname.'['.$i.']['.$key.']']['VALUES'][$i][$key.'['.$i.']'] = $ar[$optname .'_'. $key .'['.$i.']'];
				}
			}
			return $arReturn;
		}

		return array($optname => $this->get($optname));
	}

	function _json_encode($data, $options=null)
	{
		$data = \Bitrix\Main\Text\Encoding::convertEncodingArray($data, SITE_CHARSET, 'UTF-8');
		return json_encode($data, $options);
	}

	function _json_decode($data, $assoc = false)
	{
		$result = json_decode($data, $assoc);
		return \Bitrix\Main\Text\Encoding::convertEncodingArray($result, 'UTF-8', SITE_CHARSET);
	}
}

?>