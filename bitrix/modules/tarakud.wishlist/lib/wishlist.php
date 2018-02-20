<?php
/**
 * ������ �� ������� �������
 *
 * @brief ������ �� ������� �������
 * @author tarakud
 * @mail tarakud@gmail.com
 * @link www.website-creator.ru
 */
namespace Tarakud\Wishlist;

use Bitrix\Main;
use Bitrix\Main\Type;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Highloadblock as HL;

Loc::loadMessages(__FILE__);

class Wishlist
{
	const MODULE_ID = 'tarakud.wishlist';
	const MODULE_PATH = 'tarakud_wishlist';
	
	/** 
	* ��������� ������� ��� �������� ��������
	* 
	* @return void
	*/ 
	static public function addScript()
	{
		global $APPLICATION;
		
		\CJSCore::Init(array("jquery"));
		
		$url = $APPLICATION->GetCurPageParam();
		$addText = \COption::GetOptionString(self::MODULE_ID, "wishlist_add", "", SITE_ID);
		$addTitle = \COption::GetOptionString(self::MODULE_ID, "wishlist_add_title", "", SITE_ID);
		$delText = \COption::GetOptionString(self::MODULE_ID, "wishlist_del", "", SITE_ID);
		$delTitle = \COption::GetOptionString(self::MODULE_ID, "wishlist_del_title", "", SITE_ID);
		$page = \COption::GetOptionString(self::MODULE_ID, "wishlist_page", "", SITE_ID);
		
		$APPLICATION->AddHeadString('<script type="text/javascript">
		var wishlistUrl = "'.$url.'";
		BX.message({
//			T_DEL_TEXT: "'.$addText.'", // ES change
			T_DEL_TITLE: "'.$addTitle.'",
			T_ADD_TEXT: "'.$delText.'",
			T_ADD_TITLE: "'.$delTitle.'",
			T_PAGE: "'.$page.'",
		});
		</script>');
		
		if (is_array($_SESSION["TARAKUD_WISHLIST"]) && !empty($_SESSION["TARAKUD_WISHLIST"]))
		{
			$js = '<script type="text/javascript">$(function() {';
			$js .= 'var arWishId = '.\CUtil::PhpToJSObject($_SESSION["TARAKUD_WISHLIST"]).';';
			foreach ($_SESSION["TARAKUD_WISHLIST"] as $id)
			{
				$js .= 'var el = $("[data-wishid = '.$id.']");';
//				$js .= 'el.addClass("active");'; // ES change
                $js .= 'if(!el.hasClass("active")){el.addClass("active");}';
//				$js .= 'el.html("'.$addText.'");';
				$js .= 'el.html("");'; // ES change
				$js .= 'el.off("click");';
				$js .= 'el.attr("href", BX.message("T_PAGE"));';
			}
			$js .= '});</script>';

			$APPLICATION->AddHeadString($js);
		}
		
		$APPLICATION->AddHeadScript('/bitrix/js/'.self::MODULE_PATH.'/ajax.js');
	}
	
	/** 
	* ��������� � ������ ������ �� �������
	* 
	* @return void
	*/ 
	static public function loadCookies()
	{
		$_SESSION["TARAKUD_WISHLIST"] = array();
		if (isset($_COOKIE["TARAKUD_WISHLIST"]) && !empty($_COOKIE["TARAKUD_WISHLIST"]))
		{
			$ids = $_COOKIE["TARAKUD_WISHLIST"];
			$arId = unserialize($ids);
			if (is_array($arId) && !empty($arId))
			{
				foreach ($arId as $id)
					$_SESSION["TARAKUD_WISHLIST"][$id] = $id;
			}
		}
		else
		{
			self::loadElement();
		}
	}
	
	/** 
	* ��������� �������� �� hightload. 
	* 
	* @return void
	*/ 
	public function loadElement()
	{
		global $USER;
		
		if (!$USER->IsAuthorized())
			return;
			
		\Bitrix\Main\Loader::includeModule("highloadblock");
		
		$saveIblockId = \COption::GetOptionInt(self::MODULE_ID, "wishlist_iblock", "", SITE_ID);
		$hlblock = HL\HighloadBlockTable::getById($saveIblockId)->fetch();
		if (!empty($hlblock))
		{
			$entity = HL\HighloadBlockTable::compileEntity($hlblock);
			$query = new Entity\Query($entity);
			$arId = array();
			$arFilter = array("UF_USER_ID" => $USER->GetID());
			$query->setSelect(array("ID", "UF_ELEMENT_ID"));
			$query->setFilter($arFilter);
			$res = $query->exec();
			$result = new \CDBResult($res);
			while ($arRow = $result->Fetch())
			{
				$arRow["UF_ELEMENT_ID"] = intval($arRow["UF_ELEMENT_ID"]);
				if ($arRow["UF_ELEMENT_ID"] > 0)
					$arId[$arRow["UF_ELEMENT_ID"]] = $arRow["UF_ELEMENT_ID"];
			}
				
			if (!empty($arId))
			{
				foreach ($arId as $id)
					$_SESSION["TARAKUD_WISHLIST"][$id] = $id;
			}
			
			self::setCookies();
		}
	}
	
	/** 
	* ��������� ������ ������� � ������
	* 
	* @return void
	*/ 
	static public function setCookies()
	{
		$ids = serialize($_SESSION["TARAKUD_WISHLIST"]);
		setcookie("TARAKUD_WISHLIST", $ids, time()+60*60*24*30*12*1, "/", $_SERVER["SERVER_NAME"]);
	}
	
	/** 
	* ����������� ���� �������
	* 
	* @return boolean
	*/ 
	static public function isAjax()
	{
		if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax']) && $_POST['ajax'] == 'wishlist' && check_bitrix_sessid())
		{
			return true;
		}
		
		return false;
	}
	
	/** 
	* ����������� ������� �������� � ������ �������
	* 
	* @return boolean
	*/ 
	static public function isElementId($elemetId)
	{
		$elemetId = intval($elemetId);
		
		if (isset($_SESSION["TARAKUD_WISHLIST"][$elemetId]))
			return true;
		
		return false;
	}
	
	/** 
	* ���������� �������� � ������ �������
	* 
	* @param integer $iblockId: ��� ���������
	* @param integer $elementId: ��� ��������
	* @return boolean
	*/ 
	static public function add($iblockId, $elementId)
	{
		global $USER;
		
		$saveIblockId = \COption::GetOptionInt(self::MODULE_ID, "wishlist_iblock", "", SITE_ID);
		$iblockId = intval($iblockId);
		$elementId = intval($elementId);

		if ($saveIblockId > 0 && $elementId > 0)
		{
			$arFields = array(
				"UF_IBLOCK_ID" => $iblockId,
				"UF_ELEMENT_ID" => $elementId,
				"UF_USER_ID" => $USER->GetID(),
				"UF_DATE_INSERT" => new \Bitrix\Main\Type\DateTime()
			);
			$hlblock = HL\HighloadBlockTable::getById($saveIblockId)->fetch();
			if (!empty($hlblock))
			{
				$entity = HL\HighloadBlockTable::compileEntity($hlblock);
				$entityData = $entity->getDataClass();
				$result = $entityData::add($arFields);
				$ID = $result->getId();
				
				if ($result->isSuccess())
				{
					$_SESSION["TARAKUD_WISHLIST"][$elementId] = $elementId;
					self::setCookies();
					return $ID;
				}
			}
		}
		
		return false;
	}
	
	/** 
	* �������� �������� �� ������ �������
	* 
	* @param integer $elementId: ��� ��������
	* @return boolean
	*/ 
	static public function delete($elementId)
	{
		global $USER;
		
		if (!$USER->IsAuthorized())
			return;
		
		\Bitrix\Main\Loader::includeModule("highloadblock");
		$saveIblockId = \COption::GetOptionInt(self::MODULE_ID, "wishlist_iblock", "", SITE_ID);
		$iblockId = intval($iblockId);
		$elementId = intval($elementId);
		
		if ($saveIblockId > 0 && $elementId > 0)
		{
			$hlblock = HL\HighloadBlockTable::getById($saveIblockId)->fetch();
			if (!empty($hlblock))
			{
				$entity = HL\HighloadBlockTable::compileEntity($hlblock);
				$entityDataClass = $entity->getDataClass();
				$rsData = $entityDataClass::getList(array(
				   "select" => array("ID"),
				   "order" => array("ID" => "ASC"),
				   "filter" => array("UF_USER_ID" => $USER->GetID(), "UF_ELEMENT_ID" => $elementId)
				));
				if ($arRow = $rsData->Fetch())
				{
					$entityData = $entity->getDataClass();
					$res = $entityData::delete($arRow["ID"]);
					
					if ($res->isSuccess())
					{
						unset($_SESSION["TARAKUD_WISHLIST"][$elementId]);
						self::setCookies();
						return true;
					}
				}
			}
		}
		
		return false;
	}
	
	/** 
	* �������� ���������� ������ ���� �������
	* 
	* @param array $arResult: �������������� ������
	* @return void
	*/ 
	static public function sendAjax($arResult)
	{
		//global $APPLICATION;
		//$APPLICATION->RestartBuffer();
		ob_end_clean();
		echo json_encode($arResult);
		die();
	}
	
	/** 
	* ��������� ������ ������� ��� �����������. 
	* 
	* @return void
	*/ 
	public function authorizeLoad()
	{
		global $USER;
		
		\Bitrix\Main\Loader::includeModule("highloadblock");
		
		$saveIblockId = \COption::GetOptionInt(self::MODULE_ID, "wishlist_iblock", "", SITE_ID);
		$hlblock = HL\HighloadBlockTable::getById($saveIblockId)->fetch();
		if (!empty($hlblock))
		{
			self::loadCookies();
			$entity = HL\HighloadBlockTable::compileEntity($hlblock);
			$query = new Entity\Query($entity);
			$arId = array();
			$arFilter = array("UF_USER_ID" => $USER->GetID());
			$query->setSelect(array("ID", "UF_ELEMENT_ID"));
			$query->setFilter($arFilter);
			$res = $query->exec();
			$result = new \CDBResult($res);
			while ($arRow = $result->Fetch())
			{
				$arRow["UF_ELEMENT_ID"] = intval($arRow["UF_ELEMENT_ID"]);
				if ($arRow["UF_ELEMENT_ID"] > 0)
					$arId[$arRow["UF_ELEMENT_ID"]] = $arRow["UF_ELEMENT_ID"];
			}
			
			foreach ($_SESSION["TARAKUD_WISHLIST"] as $id)
			{
				if (!in_array($id, $arId))
					self::add(0, $id);
			}
			
			foreach ($arId as $key => $id)
			{
				if (!in_array($id, $_SESSION["TARAKUD_WISHLIST"]))
					self::delete($id);
			}
			
			self::setCookies();
		}
	}
	
	
	/** 
	* �������� ����������� �����.
	* 
	* ��������� ������� � ������ �������, ��������� �������
	* 
	* @return void
	*/ 
	public function autoLoad()
	{
		global $APPLICATION, $USER;
		
		\Bitrix\Main\Loader::includeModule("highloadblock");
		
		if ( !isset($_SESSION["TARAKUD_WISHLIST"]) )
			self::loadCookies();

		self::addScript();

		if (self::isAjax())
		{
			$arResult = array("status" => "no");
		
			$iblockId = intval($_POST["iblock"]);
			$elementId = intval($_POST["id"]);
			$bElement = self::isElementId($elementId);
			
			if ($elementId > 0)
			{
				if ($USER->IsAuthorized())
				{
					if (!$bElement)
					{
						$res = self::add($iblockId, $elementId);
						$arResult["status"] = "add";
					}
					else
					{
						$res = self::delete($elementId);
						$arResult["status"] = "del";
					}
					
					if ($res)
						self::setCookies();
					else
						$arResult["status"] = "no";
				}
				else
				{
					if (!$bElement)
					{
						$_SESSION["TARAKUD_WISHLIST"][$elementId] = $elementId;
						$arResult["status"] = "add";
					}
					else
					{
						unset($_SESSION["TARAKUD_WISHLIST"][$elementId]);
						$arResult["status"] = "del";
					}
					
					self::setCookies();
				}
			}
			
			self::sendAjax($arResult);
		}
	}
	
}