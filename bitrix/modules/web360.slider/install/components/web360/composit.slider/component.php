<?

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

if($arParams["JQUERY_CONNECTED"] == "N")
	$APPLICATION->AddHeadScript($componentPath.'/jquery/jquery-1.7.2.min.js');

	
//подключение дополнительных файлов 
$APPLICATION->SetAdditionalCSS($componentPath.'/jquery/bxSlider/jquery.bxslider.css');
$APPLICATION->AddHeadScript($componentPath.'/jquery/bxSlider/jquery.bxslider.js');
$APPLICATION->AddHeadScript($componentPath.'/jquery/bxSlider/jquery.easing.1.3.js');

if($arParams["FANCYBOX_CONNECTED"] == "N")
{
	$APPLICATION->SetAdditionalCSS($componentPath.'/jquery/fancybox/jquery.fancybox.css');
	$APPLICATION->AddHeadScript($componentPath.'/jquery/fancybox/jquery.fancybox.js');
	$APPLICATION->AddHeadScript($componentPath.'/jquery/fancybox/jquery.mousewheel-3.0.4.pack.js');
}

//массив данных инфоблока
if($arParams["DATA_SOURCE"] == "IBLOCK")
{
	$arIblock = array(
		"CHECKED" => "Y",
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"IBLOCK_SECTION_ID" => $arParams["IBLOCK_SECTION_ID"],
		"IBLOCK_SECTION_CODE" => $arParams["IBLOCK_SECTION_CODE"],
		"PICTURE" => $arParams["SOURCE_PICTURES"],
		"SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
		"SORT_BY" => $arParams["ELEMENT_SORT_FIELD"],
		"ONLY_ACTIVE" => $arParams["ONLY_ACTIVE"],
		"SHOW_ALL_ITEMS" => $arParams["SHOW_ALL_ITEMS"],
		
	);
}
if(CModule::IncludeModule("advertising"))
{  
	//массив данных баннера
	if($arParams["DATA_SOURCE"] == "BANNER")
	{
		$arBanner = array(
			"CHECKED" => "Y",
			"BANNER_TYPE" => $arParams["BANNER_TYPE"],
		);
	}
}
//массив данных медиабиблиотеки
if($arParams["DATA_SOURCE"] == "MEDIA_LIBRARY")
{
	$arMediaLibrary = array(
		"CHECKED" => "Y",
		"MEDIA_TYPE" => $arParams["MEDIALIBRARY_TYPE"],
	);
}

$arData = array(
	"IBLOCK" => $arIblock,
	"BANNER" => $arBanner,
	"MEDIALIBRARY" => $arMediaLibrary,
);

$arResult = array(
	"DATA_SOURCE" => $arData,
);
//формируем массив ITEMS для Медиабиблиотеки
if($arParams["DATA_SOURCE"] =="MEDIA_LIBRARY")
{

	if(CModule::IncludeModule("fileman"))
	{
		CMedialib::Init();
		
		$arMediaColection = CMedialibCollection::GetList(array('arOrder' => array('NAME' => 'ASC')));

		$Media = CMedialibItem::GetList(array('arCollections' => array("0" => $arParams["MEDIALIBRARY_TYPE"])));
		foreach($Media as $key=> $arMedia)
		{

			if(!empty($arParams["SLIDER_WIDTH"]))
				$width = $arParams["SLIDER_WIDTH"];
			else
				$width = "400";

			if(!empty($arParams["SLIDER_HEIGHT"]))
				$height = $arParams["SLIDER_HEIGHT"];
			else
				$height = "300";

			$file = CFile::ResizeImageGet($arMedia["SOURCE_ID"], array('width'=>$width, 'height'=>$height), BX_RESIZE_IMAGE_EXACT, true);

			$arResult["ITEMS"][$key]["ID"] = $key;
			$arResult["ITEMS"][$key]["NAME"] = '';
			$arResult["ITEMS"][$key]["URL"] = '';
			$arResult["ITEMS"][$key]["CONTENT"]["ID"] = $arMedia["SOURCE_ID"];
			$arResult["ITEMS"][$key]["CONTENT"]["ORIGINAL_NAME"] = $arMedia["NAME"];
			$arResult["ITEMS"][$key]["CONTENT"]["WIDTH"] = $file["width"];
			$arResult["ITEMS"][$key]["CONTENT"]["HEIGHT"] =  $file["height"];
			$arResult["ITEMS"][$key]["CONTENT"]["TYPE"] =  $arMedia["TYPE"];
			$arResult["ITEMS"][$key]["CONTENT"]["RESIZE_IMG"] = $file["src"];
			$arResult["ITEMS"][$key]["CONTENT"]["STANDART_IMG"] = $arMedia["PATH"];
			$arResult["ITEMS"][$key]["CONTENT"]["CODE"] =  '';
		}		
	}
}

