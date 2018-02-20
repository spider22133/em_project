<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main;

if(!Main\Loader::includeModule("iblock"))
	return;

$bShop = true;
if (!Main\Loader::includeModule("catalog") || !Main\Loader::includeModule("sale"))
	$bShop = false;
	
$arPersonType = array();
$arRersonProps = array(0 => GetMessage('SALE_ALL_PROPS'));
$arPaySystem = array();
$arDelivery = array();

if(Main\Loader::includeModule("sale"))
{
	//persontype
	if (!is_set($arCurrentValues["PERSON_TYPE"]))
		$arCurrentValues["PERSON_TYPE"] = 1;
	
	$dbPerson = CSalePersonType::GetList(array("SORT" => "ASC", "NAME" => "ASC"));
	while($arPerson = $dbPerson->GetNext())
		$arPersonType[$arPerson["ID"]] = $arPerson["NAME"]." (".$arPerson["LID"].")";
	//end persontype
	
	//person props
	$dbProp = CSaleOrderProps::GetList(
		array("SORT" => "ASC", "NAME" => "ASC"), 
		array("PERSON_TYPE_ID" => $arCurrentValues["PERSON_TYPE"], "ACTIVE" => "Y")
	);
	while($arProp = $dbProp->GetNext())
		$arRersonProps[$arProp["ID"]] = $arProp["NAME"];
	
	//paysystem
	$dbPaySystem = CSalePaySystem::GetList(
		array("SORT" => "ASC", "PSA_NAME" => "ASC"),
		array(
			"ACTIVE" => "Y",
			"PERSON_TYPE_ID" => $arCurrentValues["PERSON_TYPE"],
			"PSA_HAVE_PAYMENT" => "Y"
		)
	);
	while ($arPay = $dbPaySystem->GetNext())
		$arPaySystem[$arPay["ID"]] = "[".$arPay["ID"]."] ".$arPay["NAME"];
	//end paysystem
	
	//delivery
	$arDelivery[0] = GetMessage("DELIVERY_LIST_SHOW_NO");
	$dbDelivery = CSaleDelivery::GetList(
		array("SORT"=>"ASC", "NAME"=>"ASC"),
		array("ACTIVE" => "Y")
	);
	while ($arDeliv = $dbDelivery->GetNext())
		$arDelivery[$arDeliv["ID"]] = "[".$arDeliv["ID"]."] ".$arDeliv["NAME"];
	
	$arOrderStatus = array("XX" => GetMessage("ORDER_STATUS_NO"));
	$res = CSaleStatus::GetList(
		array("SORT"=>"ASC", "NAME"=>"ASC"),
		array("LID" => LANGUAGE_ID),
		false,
		false,
		array("ID", "NAME")
	);
	while ($arStatus = $res->Fetch())
		$arOrderStatus[$arStatus["ID"]] = $arStatus["NAME"];
}

$site = ($_REQUEST["site"] <> ''? $_REQUEST["site"] : ($_REQUEST["src_site"] <> ''? $_REQUEST["src_site"] : false));
$arFilter = array("TYPE_ID" => "SALE_NEW_ORDER", "ACTIVE" => "Y");
if($site !== false)
	$arFilter["LID"] = $site;

$arEvent = array();
$dbType = CEventMessage::GetList($by="ID", $order="DESC", $arFilter);
while($arType = $dbType->GetNext())
	$arEvent[$arType["ID"]] = "[".$arType["ID"]."] ".$arType["SUBJECT"];

	
