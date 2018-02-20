<?
$m_id = "vettich.autoposting";
if(IsModuleInstalled($m_id))
{
	$updater->CopyFiles("install/bitrix/js", "js");
	$updater->CopyFiles("install/bitrix/css", "css");
	$updater->CopyFiles("install/bitrix/images", "images");
}