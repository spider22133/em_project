<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters["NAME_SHOW"] = array(
	"NAME" => GetMessage("SALE_NAME_SHOW"),
	"TYPE"=>"CHECKBOX",
	"VALUES" => "N",
	"DEFAULT"=>"N",
	"PARENT" => "ADDITIONAL_SETTINGS",
);

$arTemplateParameters["DELIVERY_LIST_SHOW"] = array(
	"NAME" => GetMessage("DELIVERY_LIST_SHOW"),
	"TYPE"=>"CHECKBOX",
	"VALUES" => "N",
	"DEFAULT"=>"N",
	"PARENT" => "ADDITIONAL_SETTINGS",
);

$arTemplateParameters["PAYSYSTEM_LIST_SHOW"] = array(
	"NAME" => GetMessage("PAYSYSTEM_LIST_SHOW"),
	"TYPE"=>"CHECKBOX",
	"VALUES" => "N",
	"DEFAULT"=>"N",
	"PARENT" => "ADDITIONAL_SETTINGS",
);

?>