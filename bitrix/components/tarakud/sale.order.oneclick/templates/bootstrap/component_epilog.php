<?
use \Bitrix\Main\Page\Asset;
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

Asset::getInstance()->addJs($templateFolder.'/lib/jquery-1.12.0.min.js');
Asset::getInstance()->addJs($templateFolder.'/lib/bootstrap.min.js');
Asset::getInstance()->addCss($templateFolder.'/lib/bootstrap.min.css');
Asset::getInstance()->addCss($templateFolder.'/styles.css');
?>