$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arOffers = CIBlockPriceTools::GetOffersIBlock($arCurrentValues["IBLOCK_ID"]);
$OFFERS_IBLOCK_ID = is_array($arOffers)? $arOffers["OFFERS_IBLOCK_ID"]: 0;
$arProperty_Offers = array();
if(0 < $OFFERS_IBLOCK_ID)
{
	$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$OFFERS_IBLOCK_ID, "ACTIVE"=>"Y"));
	while($arr=$rsProp->Fetch())
	{
		if($arr["PROPERTY_TYPE"] != "F")
			$arProperty_Offers[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

$arPrice = array();
if (Main\Loader::includeModule("catalog"))
{
	$rsPrice = CCatalogGroup::GetList($v1="sort", $v2="asc");
	while($arr = $rsPrice->Fetch())
		$arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
}
elseif ($arCurrentValues["ORDER_PRODUCT"] <= 0)
{
	$arCurrentValues["ORDER_PRODUCT"] = 2;
}

$arComponentParameters = array(
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SALE_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SALE_IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"OFFERS_PROPERTY_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SALE_OFFERS_PROPERTY_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_Offers,
			"ADDITIONAL_VALUES" => "Y",
		),
		"OFFERS_SHOW" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SALE_OFFERS_SHOW"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => array("all"=>GetMessage("SALE_OFFERS_SHOW_ALL"), "list"=>GetMessage("SALE_OFFERS_SHOW_LIST")),
			"ADDITIONAL_VALUES" => "N",
		),
		"IS_JQUERY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SALE_JQUERY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"PERSON_TYPE" => array(
			"NAME" => GetMessage("SALE_PERSON_TYPE"),
			"TYPE"=>"LIST",
			"VALUES" => $arPersonType,
			"DEFAULT"=>"",
			"REFRESH" => "Y",
			"PARENT" => "BASE",
		),
		"PERSON_TYPE_PROPS" => array(
			"NAME" => GetMessage("SALE_PERSON_TYPE_PROP")." ".$arPersonType[$arCurrentValues["PERSON_TYPE"]],
			"TYPE"=>"LIST", 
			"MULTIPLE"=>"Y",
			"VALUES" => $arRersonProps,
			"DEFAULT"=>"0",
			"PARENT" => "BASE",
		),
		"ORDER_PRODUCT" => array(
			"NAME" => GetMessage("SALE_PRODUCT"),
			"TYPE"=>"LIST", 
			"VALUES" => array(
					1=>GetMessage("SALE_PRODUCT_BASKET"), 
					2=>GetMessage("SALE_PRODUCT_CURRENT"), 
					3=>GetMessage("SALE_PRODUCT_NEW"),
				),
			"DEFAULT"=>"",
			"REFRESH" => "Y",
			"PARENT" => "BASE",
		),
		"PAYSYSTEM" => array(
			"NAME" => GetMessage("SALE_PAYSYSTEM"),
			"TYPE"=>"LIST",
			"VALUES" => $arPaySystem,
			"DEFAULT"=>"",
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"DELIVERY" => array(
			"NAME" => GetMessage("SALE_DELIVERY"),
			"TYPE"=>"LIST",
			"VALUES" => $arDelivery,
			"DEFAULT"=>"",
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"ORDER_STATUS" => array(
			"NAME" => GetMessage("SALE_ORDER_STATUS"),
			"TYPE"=>"LIST",
			"VALUES" => $arOrderStatus,
			"DEFAULT"=>"",
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"DELIVERY_SHOW" => array(
			"NAME" => GetMessage("SALE_DELIVERY_SHOW"),
			"TYPE"=>"CHECKBOX",
			"VALUES" => "N",
			"DEFAULT"=>"",
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"USE_USER" => array(
			"NAME" => GetMessage("SALE_USE_USER"),
			"TYPE"=>"CHECKBOX",
			"VALUES" => "N",
			"DEFAULT"=>"",
			"REFRESH" => "Y",
			"PARENT" => "BASE",
		),
		"USE_COMMENT" => array(
			"NAME" => GetMessage("SALE_COMMENT"),
			"TYPE"=>"CHECKBOX",
			"DEFAULT"=>"N",
			"PARENT" => "BASE",
		),
		"EVENT_MESSAGE_ID" => Array(
			"NAME" => GetMessage("SALE_EMAIL_TEMPLATES"), 
			"TYPE"=>"LIST", 
			"VALUES" => $arEvent,
			"DEFAULT"=>"", 
			"MULTIPLE"=>"Y", 
			"COLS"=>25, 
			"PARENT" => "BASE",
		),
		"USE_CAPTCHA" => array(
			"NAME" => GetMessage("SALE_CAPTHA"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"TITLE_POPUP" => array(
			"NAME" => GetMessage("SALE_TITLE_POPUP"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("SALE_TITLE_POPUP_VALUE"),
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"CONFIRM_ORDER" => array(
			"NAME" => GetMessage("SALE_CONFIRM_ORDER"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("SALE_CONFIRM_ORDER_OK"),
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"IMAGE_WIDTH" => array(
			"NAME" => GetMessage("SALE_WIDTH_IMAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => "130",
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"IMAGE_HEIGHT" => array(
			"NAME" => GetMessage("SALE_HEIGHT_IMAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => "130",
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"PERSONAL_DATA" => array(
			"NAME" => GetMessage("SALE_PERSONAL_DATA"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
	)
);

if (!$bShop)
{
	unset($arComponentParameters["PARAMETERS"]["PERSON_TYPE"]);
	unset($arComponentParameters["PARAMETERS"]["PERSON_TYPE_PROPS"]);
	unset($arComponentParameters["PARAMETERS"]["PAYSYSTEM"]);
	unset($arComponentParameters["PARAMETERS"]["DELIVERY"]);
	unset($arComponentParameters["PARAMETERS"]["ORDER_STATUS"]);
	unset($arComponentParameters["PARAMETERS"]["DELIVERY_SHOW"]);
	unset($arComponentParameters["PARAMETERS"]["USE_COMMENT"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_SHOW"]);
	
	$arComponentParameters["PARAMETERS"]["ORDER_PRODUCT"] = array(
		"NAME" => GetMessage("SALE_PRODUCT"),
		"TYPE"=>"LIST", 
		"VALUES" => array(
				2=>GetMessage("SALE_PRODUCT_CURRENT"), 
				3=>GetMessage("SALE_PRODUCT_NEW"),
			),
		"DEFAULT"=>"",
		"REFRESH" => "Y",
		"PARENT" => "BASE",
	);
}

if ($arCurrentValues["USE_USER"] == "Y")
{
	$arComponentParameters["PARAMETERS"]["USER_ID"] = array(
			"NAME" => GetMessage("SALE_USER_ID"),
			"TYPE"=>"STRING", 
			"VALUES" => "",
			"DEFAULT"=>"",
			"PARENT" => "BASE",
		);
}

if ($arCurrentValues["ORDER_PRODUCT"] == 1 && $bShop)
{
	$arComponentParameters["PARAMETERS"]["HIDE_BUTTON"] = array(
			"NAME" => GetMessage("SALE_HIDE_BUTTON"),
			"TYPE"=>"CHECKBOX", 
			"DEFAULT"=>"N",
			"PARENT" => "BASE",
		);
}

if ($arCurrentValues["ORDER_PRODUCT"] == 2)
{
	$arComponentParameters["PARAMETERS"]["PRODUCT_ID"] = array(
			"NAME" => GetMessage("SALE_PRODUCT_ID"),
			"TYPE"=>"STRING", 
			"VALUES" => "",
			"DEFAULT"=>"",
			"PARENT" => "BASE",
		);
	if ($bShop)
	{
		$arComponentParameters["PARAMETERS"]["PRICE_CODE"] = array(
				"PARENT" => "BASE",
				"NAME" => GetMessage("SALE_PRICE_TYPE"),
				"TYPE" => "LIST",
				"MULTIPLE" => "N",
				"VALUES" => $arPrice,
			);
	}
}

if ($arCurrentValues["ORDER_PRODUCT"] == 3)
{
	if ($bShop)
	{
		$arComponentParameters["PARAMETERS"]["PRODUCT_PRICE"] = array(
				"NAME" => GetMessage("SALE_PRODUCT_PRICE"),
				"TYPE"=>"STRING", 
				"VALUES" => "",
				"DEFAULT"=>"100",
				"PARENT" => "BASE",
			);
		$arComponentParameters["PARAMETERS"]["PRODUCT_WEIGHT"] = array(
				"NAME" => GetMessage("SALE_PRODUCT_WEIGHT"),
				"TYPE"=>"STRING", 
				"VALUES" => "",
				"DEFAULT"=>"0",
				"PARENT" => "BASE",
			);
	}
	$arComponentParameters["PARAMETERS"]["PRODUCT_NAME"] = array(
			"NAME" => GetMessage("SALE_PRODUCT_NAME"),
			"TYPE"=>"STRING", 
			"VALUES" => "",
			"DEFAULT"=>"",
			"PARENT" => "BASE",
		);
	$arComponentParameters["PARAMETERS"]["PRODUCT_URL"] = array(
			"NAME" => GetMessage("SALE_PRODUCT_URL"),
			"TYPE"=>"STRING", 
			"VALUES" => "",
			"DEFAULT"=>"",
			"PARENT" => "BASE",
		);
}
if (($arCurrentValues["ORDER_PRODUCT"] == 3 || $arCurrentValues["ORDER_PRODUCT"] == 2) && $bShop)
{
	$arComponentParameters["PARAMETERS"]["USE_COUNT"] = array(
		"NAME" => GetMessage("SALE_COUNT"),
		"TYPE"=>"CHECKBOX",
		"DEFAULT"=>"N",
		"PARENT" => "BASE",
	);
}
?>