<?
if(!$USER->IsAdmin())
	return;

if(!\Bitrix\Main\Loader::includeModule("iblock"))
	return;
if(!\Bitrix\Main\Loader::includeModule("highloadblock"))
	return;

use Bitrix\Highloadblock as HL;
define("MODULE_ID", "tarakud.wishlist");

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

function getOptionField($arOption, $siteId)
{
	if (empty($arOption))
		return;
	
	if ($arOption[1] == "int")
		$val = COption::GetOptionInt(MODULE_ID, $arOption[0], $arOption[3], $siteId);
	else
		$val = COption::GetOptionString(MODULE_ID, $arOption[0], $arOption[3], $siteId);
	
	if (empty($val) && !empty($arOption[3]))
		$val = $arOption[3];
	
	$val = htmlspecialcharsbx($val);
	$type = $arOption[4];
	$id = htmlspecialcharsbx($arOption[0])."_".$siteId;
	$name = htmlspecialcharsbx($arOption[0])."[".$siteId."]";
	?>	
	<tr>
		<td width="40%" nowrap class="adm-detail-content-cell-l">
			<label for="<?=$id?>"><?=$arOption[2]?>:</label>
		</td>
		<td class="adm-detail-content-cell-r">
			<?if ($type[0] == "checkbox"):?>
				<input type="checkbox" id="<?=$id?>" name="<?=$name?>" value="Y"<?if($val=="Y")echo" checked";?>>
			<?elseif ($type[0] == "select"):?>
				<select id="<?=$id?>" name="<?=$name?>">
					<?
					foreach($type[2] as $id => $name)
					{
						?><option value="<?=$id?>"<?if ($val == $id) echo " selected";?>><?=$name?></option><?
					}
					?>
				</select>
			<?elseif ($type[0] == "text"):?>
				<input type="text" size="40" maxlength="255" value="<?=htmlspecialcharsbx($val)?>" name="<?=$name?>" id="<?=$id?>">
			<?endif;?>
		</td>
	</tr>
	<?
}

function saveOptionField($arAllOptions, $siteId)
{
	foreach($arAllOptions as $arOption)
	{
		$name = $arOption[0];
		$val = $_REQUEST[$name][$siteId];

		if($arOption[3][0] == "checkbox" && $val != "Y")
			$val = "N";
		
		if ($arOption[1] == "int")
			COption::SetOptionInt(MODULE_ID, $name, $val, false, $siteId);
		else
			COption::SetOptionString(MODULE_ID, $name, $val, false, $siteId);
	}
}

/* Get highload list */
$arHighload = array("0" => GetMessage('W_SELECT_IBLOCK'));
$rsData = HL\HighloadBlockTable::getList(array(
	"select" => array("ID", "NAME"),
	"order" => array("ID" => "ASC"),
));
while ($arData = $rsData->Fetch())
	$arHighload[$arData["ID"]] = "[".$arData["ID"]."] ".$arData["NAME"];

/* Get site list */
$arSite = array();
$rsSites = CSite::GetList($by="sort", $order="asc", array());
while($arRes = $rsSites->GetNext())
	$arSite[] = array("ID" => $arRes["ID"], "NAME" => $arRes["NAME"]);

$siteCurrent = COption::GetOptionString($MODULE_ID, "site", "");
if (strlen($siteCurrent) <= 0)
	$siteCurrent = $arSite[0]["ID"];

/* Options */
$arAllOptions = array(
	array("wishlist_iblock", "int", GetMessage("W_WISH_IBLOCK"), "0", array("select", "0", $arHighload)),
	array("wishlist_page", "text", GetMessage("W_PAGE"), "/wishlist/", array("text", "")),
	array("wishlist_add", "text", GetMessage("W_ADDED_TITLE"), GetMessage("W_ADDED_VAL"), array("text", "")),
	array("wishlist_add_title", "text", GetMessage("W_ADDED_TITLE_TITLE"), GetMessage("W_ADDED_TITLE_VAL"), array("text", "")),
	array("wishlist_del", "text", GetMessage("W_DEL_TITLE"), GetMessage("W_DEL_VAL"), array("text", "")),
	array("wishlist_del_title", "text", GetMessage("W_DEL_TITLE_TITLE"), GetMessage("W_DEL_TITLE_VAL"), array("text", "")),
);

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "ib_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid())
{
	if (is_set($_REQUEST["site"]) && strlen($_REQUEST["site"]) > 0)
	{
		$siteCurrent = trim($_REQUEST["site"]);
		COption::SetOptionString(MODULE_ID, "site", $siteCurrent);
	}
	
	//COption::RemoveOption(MODULE_ID, "", $siteCurrent);
	
	if(strlen($RestoreDefaults)>0)
		COption::RemoveOption(MODULE_ID);
	else
	{
		foreach ($arSite as $site)
		{
			$dirOld = COption::GetOptionString(MODULE_ID, "wishlist_page", "/wishlist/", $site["ID"]);
			$conditionOld = "#^".$dirOld."wl([0-9]+)/(.*)#";
			
			saveOptionField($arAllOptions, $site["ID"]);
			
			$dir = COption::GetOptionString(MODULE_ID, "wishlist_page", "/wishlist/", $site["ID"]);

			CUrlRewriter::Update(
				array("SITE_ID" => $site["ID"], "CONDITION" => $conditionOld),
				array(
					"CONDITION" => "#^".$dir."wl([0-9]+)/(.*)#",
					"ID" => "",
					"PATH" => $dir."index.php",
					"RULE" => "USER=\$1"
				)
			);
		}
	}
	
	if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
		LocalRedirect($_REQUEST["back_url_settings"]);
	else
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&mid_menu=1&lang=".urlencode(LANGUAGE_ID)."&".$tabControl->ActiveTabParam());
}

$tabControl->Begin();
?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?echo LANGUAGE_ID?>">
<?$tabControl->BeginNextTab();?>
	<tr>
		<td width="40%" nowrap>
			<label for="w-site"><?=GetMessage('W_SELECT_SITE');?>:</label>
		<td width="60%">
			<select name="site" id="w-site" OnChange="selectSite(this.value)">
			<?
			foreach ($arSite as $val)
			{
				$checked = "";
				if ($siteCurrent == $val["ID"])
					$checked = " selected";

				echo "<option value=\"".$val["ID"]."\"".$checked.">".$val["NAME"]." [".$val["ID"]."]</option>";
			}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td valign="top" colspan="2">
		<?foreach ($arSite as $site):?>
		<div id="par_site_<?=($site["ID"])?>" style="display: <?=($siteCurrent == $site["ID"] ? "inline" : "none");?>">
		<table cellpadding="0" cellspacing="2" class="adm-detail-content-table edit-table">
			<tr class="heading">
				<td align="center" colspan="2"><?=GetMessage("W_OPTIONS")." ".$site["NAME"]?></td>
			</tr>
			<?
			foreach($arAllOptions as $arOption)
			{
				getOptionField($arOption, $site["ID"]);
			}
			?>
		</table>
		</div>
		<?endforeach?>
		</td>
	</tr>

<?$tabControl->Buttons();?>
	<input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>" class="adm-btn-save">
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>

<script language="javascript">
var cur_site = '<?=CUtil::JSEscape($siteCurrent)?>';

function selectSite(current)
{
	if (current == cur_site)
		return;

	var last_handler = document.getElementById('par_site_' + cur_site);
	var current_handler = document.getElementById('par_site_' + current);

	last_handler.style.display = 'none';
	current_handler.style.display = 'inline';
	cur_site = current;

	return;
}
</script>