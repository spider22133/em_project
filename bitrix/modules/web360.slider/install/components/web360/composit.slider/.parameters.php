<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

// получаем свойство выбранного инфоблока
if(!CModule::IncludeModule("iblock"))
	return;
	
	$arProperty_LNS = array();
	$arProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>(isset($arCurrentValues["IBLOCK_ID"])?$arCurrentValues["IBLOCK_ID"]:$arCurrentValues["ID"])));
	while ($arrprop=$arProp->Fetch())
	{
		$arProperty[$arrprop["CODE"]] = "[".$arrprop["CODE"]."] ".$arrprop["NAME"];
		if (in_array($arrprop["PROPERTY_TYPE"], array("L", "N", "S")))
		{
			$arProperty_LNS[$arrprop["CODE"]] = "[".$arrprop["CODE"]."] ".$arrprop["NAME"];
			
		}
	}

//Получаем свойство инфоблока(только строковые)
	$rsPropS = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array( "ACTIVE"=>"Y","PROPERTY_TYPE"=>"S","IBLOCK_ID"=>(isset($arCurrentValues["IBLOCK_ID"])?$arCurrentValues["IBLOCK_ID"]:$arCurrentValues["ID"])));
	while($arrps=$rsPropS->Fetch())
	{
		$arProperty_String[$arrps["CODE"]] = "[".$arrps["CODE"]."] ".$arrps["NAME"];
	}
// Получаем типы медиабиблиотек
if(CModule::IncludeModule("fileman"))
{
	$arMediaType = CMedialib::GetTypes($arConfigTypes = array(), $bGetEmpties = false);
	$arMediaColection = CMedialibCollection::GetList(array('arOrder' => array('NAME' => 'ASC')));
$arMediaName = array();
foreach($arMediaColection as $key=>$arMediacol)
	{
		if($arMediacol["ML_TYPE"]!=2 && $arMediacol["NAME"] == true)
		{
			$arMediaName[$arMediacol["ID"]] = "[".$arMediacol["ID"]."]".$arMediacol["NAME"];
		}
	}
}

// Получаем типы баннеров
if(CModule::IncludeModule("advertising"))
{  
	$arTypeFields = Array();
	$rsAdvType = CAdvType::GetList( $s_sid, $asc, array(), $is_filtered, "N");
	while($arBannerType = $rsAdvType->Fetch())
	{
		$arTypeFields[$arBannerType["SID"]] = "[".$arBannerType["SID"]."] ".$arBannerType["NAME"];
	}
}

$arIBlockType = CIBlockParameters::GetIBlockTypes();
$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arri=$rsIBlock->Fetch())
	$arIBlock[$arri["ID"]] = "[".$arri["ID"]."] ".$arri["NAME"];


$arProperty_N = array();
$arProperty_X = array();


$arProperty_UF = array();
$arSProperty_LNS = array();
$arUserFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("IBLOCK_".$arCurrentValues["IBLOCK_ID"]."_SECTION");
foreach($arUserFields as $FIELD_NAME=>$arUserField)
{
	$arProperty_UF[$FIELD_NAME] = $arUserField["LIST_COLUMN_LABEL"]? $arUserField["LIST_COLUMN_LABEL"]: $FIELD_NAME;
	if($arUserField["USER_TYPE"]["BASE_TYPE"]=="string")
		$arSProperty_LNS[$FIELD_NAME] = $arProperty_UF[$FIELD_NAME];
}


$arOffers = CIBlockPriceTools::GetOffersIBlock($arCurrentValues["IBLOCK_ID"]);
$OFFERS_IBLOCK_ID = is_array($arOffers)? $arOffers["OFFERS_IBLOCK_ID"]: 0;
$arProperty_Offers = array();
if($OFFERS_IBLOCK_ID)
{
	$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$OFFERS_IBLOCK_ID, "ACTIVE"=>"Y"));
	while($arrp=$rsProp->Fetch())
	{
		if($arrp["PROPERTY_TYPE"] != "F")
			$arProperty_Offers[$arrp["CODE"]] = "[".$arrp["CODE"]."] ".$arrp["NAME"];
	}
}

