<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
?>
<?=ShowError($arResult["strProfileError"]);?>
<?
if ($arResult['DATA_SAVED'] == 'Y')
	echo ShowNote(GetMessage('PROFILE_DATA_SAVED'));
?>
<div class="bx_profile bx_<?=$arResult["THEME"]?>">
	<form method="post" name="form1" action="<?=$arResult["FORM_TARGET"]?>?" enctype="multipart/form-data">
		<?=$arResult["BX_SESSION_CHECK"]?>
		<input type="hidden" name="lang" value="<?=LANG?>" />
		<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
		<input type="hidden" name="LOGIN" value=<?=$arResult["arUser"]["LOGIN"]?> />
		<input type="hidden" name="EMAIL" value=<?=$arResult["arUser"]["EMAIL"]?> />

<div class="col-xs-12 col-sm-6">
    <h2><?=GetMessage("LEGEND_PROFILE")?></h2>
		<strong><?=GetMessage('NAME')?></strong><br/>
		<input type="text" name="NAME" maxlength="50" value="<?=$arResult["arUser"]["NAME"]?>" /><br>

		<strong><?=GetMessage('LAST_NAME')?></strong><br/>
		<input type="text" name="LAST_NAME" maxlength="50" value="<?=$arResult["arUser"]["LAST_NAME"]?>" /><br>

		<strong><?=GetMessage('SECOND_NAME')?></strong><br/>
		<input type="text" name="SECOND_NAME" maxlength="50"  value="<?=$arResult["arUser"]["SECOND_NAME"]?>" /><br>

		<strong><?=GetMessage('EMAIL')?><?if($arResult["EMAIL_REQUIRED"]):?><span class="starrequired">*</span><?endif?></strong>
		<input type="text" name="EMAIL" maxlength="50" value="<? echo $arResult["arUser"]["EMAIL"]?>" /><br>

		<strong><?=GetMessage('USER_PHONE')?><span class="starrequired">*</span></strong>
		<input type="tel" name="PERSONAL_PHONE" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_PHONE"]?>" /><br>

        <strong><?=GetMessage('USER_CITY')?><strong>
        <input type="text" name="PERSONAL_CITY" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_CITY"]?>" /><br><br><br>
</div>
        <div class="col-xs-12 col-sm-6">
		<h2><?=GetMessage("MAIN_PSWD")?></h2>
		<strong><?=GetMessage('NEW_PASSWORD_REQ')?></strong><br/>
		<input type="password" name="NEW_PASSWORD" maxlength="50" value="" autocomplete="off" /> <br>

		<strong><?=GetMessage('NEW_PASSWORD_CONFIRM')?></strong><br/>
		<input type="password" name="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off" /> <br>

		<?if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>
			<h2><?=strlen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB")?></h2>
			<?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>
				<strong><?=$arUserField["EDIT_FORM_LABEL"]?><?if ($arUserField["MANDATORY"]=="Y"):?><span class="starrequired">*</span><?endif;?></strong><br/>
				<?$APPLICATION->IncludeComponent(
					"bitrix:system.field.edit",
					$arUserField["USER_TYPE"]["USER_TYPE_ID"],
					array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField), null, array("HIDE_ICONS"=>"Y")
				);?>
				<br/>
			<?endforeach;?>
		<?endif;?>
            <input name="save" value="<?=GetMessage("MAIN_SAVE")?>" class="btn bx_bt_button" type="submit">

        <br>
            <?
            if($arResult["SOCSERV_ENABLED"])
            {
            $APPLICATION->IncludeComponent("bitrix:socserv.auth.split", "template1", Array(
            "SHOW_PROFILES" => "Y",
            "ALLOW_DELETE" => "Y"
            ),
            false
            );
            }
            ?>
        </div>


	</form>
</div>

