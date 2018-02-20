<?
$dbEvent = CEventMessage::GetList($b="ID", $order="ASC", array("EVENT_NAME" => "SALE_NEW_ORDER"));
if(!($dbEvent->Fetch()))
{
	$langs = CLanguage::GetList(($b=""), ($o=""));
	while($lang = $langs->Fetch())
	{
		$lid = $lang["LID"];
		IncludeModuleLangFile(__FILE__, $lid);

		$et = new CEventType;
		$et->Add(array(
			"LID" => $lid,
			"EVENT_NAME" => "SALE_NEW_ORDER",
			"NAME" => GetMessage("ORDER_EVENT_NAME"),
			"DESCRIPTION" => "",
		));

		$arSites = array();
		$sites = CSite::GetList(($b=""), ($o=""), array("LANGUAGE_ID"=>$lid));
		while ($site = $sites->Fetch())
			$arSites[] = $site["LID"];

		if(count($arSites) > 0)
		{
			$emess = new CEventMessage;
			
			$emess->Add(array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => "SALE_NEW_ORDER",
				"LID" => $arSites,
				"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
				"EMAIL_TO" => "#EMAIL#",
				"BCC" => "",
				"BCC" => "#DEFAULT_EMAIL_FROM#",
				"SUBJECT" => GetMessage("ORDER_EVENT_TITLE"),
				"MESSAGE" => GetMessage("ORDER_EVENT_TEXT"),
				"BODY_TYPE" => "text",
			));
		}
	}
}
?>