//формируем массив ITEMS для Баннера
if(CModule::IncludeModule("advertising"))
{  
	if($arParams["DATA_SOURCE"] =="BANNER")
	{
		$by = array("s_active" =>"Y");
		$arFilter = array("TYPE_SID" =>$arParams["BANNER_TYPE"]);
		$indexKey = 0;
		$arPicture = array();
			$rsBanners = CAdvBanner::GetList($by, $order, $arFilter, $is_filtered, "N");
			while($arBanner = $rsBanners->Fetch())
			{	
				$arResult["ITEMS"][$indexKey]["ID"] =  $arBanner["ID"];
				$arResult["ITEMS"][$indexKey]["NAME"] =  $arBanner["NAME"];
				$arResult["ITEMS"][$indexKey]["URL"] = $arBanner["URL"];
				
				if($arBanner["AD_TYPE"] == "image" || $arBanner["AD_TYPE"] == "flash")
				{
					$arFile = CFile::GetFileArray($arBanner["IMAGE_ID"]);
	
				if(!empty($arParams["SLIDER_WIDTH"]))
					$width = $arParams["SLIDER_WIDTH"];
				else
					$width = "400";
	
				if(!empty($arParams["SLIDER_HEIGHT"]))
					$height = $arParams["SLIDER_HEIGHT"];
				else
					$height = "300";
	
					$file = CFile::ResizeImageGet($arFile["ID"], array('width'=>$width, 'height'=>$height), BX_RESIZE_IMAGE_EXACT, true);
					
					$arResult["ITEMS"][$indexKey]["CONTENT"]["ID"] =  $arFile["ID"];
					$arResult["ITEMS"][$indexKey]["CONTENT"]["ORIGINAL_NAME"] = $arFile["ORIGINAL_NAME"];
					$arResult["ITEMS"][$indexKey]["CONTENT"]["WIDTH"] =  $file["width"];
					$arResult["ITEMS"][$indexKey]["CONTENT"]["HEIGHT"] =  $file["height"];
					$arResult["ITEMS"][$indexKey]["CONTENT"]["TYPE"] =  $arBanner["AD_TYPE"];
					$arResult["ITEMS"][$indexKey]["CONTENT"]["RESIZE_IMG"] = $file["src"];
					$arResult["ITEMS"][$indexKey]["CONTENT"]["STANDART_IMG"] = $arFile["SRC"];
					$arResult["ITEMS"][$indexKey]["CONTENT"]["CODE"] = '';
					
				}
				if($arBanner["AD_TYPE"] == "html")
				{
					$arResult["ITEMS"][$indexKey]["CONTENT"]["ID"] = '';
					$arResult["ITEMS"][$indexKey]["CONTENT"]["ORIGINAL_NAME"] = '';
					$arResult["ITEMS"][$indexKey]["CONTENT"]["WIDTH"] = '';
					$arResult["ITEMS"][$indexKey]["CONTENT"]["HEIGHT"] = '';
					$arResult["ITEMS"][$indexKey]["CONTENT"]["TYPE"] =  $arBanner["AD_TYPE"];
					$arResult["ITEMS"][$indexKey]["CONTENT"]["RESIZE_IMG"] = '';
					$arResult["ITEMS"][$indexKey]["CONTENT"]["STANDART_IMG"] = "";
					$arResult["ITEMS"][$indexKey]["CONTENT"]["CODE"] = $arBanner["CODE"];
				}
				
				$indexKey ++;
			}
	}
} 	
//Формируем массив ITEMS для инфоблока
if($arParams["DATA_SOURCE"] == "IBLOCK")
{
	$arOrder = array($arParams["ELEMENT_SORT_FIELD"]=>$arParams["ELEMENT_SORT_ORDER"]);
	$arFilter = array("IBLOCK_ID"=>$arParams["IBLOCK_ID"],"INCLUDE_SUBSECTIONS"=>"Y");
	$arSelect = array("NAME","ID","IBLOCK_ID","ACTIVE","IBLOCK_SECTION_ID");

	//источник картинки
	if($arParams["SOURCE_PICTURES"] == "PREVIEW")
		$arSelect[] = "PREVIEW_PICTURE";
	else 
		$arSelect[] = "DETAIL_PICTURE";
	
	//фильтр по разделу
	if(!empty($arParams["IBLOCK_SECTION_ID"]))
		$arFilter["IBLOCK_SECTION_ID"] = $arParams["IBLOCK_SECTION_ID"];
	else
		$arFilter["IBLOCK_SECTION_CODE"] = $arParams["IBLOCK_SECTION_CODE"];
	
	//фильтр по активности
	if($arParams["ONLY_ACTIVE"] == "Y")
		$arFilter["ACTIVE"] = "Y";

	//Показывать все элементы, если не указан раздел
	if($arParams["SHOW_ALL_ITEMS"] != "Y")
		$arFilter["SECTION_ID"] = 0;
	
	//если указан код раздела выводим элементы с этого раздела
	if(!empty($arParams["IBLOCK_SECTION_CODE"]) && empty($arParams["IBLOCK_SECTION_ID"]))
		$arFilter["IBLOCK_CODE"] = $arParams["IBLOCK_SECTION_CODE"];
	
	//если указан id-раздела выводить элементы с етого раздела
	if(!empty($arParams["IBLOCK_SECTION_ID"]))
		$arFilter["SECTION_ID"] = $arParams["IBLOCK_SECTION_ID"];	


	$res = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);

	$indexKeys = 0;
	while($arElement = $res->GetNext())
	{
			if(!empty($arElement['PREVIEW_PICTURE'])) 
				$image_id = $arElement['PREVIEW_PICTURE'];
			elseif(!empty($arElement['DETAIL_PICTURE'])) 
				$image_id = $arElement['DETAIL_PICTURE'];	    

$db_props = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $arElement["ID"], array("sort" => "asc"), Array("CODE"=>$arParams["PROP_LIST"]));
if($ar_props = $db_props->Fetch())

			$arResult["ITEMS"][$indexKeys]["ID"] =  $arElement["ID"];
			$arResult["ITEMS"][$indexKeys]["NAME"] =  $arElement["NAME"];
			$arResult["ITEMS"][$indexKeys]["URL"] = $ar_props["VALUE"];

			//заполняем массив CONTENT в arResult
			if(!empty($image_id))
			{
				$arFile = CFile::GetFileArray($image_id);

			if(!empty($arParams["SLIDER_WIDTH"]))
				$width = $arParams["SLIDER_WIDTH"];
			else
				$width = "400";

			if(!empty($arParams["SLIDER_HEIGHT"]))
				$height = $arParams["SLIDER_HEIGHT"];
			else
				$height = "300";

				$file = CFile::ResizeImageGet($arFile["ID"], array('width'=>$width, 'height'=>$height), BX_RESIZE_IMAGE_EXACT, true);
				
				$arResult["ITEMS"][$indexKeys]["CONTENT"]["ID"] =  $arFile["ID"];
				$arResult["ITEMS"][$indexKeys]["CONTENT"]["ORIGINAL_NAME"] = $arFile["ORIGINAL_NAME"];
				$arResult["ITEMS"][$indexKeys]["CONTENT"]["WIDTH"] =  $file["width"];
				$arResult["ITEMS"][$indexKeys]["CONTENT"]["HEIGHT"] =  $file["height"];
				$arResult["ITEMS"][$indexKeys]["CONTENT"]["TYPE"] = "";
				$arResult["ITEMS"][$indexKeys]["CONTENT"]["RESIZE_IMG"] = $file["src"];
				$arResult["ITEMS"][$indexKeys]["CONTENT"]["STANDART_IMG"] = $arFile["SRC"];
				$arResult["ITEMS"][$indexKeys]["CONTENT"]["CODE"] = "";	
			}
			if(empty($arFile["ID"]))
				$arResult["ITEMS"][$indexKeys]["CONTENT"] = array();

			$indexKeys ++;
	}
}

$arResult["SLIDER_COUNT"] = $arParams["SLIDER_COUNT"];
$arResult["TURNED_SLIDER"] = $arParams["TURNED_SLIDER"];
$arResult["SLIDER_WIDTH"] = $arParams["SLIDER_WIDTH"];
$arResult["SLIDER_HEIGHT"] = $arParams["SLIDER_HEIGHT"];
$arResult["SCROLL_EFFECT"] = $arParams["SCROLL_EFFECT"];
$arResult["SCROLL_SPEED"] = $arParams["SCROLL_SPEED"];
$arResult["OUTPUT_ARROWS"] = $arParams["OUTPUT_ARROWS"];
$arResult["OUTPUT_LOWER_NAVIGATION"] = $arParams["OUTPUT_LOWER_NAVIGATION"];
$arResult["AUTOMATIC_SLIDER"] = $arParams["AUTOMATIC_SLIDER"];
$arResult["SHOW_TIME_SLIDER"] = $arParams["SHOW_TIME_SLIDER"];

$APPLICATION->ShowCSS();
$APPLICATION->ShowHeadScripts();
$this->IncludeComponentTemplate();

?>







