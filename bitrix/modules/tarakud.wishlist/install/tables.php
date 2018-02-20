<?
\Bitrix\Main\Loader::includeModule("highloadblock");

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

$arTables = array(
	"WishList" => array(
		"NAME" => "WishList",
		"TABLE_NAME" => "wish_list",
		"FIELDS" => array(
			0 => array(
				"FIELD_NAME" => "UF_IBLOCK_ID",
				"USER_TYPE_ID" => "integer",
				"XML_ID" => "",
				"SORT" => "10",
				"MULTIPLE" => "N",
				"MANDATORY" => "Y",
				"SHOW_FILTER" => "N",
				"SHOW_IN_LIST" => "Y",
				"EDIT_IN_LIST" => "Y",
				"IS_SEARCHABLE" => "N",
				"SETTINGS" => array(),
				"EDIT_FORM_LABEL" => array("ru"=>GetMessage('TARAKUD_INS_IBLOCK')),
				"LIST_COLUMN_LABEL" => array("ru"=>GetMessage('TARAKUD_INS_IBLOCK')),
				"LIST_FILTER_LABEL" => array("ru"=>GetMessage('TARAKUD_INS_IBLOCK')),
				"ERROR_MESSAGE" => array("ru"=>GetMessage('TARAKUD_INS_IBLOCK')),
				"HELP_MESSAGE" => array("ru"=>GetMessage('TARAKUD_INS_IBLOCK')),
			),
			1 => array(
				"FIELD_NAME" => "UF_ELEMENT_ID",
				"USER_TYPE_ID" => "integer",
				"XML_ID" => "",
				"SORT" => "20",
				"MULTIPLE" => "N",
				"MANDATORY" => "Y",
				"SHOW_FILTER" => "N",
				"SHOW_IN_LIST" => "Y",
				"EDIT_IN_LIST" => "Y",
				"IS_SEARCHABLE" => "N",
				"SETTINGS" => array(),
				"EDIT_FORM_LABEL" => array("ru"=>GetMessage('TARAKUD_INS_ELEMENT')),
				"LIST_COLUMN_LABEL" => array("ru"=>GetMessage('TARAKUD_INS_ELEMENT')),
				"LIST_FILTER_LABEL" => array("ru"=>GetMessage('TARAKUD_INS_ELEMENT')),
				"ERROR_MESSAGE" => array("ru"=>GetMessage('TARAKUD_INS_ELEMENT')),
				"HELP_MESSAGE" => array("ru"=>GetMessage('TARAKUD_INS_ELEMENT')),
			),
			2 => array(
				"FIELD_NAME" => "UF_USER_ID",
				"USER_TYPE_ID" => "integer",
				"XML_ID" => "",
				"SORT" => "20",
				"MULTIPLE" => "N",
				"MANDATORY" => "Y",
				"SHOW_FILTER" => "N",
				"SHOW_IN_LIST" => "Y",
				"EDIT_IN_LIST" => "Y",
				"IS_SEARCHABLE" => "N",
				"SETTINGS" => array(),
				"EDIT_FORM_LABEL" => array("ru"=>GetMessage('TARAKUD_INS_USER')),
				"LIST_COLUMN_LABEL" => array("ru"=>GetMessage('TARAKUD_INS_USER')),
				"LIST_FILTER_LABEL" => array("ru"=>GetMessage('TARAKUD_INS_USER')),
				"ERROR_MESSAGE" => array("ru"=>GetMessage('TARAKUD_INS_USER')),
				"HELP_MESSAGE" => array("ru"=>GetMessage('TARAKUD_INS_USER')),
			),
			3 => array(
				"FIELD_NAME" => "UF_DATE_INSERT",
				"USER_TYPE_ID" => "datetime",
				"XML_ID" => "",
				"SORT" => "30",
				"MULTIPLE" => "N",
				"MANDATORY" => "Y",
				"SHOW_FILTER" => "N",
				"SHOW_IN_LIST" => "Y",
				"EDIT_IN_LIST" => "Y",
				"IS_SEARCHABLE" => "N",
				"SETTINGS" => array(
					"DEFAULT_VALUE" => array("TYPE" => "NOW", "VALUE" => ""),
				),
				"EDIT_FORM_LABEL" => array("ru"=>GetMessage('TARAKUD_INS_DATE')),
				"LIST_COLUMN_LABEL" => array("ru"=>GetMessage('TARAKUD_INS_DATE')),
				"LIST_FILTER_LABEL" => array("ru"=>GetMessage('TARAKUD_INS_DATE')),
				"ERROR_MESSAGE" => array("ru"=>GetMessage('TARAKUD_INS_DATE')),
				"HELP_MESSAGE" => array("ru"=>GetMessage('TARAKUD_INS_DATE')),
			)
		)
	),
);

function saveOption($iblockId)
{
	$arAllOptions = array(
		array("wishlist_iblock", "int", $iblockId),
		array("wishlist_page", "text", "/wishlist/"),
		array("wishlist_add", "text", GetMessage("W_ADDED_VAL")),
		array("wishlist_add_title", "text", GetMessage("W_ADDED_TITLE_VAL")),
		array("wishlist_del", "text", GetMessage("W_DEL_VAL")),
		array("wishlist_del_title", "text", GetMessage("W_DEL_TITLE_VAL")),
	);
	
	$rsSites = CSite::GetList($by="sort", $order="asc", array());
	while($arRes = $rsSites->Fetch())
	{
		foreach ($arAllOptions as $arOption)
		{
			if ($arOption[1] == "int")
				COption::SetOptionInt("tarakud.wishlist", $arOption[0], $arOption[2], false, $arRes["ID"]);
			else
				COption::SetOptionString("tarakud.wishlist", $arOption[0], $arOption[2], false, $arRes["ID"]);
		}
	}
}

foreach ($arTables as $table => $arTable)
{
	$rsData = HL\HighloadBlockTable::getList(array(
			'select' => array('*'),
			'order' => array('ID' => 'ASC'),
			'filter' => array('NAME' => $table)
		)
	);
	if (!$arRes = $rsData->fetch())
	{
		$arData = array(
			'NAME' => $arTable['NAME'],
			'TABLE_NAME' => $arTable['TABLE_NAME'],
		);
		$result = HL\HighloadBlockTable::add($arData);
		$ID = $result->getId();
		if ($result->isSuccess())
		{
			saveOption($ID);
			$obUserField  = new CUserTypeEntity;
			
			foreach ($arTable["FIELDS"] as $arField)
			{
				$arField["ENTITY_ID"] = "HLBLOCK_".$ID;
				$IDF = $obUserField->Add($arField);
			}
		}
	}
}
?>