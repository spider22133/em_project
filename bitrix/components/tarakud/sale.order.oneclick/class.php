<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main;
use Bitrix\Iblock;
use Bitrix\Catalog;
use Bitrix\Main\Localization\Loc as Loc;
use Bitrix\Main\SystemException as SystemException;

CUtil::InitJSCore(array('ajax', 'popup'));

class CSimpleComponent extends CBitrixComponent
{
	protected $userMail = "";
	protected $arBasketItems = array();
	protected $arPropEmail = array();
	protected $arPropValues = array();
	protected $arBasketDelayId = array();
	protected $arPropsValue = array();
	protected $lastOrderId = 0;
	protected $orderId = 0;
	protected $isAuth = false;
	protected $submitPost = "N";
	protected $ajaxUrl = "/bitrix/components/tarakud/sale.order.oneclick/ajax.php";
	public $isShop = true;

	protected function getCurrency()
	{
		$currency = COption::GetOptionString("sale", "default_currency", "RUB");

		return $currency;
	}

	protected function isAjax()
	{
		if(($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["ajax"] == "Y" && check_bitrix_sessid())
			|| $arParams["AJAX"] == "Y")
			return true;
		else
			return false;
	}

	protected function getPostSiteId()
	{
		$siteId = SITE_ID;

		if($this->isAjax() && strlen($this->arResult["POST"]["site_id"]) > 0)
		{
			$siteId = $this->arResult["POST"]["site_id"];
		}

		return $siteId;
	}

	/**
	 * Auto prepare Component Params
	 */
	public function onPrepareComponentParams($params)
	{
		global $APPLICATION;
		
		$params["IBLOCK_ID"] = intval($params["IBLOCK_ID"]);
		$params["IMAGE_HEIGHT"] = intval($params["IMAGE_HEIGHT"]);
		$params["IMAGE_WIDTH"] = intval($params["IMAGE_WIDTH"]);
		$params["AJAX"] = (($params["AJAX"] == "Y") ? "Y" : "N");
		$params["IS_JQUERY"] = (($params["IS_JQUERY"] == "Y") ? "Y" : "N");
		$params["USE_CAPTCHA"] = (($params["USE_CAPTCHA"] == "Y") ? "Y" : "N");
		$params["USE_COMMENT"] = (($params["USE_COMMENT"] == "Y") ? "Y" : "N");
		$params["USE_COUNT"] = (($params["USE_COUNT"] == "Y") ? "Y" : "N");
		$params["USE_USER"] = (($params["USE_USER"] == "Y") ? "Y" : "N");
		$params["PERSONAL_DATA"] = (($params["PERSONAL_DATA"] == "Y") ? "Y" : "N");
		$params["PRODUCT_PRICE"] = floatval($params["PRODUCT_PRICE"]);
		$params["OFFERS_SHOW"] = trim($params["OFFERS_SHOW"]);

		if ($params["IS_JQUERY"] == "Y")
			CJSCore::Init(array("jquery"));

		$params["CONFIRM_ORDER"] = strip_tags($params["CONFIRM_ORDER"]);
		if (strlen($params["CONFIRM_ORDER"]) <= 0)
			$params["CONFIRM_ORDER"] = GetMessage("SALE_CONFIRM_ORDER_OK");

		$params["TITLE_POPUP"] = trim($params["TITLE_POPUP"]);
		if (strlen($params["TITLE_POPUP"]) <= 0)
			$params["TITLE_POPUP"] = GetMessage("SALE_TITLE_POPUP_VALUE");

		$params["ORDER_PRODUCT"] = intval($params["ORDER_PRODUCT"]);
		if ($params["ORDER_PRODUCT"] <= 0)
			$params["ORDER_PRODUCT"] = 1;

		if(strlen($params["PRICE_CODE"]) <= 0 && Main\Loader::includeModule("catalog"))
		{
			$dbRes = CCatalogGroup::GetList(
				array(),
				array("BASE" => "Y"),
				false,
				false,
				array()
			);
			if ($arPriceType = $dbRes->Fetch())
				$params["PRICE_CODE"] = $arPriceType["NAME"];
		}

		$params["PERSON_TYPE"] = intval($params["PERSON_TYPE"]);
		$params["PAYSYSTEM"] = intval($params["PAYSYSTEM"]);
		$params["PRODUCT_NAME"] = trim($params["PRODUCT_NAME"]);
		$params["PRODUCT_WEIGHT"] = floatval($params["PRODUCT_WEIGHT"]);
		$params["USER_ID"] = intval($params["USER_ID"]);
		$params["HIDE_BUTTON"] = (($params["HIDE_BUTTON"] == "Y") ? "Y" : "N");
		$params["DELIVERY_SHOW"] = (($params["DELIVERY_SHOW"] == "Y") ? "Y" : "N");
		$params["DELIVERY"] = trim($params["DELIVERY"]);
		$params["DELIVERY_LIST_SHOW"] = (($params["DELIVERY_LIST_SHOW"] == "Y") ? "Y" : "N");
		$params["PAYSYSTEM_LIST_SHOW"] = (($params["PAYSYSTEM_LIST_SHOW"] == "Y") ? "Y" : "N");

		$params["IMAGE_WIDTH"] = intval($params["IMAGE_WIDTH"]);
		if ($params["IMAGE_WIDTH"] <= 0)
			$params["IMAGE_WIDTH"] = 130;
		$params["IMAGE_HEIGHT"] = intval($params["IMAGE_HEIGHT"]);
		if ($params["IMAGE_HEIGHT"] <= 0)
			$params["IMAGE_HEIGHT"] = 130;

		if(!isset($params["OFFERS_PROPERTY_CODE"]))
			$params["OFFERS_PROPERTY_CODE"] = array();
		elseif (!is_array($params["OFFERS_PROPERTY_CODE"]))
			$params["OFFERS_PROPERTY_CODE"] = array($params["OFFERS_PROPERTY_CODE"]);
		foreach($params["OFFERS_PROPERTY_CODE"] as $key => $value)
			if($value === "")
				unset($params["OFFERS_PROPERTY_CODE"][$key]);

		$params["EVENT_NAME"] = "SALE_NEW_ORDER";
		$params["ORDER_STATUS"] = trim($params["ORDER_STATUS"]);

		return $params;
	}

	protected function getPersonType()
	{
		if (!$this->isShop)
			return;

		$arResult = array();
		$arFilter = array("PERSON_TYPE_ID" => $this->arParams["PERSON_TYPE"], "ACTIVE" => "Y");
		if(!empty($this->arParams["PERSON_TYPE_PROPS"])
			&& $this->arParams["PERSON_TYPE_PROPS"][0] != 0)
		{
			$arFilter["ID"] = $this->arParams["PERSON_TYPE_PROPS"];
		}
			
		$cache_id = md5(serialize($arFilter));
		$cache_dir = "/tarakud_persontype";
		$obCache = new CPHPCache;
		if($obCache->InitCache(3600000, $cache_id, "/"))
		{
			$vars = $obCache->GetVars();
			$arResult = $vars["PERSON_TYPE_PROPS"];
		}
		elseif($obCache->StartDataCache())
		{
			$dbProp = CSaleOrderProps::GetList(
				array("SORT" => "ASC", "NAME" => "ASC"),
				$arFilter
			);
			while($arProp = $dbProp->Fetch())
			{
				$arProp["FIELD_NAME"] = "ORDER_PROP_".$arProp["ID"];
				$arResult[$arProp["ID"]] = $arProp;
			}

			$obCache->EndDataCache(array(
				"PERSON_TYPE_PROPS" => $arResult
			));
		}

		$this->arResult["PERSON_TYPE_PROPS"] = $arResult;
	}

