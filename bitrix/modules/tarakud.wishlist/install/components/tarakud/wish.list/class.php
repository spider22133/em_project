<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main;
//use Bitrix\Iblock;
//use Bitrix\Catalog;
//use Bitrix\Main\Text\String as String;
use Bitrix\Main\Localization\Loc as Loc;
use Bitrix\Main\SystemException as SystemException;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Tarakud\Wishlist;

class CSimpleComponent extends CBitrixComponent
{
	const MODULE_ID = 'tarakud.wishlist';
	public $isShop = true;
	
	/**
	 * Auto prepare Component Params
	 */
	public function onPrepareComponentParams($params)
	{
		$params["IMG_HEIGHT"] = intval($params["IMG_HEIGHT"]);
		if ($params["IMG_HEIGHT"] <= 0)
			$params["IMG_HEIGHT"] = 150;
		
		$params["IMG_WIDTH"] = intval($params["IMG_WIDTH"]);
		if ($params["IMG_WIDTH"] <= 0)
			$params["IMG_WIDTH"] = 150;
		
		$params["PAGE_ELEMENT_COUNT"] = intval($params["PAGE_ELEMENT_COUNT"]);
		if ($params["PAGE_ELEMENT_COUNT"] <= 0)
			$params["PAGE_ELEMENT_COUNT"] = 15;
		
		$params["LINE_ELEMENT_COUNT"] = intval($params["LINE_ELEMENT_COUNT"]);
		if ($params["LINE_ELEMENT_COUNT"] <= 0)
			$params["LINE_ELEMENT_COUNT"] = 1;
		
		$params["SET_TITLE"] = $params["SET_TITLE"]!="N";
		if(!is_array($params["PRICE_CODE"]))
			$params["PRICE_CODE"] = array();
		
		$params["PAGER_TITLE"] = trim($params["PAGER_TITLE"]);
		$params["PAGER_TEMPLATE"] = trim($params["PAGER_TEMPLATE"]);
		$params["PAGER_SHOW_ALWAYS"] = $params["PAGER_SHOW_ALWAYS"]=="Y";
		$params["PAGER_SHOW_ALL"] = $params["PAGER_SHOW_ALL"]=="Y";
		$params["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($params["PAGER_DESC_NUMBERING_CACHE_TIME"]);
		if ($params["PAGER_DESC_NUMBERING_CACHE_TIME"] <= 0)
			$params["PAGER_DESC_NUMBERING_CACHE_TIME"] = 3600;
		$params["PAGER_DESC_NUMBERING"] = $params["PAGER_DESC_NUMBERING"]=="Y";
		
		$params["DISPLAY_TOP_PAGER"] = $params["DISPLAY_TOP_PAGER"]=="Y";
		$params["DISPLAY_BOTTOM_PAGER"] = $params["DISPLAY_BOTTOM_PAGER"]!="N";
		
		if (empty($params["ELEMENT_SORT_FIELD"]))
			$params["ELEMENT_SORT_FIELD"] = "sort";
		if (!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $params["ELEMENT_SORT_ORDER"]))
			$params["ELEMENT_SORT_ORDER"] = "asc";
		if (empty($params["ELEMENT_SORT_FIELD2"]))
			$params["ELEMENT_SORT_FIELD2"] = "id";
		if (!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $params["ELEMENT_SORT_ORDER2"]))
			$arParams["ELEMENT_SORT_ORDER2"] = "desc";
		
		if(!isset($params["CACHE_TIME"]))
			$params["CACHE_TIME"] = 36000000;
		
		$params["BASKET_URL"]=trim($params["BASKET_URL"]);
		if($params["BASKET_URL"] === '')
			$params["BASKET_URL"] = "/personal/basket.php";
		
		$params["WISHLIST_URL"]=COption::GetOptionString("tarakud.wishlist", "wishlist_page", "/wishlist/");
		if($params["WISHLIST_URL"] == '')
			$params["WISHLIST_URL"] = "/wishlist/";
		
		$params["AUTH_URL"]=trim($params["AUTH_URL"]);
		if($params["AUTH_URL"] === '')
			$params["AUTH_URL"] = "/auth/";
		
