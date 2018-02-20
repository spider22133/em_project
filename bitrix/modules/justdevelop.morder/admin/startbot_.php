<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
if($APPLICATION->GetGroupRight("justdevelop.morder") < "R") 
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
	
if(IsModuleInstalled("justdevelop.morder")) {
	$API_KEY = COption::GetOptionString('justdevelop.morder', 'password');
	$BOT_NAME = COption::GetOptionString('justdevelop.morder', 'login');
	if(strlen($API_KEY) > 5 && strlen($BOT_NAME) > 3)
	{
		require_once dirname(__FILE__).'/../classes/general/Telegram.php';
		require_once dirname(__FILE__).'/../classes/general/Entity.php';
		require_once dirname(__FILE__).'/../classes/general/Command.php';

		try {
		    $telegram = new JUSTDEVELOP\TelegramBot\Telegram($API_KEY, $BOT_NAME);
		    $telegram->handleGetUpdates();
		} catch (\Exception $e) {}
	}
}
?>