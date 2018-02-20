<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if(!function_exists('curl_init'))
{
	IncludeModuleLangFile(__FILE__);
	$APPLICATION->SetTitle(GetMessage('VCH_ADM_PREFIX_NOT_CURL_TITLE'));
	require_once ($DOCUMENT_ROOT.BX_ROOT."/modules/main/include/prolog_admin_after.php");
	?>
	<div class="adm-detail-content-item-block">
	<?=GetMessage('VCH_ADM_PREFIX_NOT_CURL');?>
	</div>
	<?
	require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
	exit;
}


CJSCore::Init('jquery');
$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/js/vettich.autoposting/iblocks.js');
$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/js/vettich.autoposting/VOptions.js');
$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/js/vettich.autoposting/fb_options.js');
$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/js/vettich.autoposting/vk_options.js');
$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/css/vettich.autoposting/VOptions.css');

CModule::IncludeModule('vettich.autoposting');

?>
<script type="text/javascript">
VCH_POSTS_AJAX_ENABLE = <?=COption::GetOptionString('vettich.autoposting', 'is_ajax_enable', 'Y') == 'Y'? 'true' : 'false'?>;
</script>
