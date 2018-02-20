<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$arData = array();
$arData["STATUS"] = $arResult["AJAX_STATUS"];
$arData["DATA"] = $arResult["AJAX_DATA"];
$arData["ORDER_ID"] = $arResult["AJAX_ORDER_ID"];

$APPLICATION->RestartBuffer();
echo CUtil::PhpToJSObject($arData);
define("PUBLIC_AJAX_MODE", true);
die();
?>