	protected function getDelivery()
	{
		if (!$this->isShop)
			return;

		$arResult = array();
		if ($this->arParams["DELIVERY_SHOW"] == "Y")
		{
			$cache_id = md5($this->arParams["DELIVERY"]);
			$cache_dir = "/tarakud_delivery";
			$obCache = new CPHPCache;
			if($obCache->InitCache(3600000, $cache_id, "/"))
			{
				$vars = $obCache->GetVars();
				$arResult = $vars["DELIVERY"];
			}
			else
			{
				$resDev = CSaleDelivery::GetList(
					array("SORT"=>"ASC"),
					array("ACTIVE" => "Y", "ID" => $this->arParams["DELIVERY"])
				);
				if ($arDev = $resDev->GetNext())
				{
					$arDev["PRICE_PRINT"] = SaleFormatCurrency($arDev["PRICE"], $arDev["CURRENCY"]);
					$arResult = $arDev;
				}
			}
			if($obCache->StartDataCache())
			{
				$obCache->EndDataCache(array(
					"DELIVERY" => $arResult
				));
			}
		}

		$this->arResult["DELIVERY"] = $arResult;
	}

	protected function getPost()
	{
		global $APPLICATION;

		if($this->isAjax())
		{
			//CUtil::DecodeUriComponent($_POST);

			if (empty($this->arResult["ERROR_MESSAGE"]))
			{
				$this->submitPost = "Y";
				foreach ($_POST as $vname => $vvalue)
				{
					if(is_array($vvalue))
					{
						foreach($vvalue as $k => $v)
							$this->arResult["POST"][htmlspecialcharsbx($vname)][] = htmlspecialcharsbx($v);
					}
					else
						$this->arResult["POST"][htmlspecialcharsbx($vname)] = htmlspecialcharsbx($vvalue);
				}
			}
		}
	}

	protected function getCaptha()
	{
		global $APPLICATION;

		if($this->arParams["USE_CAPTCHA"] == "Y" && $this->isAjax())
		{
			include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
			$captcha_code = trim($_POST["captcha_sid"]);
			$captcha_word = trim($_POST["captcha_word"]);
			$cpt = new CCaptcha();
			$captchaPass = COption::GetOptionString("main", "captcha_password", "");
			if (strlen($captcha_word) > 0 && strlen($captcha_code) > 0)
			{
				if (!$cpt->CheckCodeCrypt($captcha_word, $captcha_code, $captchaPass))
					$this->arResult["ERROR_MESSAGE"][] = GetMessage("SALE_CAPTCHA_WRONG");
			}
			else
				$this->arResult["ERROR_MESSAGE"][] = GetMessage("SALE_CAPTHCA_EMPTY");
		}
		else
			$this->arResult["capCode"] =  htmlspecialcharsbx($APPLICATION->CaptchaGetCode());
	}
	
	//get last order props order
	protected function getProfileData()
	{
		global $USER;

		if (!$this->isShop)
			return;

		$cache_id = md5($USER->GetID());
		$cache_dir = "/tarakud_order";
		$obCache = new CPHPCache;
		if ($obCache->InitCache(3600000, $cache_id, $cache_dir))
		{
			$vars = $obCache->GetVars();
			$this->lastOrderId = $vars["LAST_ORDER"];
			$this->arPropsValue = $vars["PROPS_VALUE"];
		}
		elseif ($obCache->StartDataCache())
		{
			$resOrder = CSaleOrder::GetList(
				array('ID' => 'DESC'),
				array('USER_ID' => $USER->GetID()),
				false,
				false,
				array('ID')
			);
			if ($arOrder = $resOrder->Fetch())
			{
				$this->lastOrderId = $arOrder['ID'];

				$resPropsValue = CSaleOrderPropsValue::GetList(
					array('ORDER_ID' => 'DESC'),
					array('ORDER_ID' => $this->lastOrderId)
				);
				while($tmpPropsValue = $resPropsValue->Fetch())
					$this->arPropsValue[$tmpPropsValue['ORDER_PROPS_ID']] = $tmpPropsValue['VALUE'];

				$obCache->EndDataCache(array(
					"LAST_ORDER" => $this->lastOrderId,
					"PROPS_VALUE" => $this->arPropsValue
				));
			}
			else
				$obCache->AbortDataCache();
		}
	}

