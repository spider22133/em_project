<?
IncludeModuleLangFile(__FILE__);

class vettich_autoposting extends CModule{
	var $MODULE_ID = "vettich.autoposting";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "Y";
	var $MODULE_ROOT_DIR = '';

	function vettich_autoposting(){
		$arModuleVersion = array();

		include(__DIR__."/version.php");

		if(is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)){
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}

		$this->MODULE_ROOT_DIR = dirname(__DIR__);
		$this->MODULE_NAME = GetMessage("VPOSTING_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("VPOSTING_MODULE_DESCRIPTION");
		$this->PARTNER_NAME = GetMessage("VPOSTING_PARTNER_NAME"); 
		$this->PARTNER_URI = GetMessage("VPOSTING_PARTNER_URI");
	}

	function DoInstall(){
		global $DOCUMENT_ROOT, $APPLICATION, $errors, $ver, $GLOBALS;
		$GLOBALS["CACHE_MANAGER"]->CleanAll();

		if($this->InstallDB())
		{
			if($this->InstallFiles() && $this->InstallEvents())
			{
				RegisterModule($this->MODULE_ID);
				return true;
			}
			else
				$APPLICATION->IncludeAdminFile(GetMessage("VPOSTING_INSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/".$this->MODULE_ID."/install/install_error_files.php");
		}
		else
			$APPLICATION->IncludeAdminFile(GetMessage("VPOSTING_INSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/".$this->MODULE_ID."/install/install_error_db.php");
	}

	function DoUninstall(){
		global $DOCUMENT_ROOT, $APPLICATION, $step;
		$step = IntVal($step);
		if($step<2)
		{
			$APPLICATION->IncludeAdminFile(GetMessage("VPOSTING_UNINSTALL_TITLE"), $this->MODULE_ROOT_DIR."/install/unstep1.php");
		}
		elseif($step==2)
		{
			if($this->UnInstallDB(array(
					"savedata" => $_REQUEST["savedata"],
				))
				&& $this->UnInstallFiles()
				&& $this->UnInstallEvents())
			{
				UnRegisterModule($this->MODULE_ID);
				return true;
			}
			return false;
		}
	}

	function InstallDB($arModuleParams = array())
	{
		$lib = $this->MODULE_ROOT_DIR.'/lib';
		include $lib.'/dbase.php';
		include $lib.'/db.php';
		if(!Vettich\Autoposting\DBTable::createTable())
			return false;

		include $lib.'/dblogs.php';
		if(!Vettich\Autoposting\DBLogsTable::createTable())
			return false;

		$plib = $lib.'/posts';
		foreach($this->getPosts() as $post)
		{
			$fdb = $plib.'/'.$post.'/db.php';
			$fdbopt = $plib.'/'.$post.'/dboption.php';
			if(file_exists($fdb) && file_exists($fdbopt))
			{
				include $fdb; include $fdbopt;
				$db = 'Vettich\Autoposting\Posts\\'.$post.'\DBTable';
				$dbopt = 'Vettich\Autoposting\Posts\\'.$post.'\DBOptionTable';
				if(!$db::createTable() or !$dbopt::createTable())
					return false;
			}
		}
		$def_options = array(
			// posts
			'is_enable' => 'Y',
			'is_ajax_enable' => 'Y',
			'is_enable_logs' => 'Y',
			// facebook
			'is_fb_enable' => 'Y',
			'fb_log_success' => 'N',
			'fb_log_error' => 'Y',
			// instagram
			'is_instagram_enable' => 'Y',
			'instagram_log_success' => 'N',
			'instagram_log_error' => 'Y',
			// odnoklassniki
			'is_odnoklassniki_enable' => 'Y',
			'odnoklassniki_log_success' => 'N',
			'odnoklassniki_log_error' => 'Y',
			// twitter
			'is_twitter_enable' => 'Y',
			'twitter_log_success' => 'N',
			'twitter_log_error' => 'Y',
			// vk
			'is_vk_enable' => 'Y',
			'vk_log_success' => 'N',
			'vk_log_error' => 'Y',
		);
		foreach($def_options as $k => $v)
		{
			COption::SetOptionString($this->MODULE_ID, $k, $v);
		}
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		COption::RemoveOption($this->MODULE_ID);
		if (!$arParams['savedata'] && \CModule::IncludeModule($this->MODULE_ID))
		{
			$db = Vettich\Autoposting\PostingFunc::DBTABLE;
			if(!$db::dropTable())
				return false;

			$dblogs = Vettich\Autoposting\PostingFunc::DBLOGSTABLE;
			if(!$dblogs::dropTable())
				return false;

			foreach(\Vettich\Autoposting\PostingFunc::__GetPosts() as $post)
			{
				if(\Vettich\Autoposting\PostingFunc::isModule($post))
				{
					$arPost = \Vettich\Autoposting\PostingFunc::module2($post);
					$db = $arPost['func']::DBTABLE;
					$dbopt = $arPost['func']::DBOPTIONTABLE;
					if(!$db::dropTable() or !$dbopt::dropTable())
						return false;
				}
			}
		}
		return true;
	}

	function InstallEvents()
	{
		RegisterModuleDependences('iblock', 'OnAfterIblockElementAdd', "vettich.autoposting", '\Vettich\Autoposting\Posting', 'OnAfterIblockElementAdd');
		RegisterModuleDependences('main', 'OnAdminListDisplay', 'vettich.autoposting', '\Vettich\Autoposting\PostingIBlocks', 'OnAdminListDisplayHandler');
		RegisterModuleDependences('main', 'OnBeforeProlog', 'vettich.autoposting', '\Vettich\Autoposting\PostingIBlocks', 'OnBeforePrologHandler');
		// RegisterModuleDependences('catalog', 'OnProductAdd', 'vettich.autoposting', '\Vettich\Autoposting\Posting', 'OnProductAdd');
		return true;
	}

	function UnInstallEvents()
	{
		UnRegisterModuleDependences('iblock', 'OnAfterIblockElementAdd', "vettich.autoposting", '\Vettich\Autoposting\Posting', 'OnAfterIblockElementAdd');
		UnRegisterModuleDependences('main', 'OnAdminListDisplay', 'vettich.autoposting', '\Vettich\Autoposting\PostingIBlocks', 'OnAdminListDisplayHandler');
		UnRegisterModuleDependences('main', 'OnBeforeProlog', 'vettich.autoposting', '\Vettich\Autoposting\PostingIBlocks', 'OnBeforePrologHandler');
		// UnRegisterModuleDependences('catalog', 'OnProductAdd', 'vettich.autoposting', '\Vettich\Autoposting\Posting', 'OnProductAdd');
		return true;
	}

	function InstallFiles()
	{
		CopyDirFiles($this->MODULE_ROOT_DIR."/install/bitrix",$_SERVER["DOCUMENT_ROOT"]."/bitrix", true, true);
		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFiles($this->MODULE_ROOT_DIR."/install/bitrix/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFiles($this->MODULE_ROOT_DIR."/install/bitrix/js/vettich.autoposting", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/vettich.autoposting");
		DeleteDirFiles($this->MODULE_ROOT_DIR."/install/bitrix/css/vettich.autoposting", $_SERVER["DOCUMENT_ROOT"]."/bitrix/css/vettich.autoposting");
		DeleteDirFiles($this->MODULE_ROOT_DIR."/install/bitrix/images/vettich.autoposting", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/vettich.autoposting");
		return true;
	}

	function getPosts()
	{
		$posts = array();
		$_dir_name = $this->MODULE_ROOT_DIR.'/lib/posts/';
		$_dir = scandir($_dir_name);
		if($_dir !== false)
			foreach($_dir as $v)
				if($v != '.' && $v != '..' && is_dir($_dir_name.$v))
					$posts[] = $v;
		return $posts;
	}
}
?>