$arPrice = array();
if(CModule::IncludeModule("catalog"))
{
	$rsPrice=CCatalogGroup::GetList($v1="sort", $v2="asc");
	while($arrpr=$rsPrice->Fetch()) $arPrice[$arrpr["NAME"]] = "[".$arrpr["NAME"]."] ".$arrpr["NAME_LANG"];
}
else
{
	$arPrice = $arProperty_N;
}

$arAscDesc = array(
	"asc" => GetMessage("IBLOCK_SORT_ASC"),
	"desc" => GetMessage("IBLOCK_SORT_DESC"),
);

$arScrollEffect = array("horizontal" => GetMessage("DATA_HORIZONTALLY"), "vertical" => GetMessage("DATA_VERTICAL"), "fade" => GetMessage("DATA_FADE"));

$arBannerSours = array
(
	"EMPTY" => GetMessage("EMPTY"),
	"IBLOCK" => GetMessage("DATA_IBLOCK"), 
	"MEDIA_LIBRARY" => GetMessage("DATA_MEDIA_LIBRARY")
);

if(CModule::IncludeModule("advertising"))
{  
	$arBannerSours["BANNER"] = GetMessage("DATA_BANNER");
}

$arClicked_action = array("EMPTY" => GetMessage("EMPTY"), "TRANSIT" => GetMessage("TRANSITION"), "POPUP" => GetMessage("POPUP"));
$arSource_picture = array("PREVIEW" => GetMessage("PREVIEW_PICTURES"), "DETAIL" => GetMessage("DETAIL_PICTURES"));
$arParameters = array("fade" => GetMessage("FADE"), "elastic" => GetMessage("ELASTIC"), "EMPTY" => GetMessage("EMPTY"));
$arComponentParameters = array(
//создание групп в компоненте
	"GROUPS" => array(
		"INPUT" => array(
			"NAME" => GetMessage("INPUT"),
			"SORT" => "10"

		),
		"SLIDE_SETTING" => array(
			"NAME" => GetMessage("SLIDE_SETTING"),
			"SORT" => "30"
		),
		"ADDITIONALLY_SETTINGS" =>array(
			"NAME" => GetMessage("ADDITIONALLY_SETTINGS"),
			"SORT" => "50"
		),
	),
//Создание полей в группах
	"PARAMETERS" => array(
		"JQUERY_CONNECTED" => array(
			"PARENT" => "ADDITIONALLY_SETTINGS",
			"NAME" => GetMessage("JQUERY_CONNECTED"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"FANCYBOX_CONNECTED" => array(
			"PARENT" => "ADDITIONALLY_SETTINGS",
			"NAME" => GetMessage("FANCYBOX_CONNECTED"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"DATA_SOURCE" => array(
			"PARENT"=> "INPUT",
			"NAME" => GetMessage("DATA_SOURCE"),
			"TYPE" => "LIST",
			"REFRESH" => "Y",
			"VALUES" => $arBannerSours,
		),
		"SLIDER_COUNT" => array(
			"PARENT" => "SLIDE_SETTING",
			"NAME" => GetMessage("DATA_SLIDER_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "5",
		),
		"TURNED_SLIDER" => array(
			"PARENT" => "SLIDE_SETTING",
			"NAME" => GetMessage("DATA_TURNED_NUMBER_SLIDER"),
			"TYPE" => "STRING",
			"DEFAULT" => "1",
		),
		"SLIDER_WIDTH" => array(
			"PARENT" => "SLIDE_SETTING",
			"NAME" => GetMessage("DATA_SLIDER_WIDTH"),
			"TYPE" => "STRING",
			"DEFAULT" => "150",
		),
		"SLIDER_HEIGHT" => array(
			"PARENT" => "SLIDE_SETTING",
			"NAME" => GetMessage("DATA_SLIDER_HEIGHT"),
			"TYPE" => "STRING",
			"DEFAULT" => "100",
		),
		"SLIDE_MARGIN" => array(
			"PARENT" => "SLIDE_SETTING",
			"NAME" => GetMessage("SLIDE_MARGIN"),
			"TYPE" => "STRING",
			"DEFAULT" => "0",
		),
		"SCROLL_EFFECT" => array(
			"PARENT"=> "SLIDE_SETTING",
			"NAME" => GetMessage("DATA_SCROLL_EFFECT"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arScrollEffect,
			"REFRESH" => "Y",
		),
		"SCROLL_SPEED" => array(
			"PARENT" => "SLIDE_SETTING",
			"NAME" => GetMessage("DATA_SCROLL_SPEED"),
			"TYPE" => "STRING",
			"DEFAULT" => "2",
		),
		"OUTPUT_ARROWS" => array(
			"PARENT" => "SLIDE_SETTING",
			"NAME" => GetMessage("DATA_OUTPUT_ARROWS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"OUTPUT_LOWER_NAVIGATION" => array(
			"PARENT" => "SLIDE_SETTING",
			"NAME" => GetMessage("DATA_OUTPUT_LOWER_NAVIGATION"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"AUTOMATIC_SLIDER" => array(
			"PARENT" => "SLIDE_SETTING",
			"NAME" => GetMessage("DATA_AUTOMATIC_SLIDER"),
			"TYPE" => "CHECKBOX",
			"REFRESH" =>"Y",
			"DEFAULT" =>"N",

		),
		"SHOW_TIME_SLIDER" => array(
			"PARENT" => "SLIDE_SETTING",
			"NAME" => GetMessage("SHOW_TIME_SLIDER"),
			"TYPE" => "STRING",
			"DEFAULT" => "5",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
	),
);
if ($arCurrentValues["DATA_SOURCE"] == "IBLOCK")
{
	$arComponentParameters["GROUPS"]["IBLOCK_OPTION"] = array(
			"NAME" => GetMessage("IBLOCK_OPTION"),
			"SORT" => "40"
		);
	$arComponentParameters["GROUPS"]["BASIC"] = array(
			"NAME" => GetMessage("BASIC"),
			"SORT" => "20"
		);
	$arComponentParameters["PARAMETERS"]["SLIDER_HEADING"] = array(
		"PARENT" => "ADDITIONALLY_SETTINGS",
		"NAME" => GetMessage("SLIDER_HEADING"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	);
	$arComponentParameters["PARAMETERS"]["IBLOCK_TYPE"] = array(
		"PARENT" => "INPUT",
		"NAME" => GetMessage("IBLOCK_TYPE"),
		"TYPE" => "LIST",
		"VALUES" => $arIBlockType,
		"REFRESH" => "Y",
	);
	$arComponentParameters["PARAMETERS"]["IBLOCK_ID"] = array(
		"PARENT" => "INPUT",
		"NAME" => GetMessage("IBLOCK_IBLOCK"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => $arIBlock,
		"REFRESH" => "Y",
	);
	$arComponentParameters["PARAMETERS"]["IBLOCK_SECTION_CODE"] = array(
		"PARENT" => "INPUT",
		"NAME" => GetMessage("IBLOCK_SECTION_CODE"),
		"TYPE" => "STRING",
		"DEFAULT" => '',
	);

	$arComponentParameters["PARAMETERS"]["IBLOCK_SECTION_ID"] = array(
		"PARENT" => "INPUT",
		"NAME" => GetMessage("IBLOCK_SECTION_ID"),
		"TYPE" => "STRING",
		"DEFAULT" => '',
	);
	$arComponentParameters["PARAMETERS"]["ELEMENT_SORT_FIELD"] = array(
			"PARENT" => "BASIC",
			"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_FIELD"),
			"TYPE" => "LIST",
			"VALUES" => array(
				"shows" => GetMessage("IBLOCK_SORT_SHOWS"),
				"sort" => GetMessage("IBLOCK_SORT_SORT"),
				"timestamp_x" => GetMessage("IBLOCK_SORT_TIMESTAMP"),
				"name" => GetMessage("IBLOCK_SORT_NAME"),
				"id" => GetMessage("IBLOCK_SORT_ID"),
				"active_from" => GetMessage("IBLOCK_SORT_ACTIVE_FROM"),
				"active_to" => GetMessage("IBLOCK_SORT_ACTIVE_TO"),
			),
			"DEFAULT" => "sort",
		);
	$arComponentParameters["PARAMETERS"]["ELEMENT_SORT_ORDER"] = array(
			"PARENT" => "BASIC",
			"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_ORDER"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "asc",
		);
	$arComponentParameters["PARAMETERS"]["CLICKED_ACTION"] = array(
			"PARENT"=> "IBLOCK_OPTION",
			"NAME" => GetMessage("CLICKED_ACTION"),
			"TYPE" => "LIST",
			"REFRESH" => "Y",
			"VALUES" => $arClicked_action,
		);
	$arComponentParameters["PARAMETERS"]["SOURCE_PICTURES"] = array(
			"PARENT" =>"INPUT",
			"NAME" => GetMessage("SOURCE_PICTURES"),
			"TYPE" => "LIST",
			"REFRESH" => "Y",
			"VALUES" => $arSource_picture,
		);
	$arComponentParameters["PARAMETERS"]["ONLY_ACTIVE"] = array(
			"PARENT" =>"INPUT",
			"NAME" => GetMessage("ONLY_ACTIVE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		);
	$arComponentParameters["PARAMETERS"]["SHOW_ALL_ITEMS"] = array(
			"PARENT" =>"INPUT",
			"NAME" => GetMessage("SHOW_ALL_ITEMS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		);
	
}
if(CModule::IncludeModule("advertising"))
{  
	if ($arCurrentValues["DATA_SOURCE"] == "BANNER")
	{
		$arComponentParameters["PARAMETERS"]["BANNER_TYPE"] = array(
				"PARENT" => "INPUT",
				"NAME" => GetMessage("BANNER_TYPE"),
				"TYPE" => "LIST",
				"VALUES" => $arTypeFields,
				"REFRESH" => "Y",
		);
	}
}
if ($arCurrentValues["DATA_SOURCE"] == "MEDIA_LIBRARY")
{
	$arComponentParameters["PARAMETERS"]["MEDIALIBRARY_TYPE"] = array(
		"PARENT" => "INPUT",
		"NAME" => GetMessage("MEDIALIBRARY_TYPE"),
		"TYPE" => "LIST",
		"VALUES" => $arMediaName,
		"REFRESH" => "Y",
	);
}
if ($arCurrentValues["CLICKED_ACTION"] == "POPUP")
{
	$arComponentParameters["GROUPS"]["IBLOCK_OPTION"] = array(
		"NAME" => GetMessage("IBLOCK_OPTION"),
		"SORT" => "40"
		);
	$arComponentParameters["PARAMETERS"]["EFFECT_OPEN"] = array(
		"PARENT" => "IBLOCK_OPTION",
		"NAME" => GetMessage("EFFECT_OPEN"),
		"TYPE" => "LIST",
		"VALUES" => $arParameters,
		"REFRESH" => "Y",
	);
	$arComponentParameters["PARAMETERS"]["EFFECT_CLOSE"] = array(
		"PARENT" => "IBLOCK_OPTION",
		"NAME" => GetMessage("EFFECT_CLOSE"),
		"TYPE" => "LIST",
		"VALUES" => $arParameters,
		"REFRESH" => "Y",
	);
	$arComponentParameters["PARAMETERS"]["SPEED_OPEN"] = array(
		"PARENT" => "IBLOCK_OPTION",
		"NAME" => GetMessage("SPEED_OPEN"),
		"TYPE" => "STRING",
		"DEFAULT" => '2',
	);
	$arComponentParameters["PARAMETERS"]["SPEED_CLOSE"] = array(
		"PARENT" => "IBLOCK_OPTION",
		"NAME" => GetMessage("SPEED_CLOSE"),
		"TYPE" => "STRING",
		"DEFAULT" => '2',
	);
}
if ($arCurrentValues["CLICKED_ACTION"] == "TRANSIT" && $arCurrentValues["DATA_SOURCE"] == "IBLOCK")
{
	$arComponentParameters["GROUPS"]["IBLOCK_OPTION"] = array(
		"NAME" => GetMessage("IBLOCK_OPTION"),
		"SORT" => "40"
	);
	$arComponentParameters["PARAMETERS"]["PROP_LIST"] = array(
		"PARENT" => "IBLOCK_OPTION",
		"NAME" => GetMessage("FIELD"),
		"TYPE" => "LIST",
		"VALUES" => $arProperty_String,//$arProperty_LNS
		"REFRESH" => "Y",
	);
	$arComponentParameters["PARAMETERS"]["OTHER"] = array(
		"PARENT" =>"IBLOCK_OPTION",
		"NAME" => GetMessage("OTHER"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("OTHER"),
	);
}

?>