<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ($arParams["IS_JQUERY"] == "Y")
	CJSCore::Init(array("jquery"));

$APPLICATION->AddHeadScript($templateFolder."/fancybox/jquery.fancybox-1.3.4.pack.js");
$APPLICATION->SetAdditionalCSS($templateFolder."/fancybox/jquery.fancybox-1.3.4.css");
$APPLICATION->SetAdditionalCSS("/bitrix/css/main/bootstrap.css");
?>