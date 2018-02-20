<?
IncludeModuleLangFile(__FILE__);

if (!CModule::IncludeModule('vettich.autoposting'))
{
	echo "Module \"vettich.autoposting\" not installed.<br/>";
	return;
}
$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/css/vettich.autoposting/posts.css');

$module_id = 'vettich.autoposting';

$vopt = new VOptions();

$arModuleParams = array(
	'TABS' => array(
		'TAB1' => array(
			'NAME' => GetMessage('GENERAL'),
			'TITLE' => GetMessage('GENERAL_SETTINGS'),
		)
	),
	'BUTTONS' => array(
		'SAVE' => array(
			'NAME' => GetMessage('SAVE_BUTTON'),
		),
		'APPLY' => array(
			'ENABLE' => 'N',
		),
		'RESTORE_DEFAULTS' => array(
			'ENABLE' => 'N',
		)
	),
	'PARAMS' => array(
		'is_enable' => array(
			'TAB' => 'TAB1',
			'NAME' => GetMessage('IS_ENABLE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
		),
		'is_ajax_enable' => array(
			'TAB' => 'TAB1',
			'NAME' => GetMessage('IS_AJAX_ENABLE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
			'HELP' => GetMessage('IS_AJAX_ENABLE_HELP'),
		),
		'show_empty_acc' => array(
			'TAB' => 'TAB1',
			'NAME' => GetMessage('VCH_SHOW_EMPTY_ACC'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
			'HELP' => GetMessage('VCH_SHOW_EMPTY_ACC_HELP'),
		),
		'show_accounts' => array(
			'TAB' => 'TAB1',
			'NAME' => GetMessage('VCH_SHOW_ACCOUNTS'), 
			'HELP' => GetMessage('VCH_SHOW_ACCOUNTS_HELP'),
			'TYPE' => 'CHECKBOXLIST',
			'MULTIPLE' => 'Y',
			'VALUES' => Vettich\Autoposting\PostingFunc::GetPosts4Options(),
		),
		'link_to_logs_page' => array(
			'TAB' => 'LOGS',
			'NAME' => '',
			'TYPE' => 'CUSTOM',
			'HTML' => GetMessage('link_to_logs_page'),
		),
		'is_enable_logs' => array(
			'TAB' => 'LOGS',
			'NAME' => GetMessage('IS_ENABLE_LOGS'),
			'TYPE' => 'CHECKBOX',
		),
	),
);

$posts = Vettich\Autoposting\PostingFunc::__GetPosts();
if(!empty($posts)) foreach($posts as $post)
{
	if(Vettich\Autoposting\PostingFunc::isModule($post))
	{
		$arPostFunc = Vettich\Autoposting\PostingFunc::module2($post, 'func');
		$dir = $arPostFunc::getBaseDir().'/'.$post.'/options.php';
		if(file_exists($dir))
		{
			include($dir);
			$arModuleParams = array_merge_recursive($arModuleParams, $arPostParams);
		}
	}
}

$arModuleParams['TABS']['LOGS'] = array(
	'NAME' => GetMessage('TAB_LOGS'),
	'TITLE' => GetMessage('TAB_LOGS_TITLE'),
);

Vettich\Autoposting\PostingFunc::event('OnBuildOptions', array('arModuleParams' => &$arModuleParams));
 
$vopt->init();
?>