	protected function getOrderProps()
	{
		global $USER;

		if (!$this->isShop)
			return;

		if (empty($this->arResult["ERROR_MESSAGE"]))
		{
			foreach ($this->arResult["PERSON_TYPE_PROPS"] as &$arOrderProps)
			{
				$bErrorField = False;
				$curVal = $this->arPropsValue[$arOrderProps["DEFAULT_VALUE"]];

				if (is_set($this->arPropsValue[$arOrderProps["ID"]]))
					$curVal = $this->arPropsValue[$arOrderProps["ID"]];
				if (is_set($this->arResult["POST"]["ORDER_PROP_".$arOrderProps["ID"]]))
					$curVal = $this->arResult["POST"]["ORDER_PROP_".$arOrderProps["ID"]];

				if (!is_array($curVal) && strlen($curVal) > 0)
					$arOrderProps["VALUE"] = $curVal;

				if ($arOrderProps["TYPE"]=="LOCATION" && ($arOrderProps["IS_LOCATION"]=="Y" || $arOrderProps["IS_LOCATION4TAX"]=="Y"))
				{
					if ($arOrderProps["IS_LOCATION"]=="Y")
						$arUserResult["DELIVERY_LOCATION"] = IntVal($curVal);
					if ($arOrderProps["IS_LOCATION4TAX"]=="Y")
						$arUserResult["TAX_LOCATION"] = IntVal($curVal);

					if (IntVal($curVal)<=0)
						$bErrorField = True;
				}
				elseif ($arOrderProps["IS_PROFILE_NAME"]=="Y" || $arOrderProps["IS_PAYER"]=="Y" || $arOrderProps["IS_EMAIL"]=="Y" || $arOrderProps["IS_ZIP"]=="Y")
				{
					if ($arOrderProps["IS_PROFILE_NAME"]=="Y")
					{
						$arUserResult["PROFILE_NAME"] = Trim($curVal);
						if (strlen($arUserResult["PROFILE_NAME"])<=0)
							$bErrorField = True;
					}
					if ($arOrderProps["IS_PAYER"]=="Y")
					{
						$arUserResult["PAYER_NAME"] = Trim($curVal);
						if (strlen($arUserResult["PAYER_NAME"])<=0)
							$bErrorField = True;
					}
					if ($arOrderProps["IS_EMAIL"]=="Y")
					{
						$arUserResult["USER_EMAIL"] = Trim($curVal);
						$this->userMail = $arUserResult["USER_EMAIL"];
						if (strlen($arUserResult["USER_EMAIL"])<=0)
							$bErrorField = True;
						elseif(!check_email($arUserResult["USER_EMAIL"]))
							$this->arResult["ERROR_MESSAGE"][] = GetMessage("SALE_ERROR_EMAIL");
					}
					if ($arOrderProps["IS_ZIP"]=="Y")
					{
						$arOrderProps["REQUIED"] ="Y";
						$arUserResult["DELIVERY_LOCATION_ZIP"] = Trim($curVal);
						if (strlen($arUserResult["DELIVERY_LOCATION_ZIP"])<=0)
							$bErrorField = True;
					}
				}
				elseif ($arOrderProps["REQUIED"]=="Y")
				{
					if ($arOrderProps["TYPE"]=="TEXT" || $arOrderProps["TYPE"]=="TEXTAREA" || $arOrderProps["TYPE"]=="RADIO" || $arOrderProps["TYPE"]=="SELECT" || $arOrderProps["TYPE"] == "CHECKBOX")
					{
						if (strlen($curVal)<=0)
							$bErrorField = True;
					}
					elseif ($arOrderProps["TYPE"]=="LOCATION")
					{
						if (IntVal($curVal)<=0)
							$bErrorField = True;
					}
					elseif ($arOrderProps["TYPE"]=="MULTISELECT")
					{
						if (!is_array($curVal) || count($curVal)<=0)
							$bErrorField = True;
					}
				}

				if ($bErrorField)
					$this->arResult["ERROR_MESSAGE"][] = GetMessage("SALE_ERROR_REQUIRE")." \"".$arOrderProps["NAME"]."\"";

				if ($arOrderProps["TYPE"] == "TEXT")
				{
					if ($arOrderProps["IS_EMAIL"] == "Y")
					{
						if (strlen($curVal) <= 0 && $this->submitPost == "N")
							$arOrderProps["VALUE"] = $USER->GetEmail();

						$this->userMail = $arOrderProps["VALUE"];
					}
					elseif ($arOrderProps["IS_PAYER"] == "Y")
					{
						if (strlen($curVal) <= 0 && $this->submitPost == "N")
							$arOrderProps["VALUE"] = $USER->GetFullName();
					}
					else
					{
						if (strlen($curVal) <= 0 && $this->submitPost == "N")
							$arOrderProps["VALUE"] = htmlspecialcharsEx($arOrderProps["DEFAULT_VALUE"]);
					}
				}
				elseif ($arOrderProps["TYPE"] == "CHECKBOX")
				{
					if (($arOrderProps["DEFAULT_VALUE"]=="Y" && $this->submitPost == "N")
						|| ($curVal == "Y" && $this->submitPost == "Y") )
					{
						$arOrderProps["CHECKED"] = "Y";
						$arOrderProps["VALUE_FORMATED"] = GetMessage("SALE_Y");
					}
					else
						$arOrderProps["VALUE_FORMATED"] = GetMessage("SALE_N");
				}
				elseif ($arOrderProps["TYPE"] == "TEXTAREA")
				{
					$arOrderProps["SIZE2"] = ((IntVal($arOrderProps["SIZE2"]) > 0) ? $arOrderProps["SIZE2"] : 4);
					$arOrderProps["SIZE1"] = ((IntVal($arOrderProps["SIZE1"]) > 0) ? $arOrderProps["SIZE1"] : 40);

					if (strlen($curVal) <= 0 && $this->submitPost == "N")
						$arOrderProps["VALUE"] = htmlspecialcharsEx($arOrderProps["DEFAULT_VALUE"]);
					$arOrderProps["VALUE_FORMATED"] = $arOrderProps["VALUE"];
				}
				elseif ($arOrderProps["TYPE"] == "RADIO")
				{
					$dbVariants = CSaleOrderPropsVariant::GetList(
							array("SORT" => "ASC"),
							array("ORDER_PROPS_ID" => $arOrderProps["ID"]),
							false,
							false,
							array("*")
					);
					while ($arVariants = $dbVariants->GetNext())
					{
						if ($arVariants["VALUE"] == $curVal || (!isset($curVal) && $arVariants["VALUE"] == $arOrderProps["DEFAULT_VALUE"]))
						{
							$arVariants["CHECKED"]="Y";
							$arOrderProps["VALUE_FORMATED"] = $arVariants["NAME"];
						}

						$arOrderProps["VARIANTS"][] = $arVariants;
					}
				}
				elseif ($arOrderProps["TYPE"] == "MULTISELECT")
				{
					$arOrderProps["FIELD_NAME"] = "ORDER_PROP_".$arOrderProps["ID"].'[]';
					$arOrderProps["SIZE1"] = ((IntVal($arOrderProps["SIZE1"]) > 0) ? $arOrderProps["SIZE1"] : 5);

					if (!is_array($curVal) && strlen($curVal) > 0)
					{
						$curVal = trim($curVal, ",");
						$curVal = explode(",", $curVal);
					}

					$arDefVal = explode(",", $arOrderProps["DEFAULT_VALUE"]);
					for ($i = 0, $intCount = count($arDefVal); $i < $intCount; $i++)
						if (strlen($arDefVal[$i]) > 0)
							$arDefVal[$i] = Trim($arDefVal[$i]);

					$dbVariants = CSaleOrderPropsVariant::GetList(
						array("SORT" => "ASC"),
						array("ORDER_PROPS_ID" => $arOrderProps["ID"]),
						false,
						false,
						array("*")
					);
					$i = 0;
					while ($arVariants = $dbVariants->GetNext())
					{
						if ((is_array($curVal) && $this->submitPost == "Y" && in_array($arVariants["VALUE"], $curVal))
							|| (!isset($curVal) && $this->submitPost == "N" && in_array($arVariants["VALUE"], $arDefVal)))
						{
							$arVariants["SELECTED"] = "Y";
							if ($i > 0)
								$arOrderProps["VALUE_FORMATED"] .= ", ";
							$arOrderProps["VALUE_FORMATED"] .= $arVariants["NAME"];
							$i++;
						}
						$arOrderProps["VARIANTS"][] = $arVariants;
					}

					if (is_array($curVal))
					{
						$newVal = "";
						foreach ($curVal as $val)
							$newVal .= $val.",";

						$newVal = trim($newVal, ",");

						$arOrderProps["VALUE"] = $newVal;
					}
				}
				elseif ($arOrderProps["TYPE"] == "SELECT")
				{
					$arOrderProps["SIZE1"] = ((IntVal($arOrderProps["SIZE1"]) > 0) ? $arOrderProps["SIZE1"] : 1);
					$dbVariants = CSaleOrderPropsVariant::GetList(
							array("SORT" => "ASC", "NAME" => "ASC"),
							array("ORDER_PROPS_ID" => $arOrderProps["ID"]),
							false,
							false,
							array("*")
					);
					$flagDefault = "N";
					$nameProperty = "";
					while ($arVariants = $dbVariants->GetNext())
					{
						if ($flagDefault == "N" && $nameProperty == "")
							$nameProperty = $arVariants["NAME"];

						if (($arVariants["VALUE"] == $curVal) || ((!isset($curVal) || $curVal == "") && ($arVariants["VALUE"] == $arOrderProps["DEFAULT_VALUE"])))
						{
							$arVariants["SELECTED"] = "Y";
							$arOrderProps["VALUE_FORMATED"] = $arVariants["NAME"];
							$flagDefault = "Y";
						}
						$arOrderProps["VARIANTS"][] = $arVariants;
					}
					if ($flagDefault == "N")
					{
						$arOrderProps["VARIANTS"][0]["SELECTED"]= "Y";
						$arOrderProps["VARIANTS"][0]["VALUE_FORMATED"] = $nameProperty;
					}
				}
				elseif ($arOrderProps["TYPE"] == "LOCATION")
				{
					$arOrderProps["SIZE1"] = ((IntVal($arOrderProps["SIZE1"]) > 0) ? $arOrderProps["SIZE1"] : 1);
					$dbVariants = CSaleLocation::GetList(
							array("SORT" => "ASC", "COUNTRY_NAME_LANG" => "ASC", "CITY_NAME_LANG" => "ASC"),
							array("LID" => LANGUAGE_ID),
							false,
							false,
							array("ID", "COUNTRY_NAME", "CITY_NAME", "SORT", "COUNTRY_NAME_LANG", "CITY_NAME_LANG")
					);
					while ($arVariants = $dbVariants->GetNext())
					{
						if ((IntVal($arVariants["ID"]) == IntVal($curVal) && $this->submitPost == "Y")
							|| (!isset($curVal) && $this->submitPost == "N" && IntVal($arVariants["ID"]) == IntVal($arOrderProps["DEFAULT_VALUE"])))
						{
							$arVariants["SELECTED"] = "Y";
							$arOrderProps["VALUE_FORMATED"] = $arVariants["COUNTRY_NAME"].((strlen($arVariants["CITY_NAME"]) > 0) ? " - " : "").$arVariants["CITY_NAME"];
							$arOrderProps["VALUE"] = $arVariants["ID"];
						}
						$arVariants["NAME"] = $arVariants["COUNTRY_NAME"].((strlen($arVariants["CITY_NAME"]) > 0) ? " - " : "").$arVariants["CITY_NAME"];
						$arOrderProps["VARIANTS"][] = $arVariants;
					}
					if(count($arOrderProps["VARIANTS"]) == 1)
						$arOrderProps["VALUE"] = $arOrderProps["VARIANTS"][0]["ID"];
				}

				if (strlen($arOrderProps["CODE"]) > 0)
					$this->arPropEmail[$arOrderProps["CODE"]] = $arOrderProps["VALUE"];

				$this->arPropValues[$arOrderProps["ID"]] = $arOrderProps["VALUE"];
			}
		}
	}

