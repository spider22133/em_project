<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


global $USER_FIELD_MANAGER;

if(!CModule::IncludeModule("iblock"))
	return;

$boolCatalog = CModule::IncludeModule("catalog");

$arSort = array(
	'ID' => GetMessage('IBLOCK_SORT_ID'),
	'UF_ELEMENT_ID' => GetMessage('IBLOCK_SORT_NAME'),
	'UF_DATE_INSERT' => GetMessage('IBLOCK_SORT_DATE'),
);

$arPrice = array();
if ($boolCatalog)
{
	$rsPrice=CCatalogGroup::GetList($v1="sort", $v2="asc");
	while($arr=$rsPrice->Fetch()) $arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
}

$arAscDesc = array(
	"asc" => GetMessage("IBLOCK_SORT_ASC"),
	"desc" => GetMessage("IBLOCK_SORT_DESC"),
);

$arSocial = array(
	"vkontakte" => GetMessage("SOC_VK"),
	"facebook" => GetMessage("SOC_FB"),
	"odnoklassniki" => GetMessage("SOC_OK"),
	"moimir" => GetMessage("SOC_MYMIR"),
	"gplus" => GetMessage("SOC_GP"),
	"twitter" => GetMessage("SOC_TW")
);

$arComponentParameters = array(
	"GROUPS" => array(
		"PRICES" => array(
			"NAME" => GetMessage("IBLOCK_PRICES"),
		),
	),
	"PARAMETERS" => array(
		"AJAX_MODE" => array(),
		"ELEMENT_SORT_FIELD" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_FIELD"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "ID",
		),
		"ELEMENT_SORT_ORDER" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_ORDER"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "asc",
			"ADDITIONAL_VALUES" => "Y",
		),
		"AUTH_URL" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => GetMessage("IBLOCK_AUTH_URL"),
			"TYPE" => "STRING",
			"DEFAULT" => "/auth/",
		),
		"BASKET_URL" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => GetMessage("IBLOCK_BASKET_URL"),
			"TYPE" => "STRING",
			"DEFAULT" => "/personal/basket.php",
		),
		"SET_TITLE" => array(),
		"PAGE_ELEMENT_COUNT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_PAGE_ELEMENT_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "15",
		),
		"IMG_WIDTH" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_IMG_WIDTH"),
			"TYPE" => "STRING",
			"DEFAULT" => "150",
		),
		"IMG_HEIGHT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_IMG_HEIGHT"),
			"TYPE" => "STRING",
			"DEFAULT" => "150",
		),
		"SOCIAL" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("SOC_TITLE"),
			"TYPE" => "LIST",
			"VALUES" => $arSocial,
			"MULTIPLE" => "Y",
			"DEFAULT" => array("vkontakte","facebook","odnoklassniki","moimir","gplus","twitter"),
		),
		"PRICE_CODE" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("IBLOCK_PRICE_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arPrice,
		),
		"CACHE_TIME"  =>  array("DEFAULT"=>36000000),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BCS_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	),
);
CIBlockParameters::AddPagerSettings($arComponentParameters, GetMessage("T_IBLOCK_DESC_PAGER_CATALOG"), true, true);
?>