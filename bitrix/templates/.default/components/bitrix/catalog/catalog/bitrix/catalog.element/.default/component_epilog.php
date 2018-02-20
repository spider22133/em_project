<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
use Bitrix\Main\Loader;
global $APPLICATION;
$APPLICATION->SetPageProperty('og-url', '<meta property="og:url" content="https://www.'.$_SERVER['HTTP_HOST'] .  $APPLICATION->GetCurPage(false) . '" />');
$APPLICATION->SetPageProperty('og-title', '<meta property="og:title" content="' . $arResult["NAME"] . '" />');
$APPLICATION->SetPageProperty('og-description', '<meta property="og:description" content="' . strip_tags( trim( preg_replace('/\s+/', ' ', substr( $arResult['DETAIL_TEXT'], 0, 200 )))). '" />');
$APPLICATION->SetPageProperty('og-image', '<meta property="og:image" content="https://www.'. $_SERVER['HTTP_HOST'] . $arResult['DETAIL_PICTURE']['SRC'] . '" />');
$APPLICATION->SetPageProperty('og-image-sec', '<meta property="og:image:secure_url" content="https://www.'. $_SERVER['HTTP_HOST'] . $arResult['DETAIL_PICTURE']['SRC'] . '" />');
$APPLICATION->SetPageProperty('og-image-type', '<meta property="og:image:type" content="image/jpeg" />');
$APPLICATION->SetPageProperty('og-image-height', '<meta property="og:image:height" content="'. $arResult['DETAIL_PICTURE']['HEIGHT'] . '" />');
$APPLICATION->SetPageProperty('og-image-width', '<meta property="og:image:width" content="'. $arResult['DETAIL_PICTURE']['WIDTH'] . '" />');



//$APPLICATION->AddHeadString('<meta property="og:title" content="' . $arResult["NAME"] . '" />');
//$APPLICATION->AddHeadString('<meta property="og:description" content="' . strip_tags( trim( preg_replace('/\s+/', ' ', substr( $arResult['DETAIL_TEXT'], 0, 200 )))). '" />');
//$APPLICATION->AddHeadString('<meta property="og:image" content="' . $arResult['DETAIL_PICTURE']['SRC'] . '" />');
if (isset($templateData['TEMPLATE_THEME']))
{
	$APPLICATION->SetAdditionalCSS($templateData['TEMPLATE_THEME']);
}
if (isset($templateData['TEMPLATE_LIBRARY']) && !empty($templateData['TEMPLATE_LIBRARY']))
{
	$loadCurrency = false;
	if (!empty($templateData['CURRENCIES']))
		$loadCurrency = Loader::includeModule('currency');
	CJSCore::Init($templateData['TEMPLATE_LIBRARY']);
	if ($loadCurrency)
	{
	?>
	<script type="text/javascript">
		BX.Currency.setCurrencies(<? echo $templateData['CURRENCIES']; ?>);
	</script>
<?
	}
}
if (isset($templateData['JS_OBJ']))
{
?><script type="text/javascript">
BX.ready(BX.defer(function(){
	if (!!window.<? echo $templateData['JS_OBJ']; ?>)
	{
		window.<? echo $templateData['JS_OBJ']; ?>.allowViewedCount(true);
	}
}));
</script><?
}
?>