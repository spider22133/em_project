<?
use Bitrix\Main;

IncludeModuleLangFile(__FILE__);
Class tarakud_wishlist extends CModule
{
	const MODULE_ID = 'tarakud.wishlist';
	var $MODULE_ID = 'tarakud.wishlist'; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("TARAKUD_WISHLIST_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("TARAKUD_WISHLIST_DESC");

		$this->PARTNER_NAME = GetMessage("TARAKUD_WISHLIST_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("TARAKUD_WISHLIST_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		$eventManager = Main\EventManager::getInstance();
		$eventManager->registerEventHandler("main", "OnBeforeProlog", self::MODULE_ID, "Tarakud\Wishlist\Wishlist", "autoLoad");
		$eventManager->registerEventHandler("main", "OnAfterUserLogin", self::MODULE_ID, "Tarakud\Wishlist\Wishlist", "authorizeLoad");
		
		CUrlRewriter::Add(
			array(
				"SITE_ID" => "s1",
				"CONDITION" => "#^/wishlist/wl([0-9]+)/(.*)#",
				"ID" => "",
				"PATH" => "/wishlist/index.php",
				"RULE" => "USER=\$1"
			)
		);
		
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		$eventManager = Main\EventManager::getInstance();
		$eventManager->unregisterEventHandler('main', 'OnBeforeProlog', self::MODULE_ID, 'Tarakud\Wishlist\Wishlist', 'autoLoad');
		$eventManager->unregisterEventHandler('main', 'OnAfterUserLogin', self::MODULE_ID, 'Tarakud\Wishlist\Wishlist', 'authorizeLoad');
		
		return true;
	}

	function InstallFiles($arParams = array())
	{
		copyDirFiles(
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/'.self::MODULE_ID.'/install/js',
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/js',
			true, true
		);
		
		copyDirFiles(
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/'.self::MODULE_ID.'/install/components',
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/components',
			true, true
		);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx('/bitrix/components/tarakud/wish.list/');
		deleteDirFiles(
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/'.self::MODULE_ID.'/install/js',
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/js'
		);
		
		return true;
	}
	
	function InstallTables()
	{	
		include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/tables.php");
		
		return true;
	}

	function UnInstallTables()
	{
		return true;
	}
	
	function DoInstall()
	{
		global $APPLICATION;
		
		$this->InstallFiles();
		$this->InstallTables();
		$this->InstallDB();
		RegisterModule(self::MODULE_ID);
	}

	function DoUninstall()
	{
		global $APPLICATION;
		UnRegisterModule(self::MODULE_ID);
		
		$this->UnInstallDB();
		$this->UnInstallFiles();
	}
}
?>