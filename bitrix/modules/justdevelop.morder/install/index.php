<?
IncludeModuleLangFile(__FILE__);

class justdevelop_morder extends CModule
{
	const MODULE_ID = "justdevelop.morder";
	var $MODULE_ID = "justdevelop.morder";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';
	var $MODULE_GROUP_RIGHTS = "Y";
	
	function __construct(){
		
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("JUSTDEVELOP_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("JUSTDEVELOP_MODULE_DESC");
		$this->PARTNER_NAME = GetMessage("JUSTDEVELOP_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("JUSTDEVELOP_PARTNER_URI");
	}
	
	function InstallDB($arParams = array()){
		
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		if($this->errors !== false){
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}
		else{
			RegisterModule(self::MODULE_ID);
			CModule::IncludeModule(self::MODULE_ID);
			
			RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", self::MODULE_ID, "CJUSTDEVELOP", "OnSaleComponentOrderOneStepCompleteHandler");
			RegisterModuleDependences("sale", "OnSaleComponentOrderComplete", self::MODULE_ID, "CJUSTDEVELOP", "OnSaleComponentOrderCompleteHandler");
			RegisterModuleDependences("sale", "OnSalePayOrder", self::MODULE_ID, "CJUSTDEVELOP", "OnSalePayOrderHandler");
			RegisterModuleDependences("sale", "OnSaleDeliveryOrder", self::MODULE_ID, "CJUSTDEVELOP", "OnSaleDeliveryOrderHandler");
			RegisterModuleDependences("sale", "OnSaleCancelOrder", self::MODULE_ID, "CJUSTDEVELOP", "OnSaleCancelOrderHandler");
			RegisterModuleDependences("sale", "OnSaleStatusOrder", self::MODULE_ID, "CJUSTDEVELOP", "OnSaleStatusOrderHandler");
			
			return true;
		}
	}
	                                                
	function UnInstallDB($arParams = array()){
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

				
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", self::MODULE_ID, "CJUSTDEVELOP", "OnSaleComponentOrderOneStepCompleteHandler");
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderComplete", self::MODULE_ID, "CJUSTDEVELOP", "OnSaleComponentOrderCompleteHandler");
		UnRegisterModuleDependences("sale", "OnSalePayOrder", self::MODULE_ID, "CJUSTDEVELOP", "OnSalePayOrderHandler");
		UnRegisterModuleDependences("sale", "OnSaleDeliveryOrder", self::MODULE_ID, "CJUSTDEVELOP", "OnSaleDeliveryOrderHandler");
		UnRegisterModuleDependences("sale", "OnSaleCancelOrder", self::MODULE_ID, "CJUSTDEVELOP", "OnSaleCancelOrderHandler");
		UnRegisterModuleDependences("sale", "OnSaleStatusOrder", self::MODULE_ID, "CJUSTDEVELOP", "OnSaleStatusOrderHandler");
	
		UnRegisterModule(self::MODULE_ID);

		if($this->errors !== false){
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}
		return true;
	}
	
	function InstallEvents(){
		
		return true;
	}
	
	function UnInstallEvents(){
		
		return true;
	}
	
	function InstallFiles($arParams = array()){
		
		if($_ENV["COMPUTERNAME"] != "BX"){
			if(is_dir($p = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/admin")){
				if($dir = opendir($p)){
					while(false !== $item = readdir($dir)){
						if($item == '..' || $item == '.' || $item == 'menu.php'){
							continue;
						}
						file_put_contents($file = $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/".self::MODULE_ID."_".$item, '<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.self::MODULE_ID.'/admin/'.$item.'");?'.'>');
					}
					closedir($dir);
				}
			}
			
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/images/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/".self::MODULE_ID, false, true);
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/themes/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", false, true);
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/js/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".self::MODULE_ID."/js/", true, true);
		}
		return true;
	}

	function UnInstallFiles(){
		
		if($_ENV["COMPUTERNAME"] != "BX"){
			if(is_dir($p = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/admin")){
				if($dir = opendir($p)){
					while(false !== $item = readdir($dir)){
						if($item == '..' || $item == '.' || $item == 'menu.php'){
							continue;
						}
						unlink($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/".self::MODULE_ID.'_'.$item);
					}
					closedir($dir);
				}
			}

			
			//css
			DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");
			//icons
			DeleteDirFilesEx("/bitrix/themes/.default/icons/".self::MODULE_ID."/");
			//images
			DeleteDirFilesEx("/bitrix/images/".self::MODULE_ID."/");
			
		}
		return true;
	}

	function DoInstall(){
		
		global $DB, $DOCUMENT_ROOT, $APPLICATION, $step;
		$POST_RIGHT = $APPLICATION->GetGroupRight(self::MODULE_ID);
		if($POST_RIGHT == "W"){
			
			$step = IntVal($step);
			if($step < 2){
				$APPLICATION->IncludeAdminFile(GetMessage("JUSTDEVELOP_inst_title"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/inst1.php");
			}
			elseif($step == 2){
				if($this->InstallDB()){
					$this->InstallEvents();
					$this->InstallFiles();
				}
				$GLOBALS["errors"] = $this->errors;
				$APPLICATION->IncludeAdminFile(GetMessage("JUSTDEVELOP_inst_title"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/inst2.php");
			}
		}
	}

	function DoUninstall(){
		
		global $DB, $DOCUMENT_ROOT, $APPLICATION, $step;
		$POST_RIGHT = $APPLICATION->GetGroupRight(self::MODULE_ID);
		if($POST_RIGHT == "W"){
			$step = IntVal($step);
			if($step < 2){
				$APPLICATION->IncludeAdminFile(GetMessage("JUSTDEVELOP_uninst_title"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/uninst1.php");
			}
			elseif($step == 2){
				$this->UnInstallDB();
				$this->UnInstallEvents();
				$this->UnInstallFiles();
				$GLOBALS["errors"] = $this->errors;
				$APPLICATION->IncludeAdminFile(GetMessage("JUSTDEVELOP_uninst_title"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/uninst2.php");
			}
		}
	}
	
}
?>