		$params["SOCIAL_TEXT"] = "";
		if (!empty($params["SOCIAL"]))
			$params["SOCIAL_TEXT"] = implode(",", $params["SOCIAL"]);
		
		return $params;
	}

	/**
	 * Check Required Modules
	 * @throws Exception
	 */
	protected function checkModules()
	{
		if(!Main\Loader::includeModule("iblock"))
			throw new SystemException(Loc::getMessage("SOA_MODULE_IBLOCK_NOT_INSTALL"));
		
		if(!Main\Loader::includeModule("highloadblock"))
			throw new SystemException(Loc::getMessage("SOA_MODULE_HIGHTIBLOCK_NOT_INSTALL"));
		
		if(!Main\Loader::includeModule("tarakud.wishlist"))
			throw new SystemException(Loc::getMessage("SOA_MODULE_WISHLIST_NOT_INSTALL"));

		if (!Main\Loader::includeModule("sale") || !Main\Loader::includeModule("catalog") || !Main\Loader::includeModule("currency"))
			$this->isShop = false;
	}
	
	protected function getUser()
	{
		return intval($_REQUEST["USER"]);
	}
	
	protected function getList()
	{
		global $USER;
		
		$arId = array();
		$userId = $this->getUser();
		if ($userId <= 0 && $USER->IsAuthorized())
			$userId = $USER->GetID();

		if ($userId > 0)
		{
			$saveIblockId = COption::GetOptionInt(self::MODULE_ID, "wishlist_iblock", "", SITE_ID);
			if ($saveIblockId <= 0)
				return;
			
			$hlblock = HL\HighloadBlockTable::getById($saveIblockId)->fetch();
			if (empty($hlblock))
				return;
			
			$entity = HL\HighloadBlockTable::compileEntity($hlblock);
			
			$arSelect = array("*");
			$arFilter = array("UF_USER_ID" => $userId);
			$arHighload = array();
			$query = new Entity\Query($entity);
			$query->setSelect($arSelect);
			$query->setFilter($arFilter);
			$res = $query->exec();
			$result = new CDBResult($res);
			while ($arData = $result->Fetch())
			{
				$arId[] = $arData["UF_ELEMENT_ID"];
				$_SESSION["TARAKUD_WISHLIST"][$arData["UF_ELEMENT_ID"]] = $arData["UF_ELEMENT_ID"];
				
				$date = $arData["UF_DATE_INSERT"];
				$arData["DATE_INSERT"] = $date->toString(new \Bitrix\Main\Context\Culture(array("FORMAT_DATETIME" => "DD.MM.YYYY HH:MI:SS")));
				$arHighload[$arData["UF_ELEMENT_ID"]] = $arData;
			}
			
			Wishlist\Wishlist::setCookies();
		}
		elseif (isset($_SESSION["TARAKUD_WISHLIST"]) && is_array($_SESSION["TARAKUD_WISHLIST"]) 
			&& !empty($_SESSION["TARAKUD_WISHLIST"]))
		{
			$arId = $_SESSION["TARAKUD_WISHLIST"];
		}
		
		if (!empty($arId))
		{
			$arNavParams = $this->getNavParams();
			$priceType = $this->arParams["PRICE_CODE"];
			$arSelect = array(
				"ID",
				"IBLOCK_ID",
				"CODE",
				"XML_ID",
				"NAME",
				"ACTIVE",
				"DATE_ACTIVE_FROM",
				"DATE_ACTIVE_TO",
				"SORT",
				"PREVIEW_TEXT",
				"PREVIEW_TEXT_TYPE",
				"DETAIL_TEXT",
				"DETAIL_TEXT_TYPE",
				"DATE_CREATE",
				"CREATED_BY",
				"TIMESTAMP_X",
				"MODIFIED_BY",
				"TAGS",
				"IBLOCK_SECTION_ID",
				"DETAIL_PAGE_URL",
				"DETAIL_PICTURE",
				"PREVIEW_PICTURE"
			);
			$arFilter = array(
				"ID" => $arId, 
				"ACTIVE" => "Y",
			);
			$arSort = array(
				$this->arParams["ELEMENT_SORT_FIELD"] => $this->arParams["ELEMENT_SORT_ORDER"],
				$this->arParams["ELEMENT_SORT_FIELD2"] => $this->arParams["ELEMENT_SORT_ORDER2"],
			);
			$arResult = array("ITEMS" => array(), "ELEMENTS" => array());
			$res = CIBlockElement::GetList($arSort, $arFilter, false, $arNavParams, $arSelect);
			while ($obElement = $res->GetNextElement())
			{
				$arRow = $obElement->GetFields();
				$arRow["PROPERTIES"] = $obElement->GetProperties();
				
				if (isset($arHighload[$arRow["ID"]]["DATE_INSERT"]))
					$arRow["DATE_INSERT"] = $arHighload[$arRow["ID"]]["DATE_INSERT"];
				
				$arRow["PREVIEW_PICTURE"] = ($arRow["PREVIEW_PICTURE"] > 0 ? CFile::GetFileArray($arRow["PREVIEW_PICTURE"]) : false);
				$arRow["DETAIL_PICTURE"] = ($arRow["DETAIL_PICTURE"] > 0 ? CFile::GetFileArray($arRow["DETAIL_PICTURE"]) : false);
				
				$pic = false;
				if ($arRow["DETAIL_PICTURE"])
					$pic = $arRow["DETAIL_PICTURE"];
				elseif ($arRow["PREVIEW_PICTURE"])
					$pic = $arRow["PREVIEW_PICTURE"];
				
				if ($pic)
				{
					$arFile = CFile::ResizeImageGet($pic, array('width'=>$this->arParams["IMG_WIDTH"], 'height'=>$this->arParams["IMG_HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);
					$arRow["PICTURE"] = array(
						"WIDTH" => $arFile["width"],
						"HEIGHT" => $arFile["height"],
						"SRC" => $arFile["src"],
					);
				}
				
				if($this->isShop)
				{
					$arPrice = CCatalogProduct::GetOptimalPrice($arRow["ID"], 1, $USER->GetUserGroupArray(), "N");
					$arPrice["PRICE"]["DISCOUNT_PRICE"] = $arPrice["DISCOUNT_PRICE"];
					$arPrice["PRICE"]["PRINT_VALUE"] = CurrencyFormat($arPrice["PRICE"]["PRICE"], $arPrice["PRICE"]["CURRENCY"]);
					$arPrice["PRICE"]["PRINT_DISCOUNT_VALUE"] = CurrencyFormat($arPrice["PRICE"]["DISCOUNT_PRICE"], $arPrice["PRICE"]["CURRENCY"]);
					$arPrice["PRICE"]["DISCOUNT"] = $arPrice["DISCOUNT"];
					$arRow["PRICE"] = $arPrice["PRICE"];
				}
				
				$iblockId = $arRow["IBLOCK_ID"];
				$arResult["ELEMENTS"][] = $arRow["ID"];
				$arResult["ITEMS"][$arRow["ID"]] = $arRow;
			}

			$navComponentParameters = array();
			$arResult["NAV_STRING"] = $res->GetPageNavStringEx(
				$navComponentObject,
				$this->arParams["PAGER_TITLE"],
				$this->arParams["PAGER_TEMPLATE"],
				$this->arParams["PAGER_SHOW_ALWAYS"],
				$this,
				$navComponentParameters
			);
		}
		
		if($this->isShop && $iblockId > 0 && !empty($arResult["ELEMENTS"]))
		{
			$arResultPrices = CIBlockPriceTools::GetCatalogPrices($iblockId, $this->arParams["PRICE_CODE"]);
			
			$arOffers = CIBlockPriceTools::GetOffersArray(
				$iblockId,
				$arResult["ELEMENTS"],
				array("ID" => "ASC"),
				array(),
				array(),
				0,
				$arResultPrices,
				"N",
				array()
			);
			if(!empty($arOffers))
			{
				foreach($arOffers as $arOffer)
				{
					if (isset($arResult["ITEMS"][$arOffer["LINK_ELEMENT_ID"]]))
					{
						$arOffer["MIN_PRICE"]["MIN_PRICE_FROM"] = Loc::getMessage("PRICE_FROM")." ".$arOffer["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"];
						$arResult["ITEMS"][$arOffer["LINK_ELEMENT_ID"]]["PRICE"] = $arOffer["MIN_PRICE"];
					}
				}
			}
			unset($arOffers);
			unset($arOffer);
		}
		
		$arResult["RECORD_COUNT"] = $res->NavRecordCount;
		
		return $arResult;
	}
	
	protected function getNavParams()
	{
		if ($this->arParams['DISPLAY_TOP_PAGER'] || $this->arParams['DISPLAY_BOTTOM_PAGER'])
		{
			$arNavParams = array(
				"nPageSize" => $this->arParams["PAGE_ELEMENT_COUNT"],
				"bDescPageNumbering" => $this->arParams["PAGER_DESC_NUMBERING"],
				"bShowAll" => $this->arParams["PAGER_SHOW_ALL"]
			);
		}
		else
		{
			$arNavParams = array(
				"nTopCount" => $this->arParams["PAGE_ELEMENT_COUNT"],
				"bDescPageNumbering" => $this->arParams["PAGER_DESC_NUMBERING"],
			);
		}
		
		return $arNavParams;
	}
	
	protected function getNavigation()
	{
		$arNavigation = false;
		
		if ($this->arParams['DISPLAY_TOP_PAGER'] || $this->arParams['DISPLAY_BOTTOM_PAGER'])
		{
			$arNavParams = $this->getNavParams();
			$arNavigation = CDBResult::GetNavParams($arNavParams);
			if($arNavigation["PAGEN"]==0 && $this->arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
				$this->arParams["CACHE_TIME"] = $this->arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];
		}
		
		return $arNavigation;
	}
	
	protected function getWishCacheId()
	{
		global $USER;
		
		$group = $USER->GetGroups();
		$ids = $_COOKIE["TARAKUD_WISHLIST"];
		$nav = $this->getNavigation();
		$user = intval($_REQUEST["USER"]);

		return array($group, $ids, $nav, $user);
	}
	
	/** 
	* Определение наличия элемента в списке желаний
	* 
	* @return boolean
	*/ 
	public function isElementId($id)
	{
		if (isset($_SESSION["TARAKUD_WISHLIST"][$id]))
			return true;
		
		return false;
	}
	
	protected function deleteItem()
	{
		global $USER;
		
		$id = intval($_REQUEST["id"]);
		if ($id <= 0 || !$this->isElementId($id))
			return;
		
		if ($USER->IsAuthorized())
			Wishlist\Wishlist::delete($id);
		else
			unset($_SESSION["TARAKUD_WISHLIST"][$id]);
		
		Wishlist\Wishlist::setCookies();
		
		LocalRedirect($this->arParams["WISHLIST_URL"]);
	}
	
	/**
	 * Start Component
	 */
	public function executeComponent()
	{
		global $APPLICATION, $USER;
		try
		{
			$this->arResult = array();
			$this->checkModules();
			$this->deleteItem();
			//if($this->StartResultCache(false, array($this->getWishCacheId())))
				$this->arResult = $this->getList();
			
			//$this->setResultCacheKeys( array() );
			$access = false;
			$delete = true;
			if ($USER->IsAuthorized())
				$access = true;
			if ($this->getUser() > 0)
			{
				$access = false;
				$delete = false;;
			}
			
			$this->arResult["PERSONAL"] = array(
				"ACCESS_SOCIAL" => $access,
				"ACCESS_DELETE" => $delete,
				"SHARE_URL" => SITE_SERVER_NAME.$this->arParams["WISHLIST_URL"]."wl".$USER->getId()."/",
			);
			
			$this->arResult["AUTH"] = false;
			if ($USER->IsAuthorized())
				$this->arResult["AUTH"] = true;
			
			if($this->arParams["SET_TITLE"])
			{
				$APPLICATION->SetTitle(GetMessage('WISH_PAGE_TITLE'));
			}

			$this->IncludeComponentTemplate();
		}
		catch (SystemException $e)
		{
			ShowError($e->getMessage());
		}
	}
}

?>