	protected function saveOrder()
	{
		if($this->isAjax() && empty($this->arResult["ERROR_MESSAGE"]))
		{
			$this->arResult["POST"]["quantity"] = floatval($this->arResult["POST"]["quantity"]);
			if ($this->arResult["POST"]["quantity"] <= 0)
				$this->arResult["POST"]["quantity"] = 1;

			$this->addUser();
			if ($this->isShop)
			{
				$this->addToBasket();
				$this->addOrder();
			}
			else
			{
				$this->addOrderStart();
			}
			
			$this->logoutUser();
		}
	}

	protected function addUser()
	{
		global $USER;

		$newUserId = 0;
		if (!$USER->IsAuthorized())
		{
			$this->isAuth = true;
			if ($this->arParams["USE_USER"] == "Y" && $this->arParams["USER_ID"] > 0)
			{
				$rsUser = CUser::GetList($by, $order, array("ACTIVE" => "Y", "ID" => $this->arParams["USER_ID"]), array("FIELDS" => array("ID")));
				if ($arUser = $rsUser->Fetch())
					$newUserId = $arUser["ID"];
			}
			else
			{
				if (strlen($this->userMail) <= 0)
				{
					$userLogin = 'user_'.mt_rand(10000,1000000);
					$serverName = (strlen(SITE_SERVER_NAME) > 0) ? SITE_SERVER_NAME : 'site.com';
					$this->userMail = $userLogin.'@'.$serverName;
				}
				
				$login = $_COOKIE[COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"];
				$rsUser = CUser::GetList(
					$by="id",
					$order="asc",
					array("ACTIVE"=>"Y", "EMAIL"=>$this->userMail, "LOGIN"=>$login)
				);
				if ($arUser = $rsUser->Fetch())
				{
					$newUserId = $arUser["ID"];
					
					$res = CUser::GetUserGroupList($newUserId);
					while ($arGroup = $res->Fetch())
					{
						if ($arGroup["GROUP_ID"] == 1)
						{
							$this->arResult["ERROR_MESSAGE"][] = GetMessage("SALE_ERROR_REG");
							return;
						}
					}
				}
				else
				{
					$arRegister = $USER->SimpleRegister($this->userMail);
					if ($arRegister["TYPE"] == "OK")
					{
						$rsUser = CUser::GetList(
							$by="id",
							$order="asc",
							array("ACTIVE"=>"Y", "EMAIL"=>$this->userMail)
						);
						if ($arUser = $rsUser->Fetch())
						{
							$newUserId = $arUser["ID"];
						}
					}
					elseif ($arRegister["TYPE"] == "ERROR")
						$this->arResult["ERROR_MESSAGE"][] = $arRegister["MESSAGE"];
				}
			}

			if ($newUserId > 0)
			{
				$USER->Authorize($newUserId);
			}
		}
	}

	protected function addToBasket()
	{
		global $USER;

		$arErrors = array();
		$arWarnings = array();
		$arCupon = array();
		$siteId = $this->getPostSiteId();
		
		//откладываем товары в корзине
		if ($this->arParams["ORDER_PRODUCT"] != 1)
		{
			$fUserId = IntVal(CSaleBasket::GetBasketUserID());
			$arFields = array("DELAY" => "Y");
			$resCartItems = CSaleBasket::GetList(
				array("ID" => "ASC"),
				array(
					"FUSER_ID" => $fUserId,
					"LID" => $siteId,
					"ORDER_ID" => "NULL",
					"DELAY" => "N",
				),
				false,
				false,
				array("ID")
			);
			while ($arBasket = $resCartItems->Fetch())
			{
				CSaleBasket::Update($arBasket["ID"], $arFields);
				$this->arBasketDelayId[] = $arBasket["ID"];
			}
		}
		
		if ($this->arParams["ORDER_PRODUCT"] == 3)
		{
			$productId = mt_rand(1000,100000);

			$arFields = array(
				"PRODUCT_ID" => $productId,
				"PRODUCT_PRICE_ID" => 0,
				"PRICE" => $this->arParams["PRODUCT_PRICE"],
				"CURRENCY" => $this->getCurrency(),
				"WEIGHT" => $this->arParams["PRODUCT_WEIGHT"],
				"QUANTITY" => $this->arResult["POST"]["quantity"],
				"LID" => $this->arResult["POST"]["site_id"],
				"DELAY" => "N",
				"CAN_BUY" => "Y",
				"NAME" => $this->arParams["PRODUCT_NAME"],
				"MODULE" => "oneclick",
				"NOTES" => GetMessage('SALE_PRODUCT_NOTES'),
				"DISCOUNT_PRICE" => 0,
				"VAT_RATE" => 0,
				"DETAIL_PAGE_URL" => $this->arParams["PRODUCT_URL"],
				"CATALOG_XML_ID" => "oneclick"
			);
			CSaleBasket::Add($arFields);
		}

		if ($this->arParams["ORDER_PRODUCT"] == 2)
		{
			$this->arResult["POST"]["element_offers"] = intval($this->arResult["POST"]["element_offers"]);
			if ($this->arResult["POST"]["element_offers"] > 0)
				$this->arParams["PRODUCT_ID"] = $this->arResult["POST"]["element_offers"];

			if ($this->arParams["PRODUCT_ID"] > 0)
			{
				$productProperties = CIBlockPriceTools::GetOfferProperties(
					$this->arParams["PRODUCT_ID"],
					$this->arParams["IBLOCK_ID"],
					$this->arParams["OFFERS_PROPERTY_CODE"]
				);

				$arRewriteFields = array(
					"CALLBACK_FUNC" => "CatalogBasketCallback",
					"ORDER_CALLBACK_FUNC" => "CatalogBasketOrderCallback",
					"CANCEL_CALLBACK_FUNC" => "CatalogBasketCancelCallback",
					"PAY_CALLBACK_FUNC" => "CatalogPayOrderCallback",
					"LID" => $this->arResult["POST"]["site_id"],
					"CATALOG_XML_ID" => "oneclick",
				);
				
				$quantity = floatval($this->arResult["POST"]["quantity"]);
				if ($quantity <= 0)
					$quantity = 1;
				/*$rsRatios = CCatalogMeasureRatio::getList(
					array(),
					array('PRODUCT_ID' => $this->arParams["PRODUCT_ID"]),
					false,
					false,
					array('PRODUCT_ID', 'RATIO')
				);
				if ($arRatio = $rsRatios->Fetch())
				{
					$intRatio = (int)$arRatio['RATIO'];
					$dblRatio = doubleval($arRatio['RATIO']);
					$ratio = ($dblRatio > $intRatio ? $dblRatio : $intRatio);
				}
				else*/
					$ratio = 1;
				
				$quantity = $quantity * $ratio;

				Add2BasketByProductID($this->arParams["PRODUCT_ID"], $quantity, $arRewriteFields, $productProperties);
			}
			else
				$this->arResult["ERROR_MESSAGE"][] = GetMessage('SALE_PRODUCT_NULL');
		}

		$arShoppingCart = CSaleBasket::DoGetUserShoppingCart($siteId, IntVal($USER->GetID()), IntVal(CSaleBasket::GetBasketUserID()), $arErrors, $arCupon);

		//delete another
		/*if ($this->arParams["ORDER_PRODUCT"] == 2)
			$currentProductId = $this->arParams["PRODUCT_ID"];
		if ($this->arParams["ORDER_PRODUCT"] == 3)
			$currentProductId = $productId;
		
		if ($this->arParams["ORDER_PRODUCT"] == 2 && isset($this->arResult["POST"]["element_offers"]) 
			&& $this->arResult["POST"]["element_offers"] > 0)
			$currentProductId = $this->arResult["POST"]["element_offers"];
			
		if (is_array($arShoppingCart) && $this->arParams["ORDER_PRODUCT"] == 2)
		{
			foreach ($arShoppingCart as $key => $product)
			{
				if ($product["PRODUCT_ID"] != $currentProductId)
				{
					unset($arShoppingCart[$key]);
				}
			}
		}*/
		
		$deliveryId = $this->arParams["DELIVERY"];
		if ($this->arParams["DELIVERY_LIST_SHOW"] == "Y" && $this->arResult["POST"]["deliverylist"] > 0)
			$deliveryId = $this->arResult["POST"]["deliverylist"];
		
		$paysystemId = $this->arParams["PAYSYSTEM"];
		if ($this->arParams["PAYSYSTEM_LIST_SHOW"] == "Y" && $this->arResult["POST"]["paysystemlist"] > 0)
			$paysystemId = $this->arResult["POST"]["paysystemlist"];
		
		$this->arBasketItems = CSaleOrder::DoCalculateOrder(
			$siteId,
			$USER->GetID(),
			$arShoppingCart,
			$this->arParams["PERSON_TYPE"],
			$this->arPropValues,
			$deliveryId,
			$paysystemId,
			array(),
			$arErrors,
			$arWarnings
		);

		if (!empty($arErrors))
		{
			foreach ($arErrors as $error)
				if (strlen($error["TEXT"]) > 0)
					$this->arResult["ERROR_MESSAGE"][] = $error["TEXT"];
		}

		foreach(GetModuleEvents("sale", "OnSaleComponentOrderOneStepProcess", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$this->arBasketItems, array(), &$this->arParams));
	}

	protected function addOrder()
	{
		global $USER, $DB;

		$arErrors = array();
		$arCupon = array();
		$siteId = $this->getPostSiteId();

		if (empty($this->arResult["ERROR_MESSAGE"]))
		{
			$statusId = "N";
			if ($this->arParams["ORDER_STATUS"] != "XX")
				$statusId = $this->arParams["ORDER_STATUS"];

			$arAdditionalFields = array(
				"LID" => $this->arResult["POST"]["site_id"],
				"STATUS_ID" => $statusId,
				"PAYED" => "N",
				"CANCELED" => "N",
				"USER_DESCRIPTION" => trim($_POST["USER_DESCRIPTION"]),
			);

			$affiliateID = CSaleAffiliate::GetAffiliate();
			if ($affiliateID > 0)
			{
				$dbAffiliat = CSaleAffiliate::GetList(array(), array("SITE_ID" => $siteId, "ID" => $affiliateID));
				$arAffiliates = $dbAffiliat->Fetch();
				if (count($arAffiliates) > 1)
					$arAdditionalFields["AFFILIATE_ID"] = $affiliateID;
			}
			else
				$arAdditionalFields["AFFILIATE_ID"] = false;

			if (strlen($this->arParams["DELIVERY"]) > 0 && strlen($this->arBasketItems["DELIVERY_ID"]) <= 0)
				$this->arBasketItems["DELIVERY_ID"] = $this->arParams["DELIVERY"];
			
			if ($this->arParams["DELIVERY_LIST_SHOW"] == "Y" && $this->arResult["POST"]["deliverylist"] > 0)
				$this->arBasketItems["DELIVERY_ID"] = $this->arResult["POST"]["deliverylist"];
			
			$option = COption::GetOptionString("main", "~sale_converted_15", "N");
			
			//$ORDER_ID = CSaleOrder::DoSaveOrder($this->arBasketItems, $arAdditionalFields, 0, $arErrors, $arCupon);
			
			if ($option != "Y")
			{
				$ORDER_ID = CSaleOrder::DoSaveOrder($this->arBasketItems, $arAdditionalFields, 0, $arErrors, $arCupon);
			}
			else
			{
				$arOrder = $this->arBasketItems;
				$arFields = array(
					"LID" => $arOrder["SITE_ID"],
					"PERSON_TYPE_ID" => $arOrder["PERSON_TYPE_ID"],
					"PRICE" => $arOrder["PRICE"],
					"CURRENCY" => $arOrder["CURRENCY"],
					"USER_ID" => $arOrder["USER_ID"],
					"PAY_SYSTEM_ID" => $arOrder["PAY_SYSTEM_ID"],
					"PRICE_DELIVERY" => $arOrder["DELIVERY_PRICE"],
					"DELIVERY_ID" => (strlen($arOrder["DELIVERY_ID"]) > 0 ? $arOrder["DELIVERY_ID"] : false),
					"DISCOUNT_VALUE" => $arOrder["DISCOUNT_PRICE"],
					"TAX_VALUE" => $arOrder["TAX_VALUE"],
					"TRACKING_NUMBER" => $arOrder["TRACKING_NUMBER"],
					"PAYED" => "N",
					"CANCELED" => "N",
					"STATUS_ID" => "N"
				);

				if ($arOrder["DELIVERY_PRICE"] == $arOrder["PRICE_DELIVERY"]
					&& isset($arOrder['PRICE_DELIVERY_DIFF']) && floatval($arOrder['PRICE_DELIVERY_DIFF']) > 0)
				{
					$arFields["DELIVERY_PRICE"] = $arOrder['PRICE_DELIVERY_DIFF'] + $arOrder["PRICE_DELIVERY"];
				}

				$arFields = array_merge($arFields, $arAdditionalFields);
				
				if (COption::GetOptionString("sale", "product_reserve_condition", "O") == "O")
					$arFields["RESERVED"] = "Y";

				$ORDER_ID = CSaleOrder::Add($arFields);
				if ($ORDER_ID > 0)
				{
					CSaleTax::DoSaveOrderTax($ORDER_ID, $arOrder["TAX_LIST"], $arErrors);
					CSaleOrderProps::DoSaveOrderProps($ORDER_ID, $arOrder["PERSON_TYPE_ID"], $arOrder["ORDER_PROP"], $arErrors);
					
					//add profile
					if ($this->lastOrderId <= 0)
					{
						$arFields = array(
							"NAME" => GetMessage("SALE_PROFILE_NAME")." ".$arOrder["USER_ID"]." ".Date("Y-m-d"),
							"USER_ID" => $arOrder["USER_ID"],
							"PERSON_TYPE_ID" => $arOrder["PERSON_TYPE_ID"]
						);
						$profileId = CSaleOrderUserProps::Add($arFields);
						if ($profileId && is_array($arOrder["ORDER_PROP"]))
						{
							foreach ($arOrder["ORDER_PROP"] as $id => $val)
							{
								$arFields = array(
										"USER_PROPS_ID" => $profileId,
										"ORDER_PROPS_ID" => $id,
										"NAME" => $this->arResult["PERSON_TYPE_PROPS"][$id]["NAME"],
										"VALUE" => $val
									);
								CSaleOrderUserPropsValue::Add($arFields);
							}
						}
					}
				}
			}
			
			if ($ORDER_ID > 0 && empty($arErrors))
			{
				$this->arResult["IS_ORDER"] = "Y";
				$this->orderId = $ORDER_ID;
				
				CSaleBasket::OrderBasket($ORDER_ID, CSaleBasket::GetBasketUserID(), $siteId, false);
				
				if (!empty($this->arBasketDelayId) && $this->arParams["ORDER_PRODUCT"] != 1)
				{
					$arFields = array("DELAY" => "N");
					foreach ($this->arBasketDelayId as $basketId)
					{
						CSaleBasket::Update($basketId, $arFields);
					}
				}
				
				//send mail order
				$strOrderList = "";
				foreach ($this->arBasketItems["BASKET_ITEMS"] as $val)
				{
					$strOrderList .= $val["NAME"]." - ".$val["QUANTITY"]." ".GetMessage("SOA_SHT").": ".SaleFormatCurrency($val["PRICE"], $this->arBasketItems["CURRENCY"]);
					$strOrderList .= "\n";
				}

				$arOrder = CSaleOrder::GetByID($ORDER_ID);

				$mailTo = $USER->GetEmail();
				if (strlen($this->arBasketItems["USER_EMAIL"]) > 0 && check_email($this->arBasketItems["USER_EMAIL"]))
					$mailTo = $this->arBasketItems["USER_EMAIL"];

				$arEventFields = array(
					"ORDER_ID" => $arOrder["ACCOUNT_NUMBER"],
					"ORDER_DATE" => Date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT", $siteId))),
					"ORDER_USER" => $USER->GetFullName(),
					"PRICE" => SaleFormatCurrency($this->arBasketItems["PRICE"], $this->arBasketItems["CURRENCY"]),
					"BCC" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
					"EMAIL" => $mailTo,
					"ORDER_LIST" => $strOrderList,
					"SALE_EMAIL" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
					"DELIVERY_PRICE" => $this->arBasketItems["PRICE_DELIVERY"],
				);

				if (is_array($this->arPropEmail) && count($this->arPropEmail) > 0)
				{
					foreach ($this->arPropEmail as $key => $val)
					{
						$arEventFields["POPUP_".$key] = $val;
					}
				}

				foreach(GetModuleEvents("sale", "OnOrderNewSendEmail", true) as $arEvent)
					ExecuteModuleEventEx($arEvent, Array($arOrder["ACCOUNT_NUMBER"], &$this->arParams["EVENT_NAME"], &$arEventFields));

				if(!empty($this->arParams["EVENT_MESSAGE_ID"]))
				{
					foreach($this->arParams["EVENT_MESSAGE_ID"] as $v)
						if(intval($v) > 0)
							CEvent::Send($this->arParams["EVENT_NAME"], $siteId, $arEventFields, "N", intval($v));
				}
				else
					CEvent::Send($this->arParams["EVENT_NAME"], $siteId, $arEventFields);
				/*end mail*/

				$_SESSION["SALE_BASKET_NUM_PRODUCTS"][$siteId] = 0;

				foreach(GetModuleEvents("sale", "OnSaleComponentOrderOneStepComplete", true) as $arEvent)
					ExecuteModuleEventEx($arEvent, Array($arOrder["ACCOUNT_NUMBER"], $arOrder, $this->arParams));
			}

			if(CModule::IncludeModule("statistic"))
			{
				$event1 = "eStore";
				$event2 = "order_confirm";
				$event3 = $this->arResult["ORDER_ID"];

				$e = $event1."/".$event2."/".$event3;

				if(!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"])))
				{
					CStatistic::Set_Event($event1, $event2, $event3);
					$_SESSION["ORDER_EVENTS"][] = $e;
				}
			}
		}

		$this->getError($arErrors);
	}
	
	protected function logoutUser()
	{
		global $USER;
		
		if ($this->isAuth && $this->arParams["USE_USER"] == "Y" && $this->arParams["USER_ID"] > 0)
		{
			$USER->Logout();
		}
	}
	
	protected function addOrderStart()
	{
		global $USER, $DB;

		$siteId = $this->getPostSiteId();

		if (!empty($this->arResult["POST"]["mail"]))
		{
			if(!check_email($this->arResult["POST"]["mail"]))
				$this->arResult["ERROR_MESSAGE"][] = GetMessage("SALE_ERROR_EMAIL");
		}

		$mailTo = $USER->GetEmail();
		$userName = $USER->GetFullName();
		if (!empty($this->arResult["POST"]["name"]))
			$userName = $this->arResult["POST"]["name"];

		$strOrderList = GetMessage("SALE_ORDER_FIO").": ".$userName."\n";
		if (!empty($this->arResult["POST"]["phone"]))
			$strOrderList .= GetMessage("SALE_ORDER_PHONE").": ".$this->arResult["POST"]["phone"]."\n";
		if (!empty($this->arResult["POST"]["mail"]))
		{
			$strOrderList .= "E-mail: ".$this->arResult["POST"]["mail"];
			$mailTo = $this->arResult["POST"]["mail"];
		}

		if (empty($this->arResult["ERROR_MESSAGE"]))
		{
			$arEventFields = array(
				"ORDER_DATE" => Date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT", $siteId))),
				"ORDER_USER" => $userName,
				"BCC" => COption::GetOptionString("sale", "order_email", "order@".$_SERVER["HTTP_HOST"]),
				"EMAIL" => $mailTo,
				"ORDER" => $strOrderList,
				"SALE_EMAIL" => COption::GetOptionString("sale", "order_email", "order@".$_SERVER["HTTP_HOST"]),
			);

			if(!empty($this->arParams["EVENT_MESSAGE_ID"]))
			{
				foreach($this->arParams["EVENT_MESSAGE_ID"] as $v)
					if(intval($v) > 0)
						CEvent::Send($this->arParams["EVENT_NAME"], $siteId, $arEventFields, "N", intval($v));
			}
			else
				CEvent::Send($this->arParams["EVENT_NAME"], $siteId, $arEventFields);
		}
	}

	protected function getError($arError)
	{
		global $APPLICATION;

		if (!empty($arError))
		{
			foreach ($arError as $error)
				if (strlen($error["TEXT"]) > 0)
					$this->arResult["ERROR_MESSAGE"][] = $error["TEXT"];
		}

		if($this->arParams["USE_CAPTCHA"] == "Y")
			$this->arResult["capCode"] =  htmlspecialcharsbx($APPLICATION->CaptchaGetCode());
	}

	protected function getElement()
	{
		global $USER;

		if ($this->arParams["ORDER_PRODUCT"] == 2)
		{
			if (!is_array($this->arParams["PRODUCT_ID"]) && $this->arParams["PRODUCT_ID"] > 0)
			{
				$arElement = array();
				$arSelect = array(
					"ID",
					"IBLOCK_ID",
					"CODE",
					"NAME",
					"ACTIVE",
					"PREVIEW_TEXT",
					"PREVIEW_TEXT_TYPE",
					"DETAIL_TEXT",
					"DETAIL_TEXT_TYPE",
					"DATE_CREATE",
					"CREATED_BY",
					"IBLOCK_SECTION_ID",
					"DETAIL_PAGE_URL",
					"DETAIL_PICTURE",
					"PREVIEW_PICTURE",
					"PROPERTY_*",
				);
				$arFilter = array(
					"ID" => $this->arParams["PRODUCT_ID"],
					"IBLOCK_LID" => SITE_ID,
					"ACTIVE" => "Y",
					"CHECK_PERMISSIONS" => "Y",
					"MIN_PERMISSION" => 'R',
				);

				$cacheTime = 604800;
				$cacheId = md5(serialize($arFilter));
				$cachePath = "/oneclick_element";
				$obCache = new CPHPCache;
				if ($obCache->InitCache($cacheTime, $cacheId, $cachePath))
				{
					$vars = $obCache->GetVars();
					$arElement = $vars["ELEMENT"];
				}
				elseif ($obCache->StartDataCache())
				{
					global $CACHE_MANAGER;
					$CACHE_MANAGER->StartTagCache($cachePath);
					$CACHE_MANAGER->RegisterTag('iblock_' . $this->arParams["IBLOCK_ID"]);

					$arElement = array();
					$rsElement = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
					$rsElement->SetUrlTemplates($this->arParams["DETAIL_URL"]);
					if($obElement = $rsElement->GetNextElement())
					{
						$arElement = $obElement->GetFields();

						$arPicture = array();
						if ($arElement["PREVIEW_PICTURE"] > 0)
						{
							$arPicture = CFile::GetFileArray($arElement["PREVIEW_PICTURE"]);
							$arElement["PREVIEW_PICTURE"] = $arPicture;
						}

						if ($arElement["DETAIL_PICTURE"] > 0)
						{
							$arPicture = CFile::GetFileArray($arElement["DETAIL_PICTURE"]);
							$arElement["DETAIL_PICTURE"] = $arPicture;
						}

						if (!empty($arPicture))
						{
							$arElement["RESIZE_PICTURE"] = CFile::ResizeImageGet($arPicture, array('width'=>$this->arParams["IMAGE_WIDTH"], 'height'=>$this->arParams["IMAGE_HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, true);
						}

						$arElement["PROPERTIES"] = $obElement->GetProperties();

						if ($this->isShop)
						{
							$priceType = $this->arParams["PRICE_CODE"];
							$arResultPrices = CIBlockPriceTools::GetCatalogPrices($arElement["IBLOCK_ID"], array($priceType));

							$arElement["PRICES"] = CCatalogProduct::GetOptimalPrice($arElement["ID"], 1, $USER->GetUserGroupArray(), "N");
							$arElement["PRICES"]["PRICE"]["PRINT_PRICE"] = SaleFormatCurrency($arElement["PRICES"]["DISCOUNT_PRICE"], $arElement["PRICES"]["PRICE"]["CURRENCY"]);

							$arOffers = CIBlockPriceTools::GetOffersArray(
								$arElement["IBLOCK_ID"],
								$arElement["ID"],
								array(),
								array(),
								$this->arParams["OFFERS_PROPERTY_CODE"],
								0,
								$arResultPrices,
								"N",
								array()
							);

							$arId = array();
							$minPrice = 0;
							$arData = array();
							if (!empty($arOffers))
							{
								foreach ($arOffers as $arItem)
								{
									if ($arItem["CAN_BUY"])
									{
										if ($arItem["PRICES"][$priceType]["DISCOUNT_VALUE"] < $arItem["PRICES"][$priceType]["VALUE"])
										{
											$arItem["PRICE"] = $arItem["PRICES"][$priceType]["PRINT_DISCOUNT_VALUE"];
											$priceTmp = $arItem["PRICES"][$priceType]["DISCOUNT_VALUE"];
										}
										else
										{
											$arItem["PRICE"] = $arItem["PRICES"][$priceType]["PRINT_VALUE"];
											$priceTmp = $arItem["PRICES"][$priceType]["VALUE"];
										}

										if ($minPrice == 0 || $priceTmp < $minPrice)
											$minPrice = $priceTmp;

										$arData[$arItem["ID"]] = $arItem;
									}
									$arId[] = $arItem["ID"];
								}

								$res = CIBlockElement::GetList(
									array(),
									array("ID" => $arId),
									false,
									false,
									array("ID", "NAME")
								);
								while ($arRes = $res->GetNext())
								{
									$arData[$arRes["ID"]]["NAME"] = $arRes["NAME"];
									$arData[$arRes["ID"]]["DETAIL_PAGE_URL"] = $arRes["DETAIL_PAGE_URL"];
								}

								$arElement["PRICES_MIN_OFFERS"] = array(
									"VALUE" => $minPrice,
									"PRINT_VALUE" => SaleFormatCurrency($minPrice, $arItem["PRICES"][$priceType]["CURRENCY"]),
									"PRINT_DISCOUNT_VALUE" => SaleFormatCurrency($minPrice, $arItem["PRICES"][$priceType]["CURRENCY"])
								);
							}

							$arElement["OFFERS"] = $arData;
						}
					}
					else
					{
						$this->arResult["ERROR_MESSAGE"][] = GetMessage("SALE_ERROR_ELEMENT_ID");
					}

					$CACHE_MANAGER->EndTagCache();

					$obCache->EndDataCache(array(
						"ELEMENT" => $arElement,
					));
				}
			}
			else
			{
				$arElement = $this->arParams["PRODUCT_ID"];
				if ($this->isShop)
				{
					if (isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]))
					{
						$arElement["PRICES_MIN_OFFERS"] = $arElement["MIN_PRICE"];
						foreach ($arElement["OFFERS"] as &$arOffer)
						{
							$arOffer["PRICE"] = $arOffer["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"];
						}
					}
					else
					{
						$arElement["MIN_PRICE"]["PRINT_PRICE"] = $arElement["MIN_PRICE"]["PRINT_VALUE"];
						$arElement["PRICES"]["PRICE"] = $arElement["MIN_PRICE"];
					}
				}

				$picture = 0;
				if ($arElement["~DETAIL_PICTURE"] > 0)
				{
					$picture = $arElement["~DETAIL_PICTURE"];
				}
				if ($arElement["~PREVIEW_PICTURE"] > 0 && $picture <= 0)
				{
					$picture = $arElement["~PREVIEW_PICTURE"];
				}

				if ($picture > 0)
				{
					$arElement["RESIZE_PICTURE"] = CFile::ResizeImageGet($picture, array('width'=>$this->arParams["IMAGE_WIDTH"], 'height'=>$this->arParams["IMAGE_HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, true);
				}

				/*unset($arElement["PROPERTIES"]);
				unset($arElement["DISPLAY_PROPERTIES"]);
				unset($arElement["SECTION"]);
				unset($arElement["SKU_PROPS"]);*/
			}

			if (!isset($arElement["CATALOG_MEASURE_NAME"]) || empty($arElement["CATALOG_MEASURE_NAME"]))
				$arElement["CATALOG_MEASURE_NAME"] = GetMessage("SALE_MEASURE");
			
			$this->arResult["ELEMENTS"] = $arElement;
		}
	}

	protected function getHideBasketButton()
	{
		if ($this->arParams["HIDE_BUTTON"] == "Y"
			&& $this->arParams["ORDER_PRODUCT"] == 1 && $this->isShop)
		{
			if (!is_set($bHideButton))
			{
				$bHideButton = "N";
				$dbBasket = CSaleBasket::GetList(
					array("ID" => "ASC"),
					array(
						"FUSER_ID" => CSaleBasket::GetBasketUserID(),
						"LID" => SITE_ID,
						"ORDER_ID" => "NULL",
						"DELAY" => "N",
						"SUBSCRIBE" => "N"
					),
					false,
					false,
					array("ID")
				);
				if (!$arBasket = $dbBasket->Fetch())
					$bHideButton = "Y";
			}

			$this->arResult["HIDE_BUTTON"] = $bHideButton;
		}
	}

	protected function formatAjaxAnswer()
	{
		global $APPLICATION;

		$this->arResult["AJAX_STATUS"] = "";
		$this->arResult["AJAX_DATA"] = "";
		$this->arResult["AJAX_ORDER_ID"] = 0;

		if (!empty($this->arResult["ERROR_MESSAGE"]))
		{
			$errorText = "<div class='order-error'>";
			foreach($this->arResult["ERROR_MESSAGE"] as $val)
			{
				$errorText .= "<div>".$val."</div>";
			}
			$errorText .= "</div>";

			$this->arResult["AJAX_STATUS"] = "ERROR";
			$this->arResult["AJAX_DATA"] = $errorText;
		}
		else
		{
			$this->arResult["AJAX_STATUS"] = "OK";
			$this->arResult["AJAX_DATA"] = GetMessage("SALE_CONFIRM_ORDER_OK");
			$this->arResult["AJAX_ORDER_ID"] = $this->orderId;
		}
	}
	
	protected function getListDelivery()
	{
		$arDelivery = array();
		if ($this->isShop && $this->arParams["DELIVERY_LIST_SHOW"] == "Y")
		{
			$dbDelivery = CSaleDelivery::GetList(
				array("SORT"=>"ASC", "NAME"=>"ASC"),
				array("ACTIVE" => "Y", "LID" => SITE_ID)
			);
			while ($arDeliv = $dbDelivery->GetNext())
			{
				$arDeliv["PRICE_PRINT"] = CurrencyFormat($arDeliv["PRICE"], $arDeliv["CURRENCY"]);
				$arDelivery[] = $arDeliv;
			}
		}
		
		$this->arResult["DELIVERY_LIST"] = $arDelivery;
		
	}
	
	protected function getListPaysystem()
	{
		$arPaysystem = array();
		if ($this->isShop && $this->arParams["PAYSYSTEM_LIST_SHOW"] == "Y")
		{
			$dbPaySystem = CSalePaySystem::GetList(
				array("SORT" => "ASC", "PSA_NAME" => "ASC"),
				array(
					"ACTIVE" => "Y",
					"PSA_HAVE_PAYMENT" => "Y"
				)
			);
			while ($arPay = $dbPaySystem->GetNext())
				$arPaySystem[] = $arPay;
		}
		
		$this->arResult["PAYSYSTEM_LIST"] = $arPaySystem;
	}
	
	/**
	 * Check Required Modules
	 * @throws Exception
	 */
	protected function checkModules()
	{
		if(!Main\Loader::includeModule("iblock"))
			throw new SystemException(Loc::getMessage("SOA_MODULE_IBLOCK_NOT_INSTALL"));

		if (!Main\Loader::includeModule("sale") || !Main\Loader::includeModule("catalog"))
			$this->isShop = false;
	}

	/**
	 * Start Component
	 */
	public function executeComponent()
	{
		global $APPLICATION;
		try
		{
			$this->arResult = array();

			$this->checkModules();
			$this->getPersonType();
			$this->getDelivery();
			$this->getPost();
			$this->getCaptha();
			$this->getProfileData();
			$this->getOrderProps();
			$this->getListDelivery();
			$this->getListPaysystem();
			
			if($this->isAjax())
			{
				$this->saveOrder();
				$this->formatAjaxAnswer();

				$this->IncludeComponentTemplate('ajax');
			}
			else
			{
				$this->getElement();
				$this->arResult["AJAX_URL"] = $this->ajaxUrl;
				$this->getHideBasketButton();

				$arParams = array();
				foreach ($this->arParams as $key => $val)
				{
					if ($key[0] != "~")
						$arParams[$key] = $val;
				}
				if (is_array($arParams["PRODUCT_ID"]) && $arParams["PRODUCT_ID"]["ID"] > 0)
					$arParams["PRODUCT_ID"] = $arParams["PRODUCT_ID"]["ID"];
				$this->arResult["PARAMS"] = serialize($arParams);
				unset($arParams);
				
				mt_srand((double)microtime()*1000000);
				$this->arResult["UNIQUE_CODE"] = $this->arResult["ELEMENTS"]["ID"].mt_rand(1,1000000);
				
				$this->IncludeComponentTemplate();
			}
		}
		catch (SystemException $e)
		{
			if ($this->isAjax)
			{
				$APPLICATION->restartBuffer();
				$this->arResult["ERROR_MESSAGE"] = $e->getMessage();
				$this->formatAjaxAnswer();
				$this->IncludeComponentTemplate('ajax');
				die();
			}

			ShowError($e->getMessage());
		}
	}
}

?>