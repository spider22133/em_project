<?
define("NO_AGENT_CHECK", true);
define("STOP_STATISTICS", true);
define("NO_KEEP_STATISTIC", true);
define("PUBLIC_AJAX_MODE", true);
define("NO_AGENT_STATISTIC", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CUtil::DecodeUriComponent($_POST);

if($_SERVER["REQUEST_METHOD"] == "POST" && check_bitrix_sessid() && $_POST["ajax"] == "Y")
{
	$arParams = unserialize($_POST["ajax_params"]);
	unset($_POST["ajax_params"]);
	$arParams["AJAX"] = "Y";
	
	if (!empty($arParams["COMPONENT_TEMPLATE"]))
	{
		$file = $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/tarakud/sale.order.oneclick/templates/".$arParams["COMPONENT_TEMPLATE"]."/ajax.php";
		
		if (!file_exists($file))
			$arParams["COMPONENT_TEMPLATE"] = "";
	}

	$APPLICATION->RestartBuffer();
	header('Content-Type: text/html; charset='.LANG_CHARSET);
	$APPLICATION->IncludeComponent(
		"tarakud:sale.order.oneclick",
		$arParams["COMPONENT_TEMPLATE"],
		$arParams
	);
}
?>