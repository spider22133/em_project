<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);

if(CModule::IncludeModule('vettich.autoposting'))
{
	$data = $_GET;
	unset($data['method']);
	echo json_encode(\Vettich\Autoposting\Posts\vk\Posting::method($_GET['method'], $data));
	exit;
}
