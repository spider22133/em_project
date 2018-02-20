<?
use Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (!function_exists("showFilePropertyField"))
{
	function showFilePropertyField($name, $property_fields, $values, $max_file_size_show=50000)
	{
		$res = "";

		if (!is_array($values) || empty($values))
			$values = array(
				"n0" => 0,
			);

		if ($property_fields["MULTIPLE"] == "N")
		{
			$res = "<label for=\"\"><input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[0]\" id=\"".$name."[0]\"></label>";
		}
		else
		{
			$res = '
			<script type="text/javascript">
				function addControl(item)
				{
					var current_name = item.id.split("[")[0],
						current_id = item.id.split("[")[1].replace("[", "").replace("]", ""),
						next_id = parseInt(current_id) + 1;

					var newInput = document.createElement("input");
					newInput.type = "file";
					newInput.name = current_name + "[" + next_id + "]";
					newInput.id = current_name + "[" + next_id + "]";
					newInput.onchange = function() { addControl(this); };

					var br = document.createElement("br");
					var br2 = document.createElement("br");

					BX(item.id).parentNode.appendChild(br);
					BX(item.id).parentNode.appendChild(br2);
					BX(item.id).parentNode.appendChild(newInput);
				}
			</script>
			';

			$res .= "<label for=\"\"><input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[0]\" id=\"".$name."[0]\"></label>";
			$res .= "<br/><br/>";
			$res .= "<label for=\"\"><input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[1]\" id=\"".$name."[1]\" onChange=\"javascript:addControl(this);\"></label>";
		}

		return $res;
	}
}

if (!function_exists("PrintPropsForm"))
{
	function PrintPropsForm($arSource = array())
	{
		foreach($arSource as $arProperties)
		{
			?>
				<?if($arProperties["TYPE"] != "CHECKBOX"):?>
				<div class="form-group">
					<label for="<?=$arProperties["FIELD_NAME"]?>">
						<?if($arProperties["REQUIED"]==="Y"):?>
							<span class="popup-required">*</span>
						<?endif;?>
						<?=$arProperties["NAME"]?>
					</label>
				<?endif;?>
				
				<?
				if($arProperties["TYPE"] == "CHECKBOX")
				{
					?>
					<div class="checkbox">
						<label for="<?=$arProperties["FIELD_NAME"]?>">
							<input id="<?=$arProperties["FIELD_NAME"]?>" type="checkbox" name="<?=$arProperties["FIELD_NAME"]?>" value="Y"<?if ($arProperties["CHECKED"]=="Y") echo " checked";?>> <?=$arProperties["NAME"]?>
						</label>

					<?
				}
				elseif($arProperties["TYPE"] == "TEXT")
				{
					$type = "text";
					$arMail = array("email", "mail", "e-mail");
					if ( in_array(strtolower($arProperties["CODE"]), $arMail) )
						$type = "email";
					?>
					<input id="<?=$arProperties["FIELD_NAME"]?>" class="form-control" type="<?=$type?>" maxlength="250" size="<?=$arProperties["SIZE1"]?>" value="<?=$arProperties["VALUE"]?>" name="<?=$arProperties["FIELD_NAME"]?>" placeholder="<?=GetMessage("SALE_ENTER")." ".$arProperties["NAME"]?>" <?=($arProperties["REQUIED"]==="Y")?"required":"";?> >
					<?
				}
				elseif($arProperties["TYPE"] == "SELECT")
				{
					?>
					<select id="<?=$arProperties["FIELD_NAME"]?>" class="form-control" name="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>" <?=($arProperties["REQUIED"]==="Y")?"required":"";?>>
					<?
					foreach($arProperties["VARIANTS"] as $arVariants)
					{
						?>
						<option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y") echo " selected";?>><?=$arVariants["NAME"]?></option>
						<?
					}
					?>
					</select>
					<?
				}
				elseif ($arProperties["TYPE"] == "MULTISELECT")
				{
					?>
					<select multiple id="<?=$arProperties["FIELD_NAME"]?>" class="form-control" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>" <?=($arProperties["REQUIED"]==="Y")?"required":"";?>>
					<?
					foreach($arProperties["VARIANTS"] as $arVariants)
					{
						?>
						<option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y") echo " selected";?>><?=$arVariants["NAME"]?></option>
						<?
					}
					?>
					</select>
					<?
				}
				elseif ($arProperties["TYPE"] == "TEXTAREA")
				{
					?>
					<textarea id="<?=$arProperties["FIELD_NAME"]?>" class="form-control" rows="<?=$arProperties["SIZE2"]?>" cols="<?=$arProperties["SIZE1"]?>" name="<?=$arProperties["FIELD_NAME"]?>" placeholder="<?=GetMessage("SALE_ENTER")." ".$arProperties["NAME"]?>" <?=($arProperties["REQUIED"]==="Y")?"required":"";?>><?=$arProperties["VALUE"]?></textarea>
					<?
				}
				elseif ($arProperties["TYPE"] == "LOCATION")
				{
					$value = intval($arProperties["DEFAULT_VALUE"]);
					if (is_array($arProperties["VARIANTS"]) && count($arProperties["VARIANTS"]) > 0)
					{
						foreach ($arProperties["VARIANTS"] as $arVariant)
						{
							if ($arVariant["SELECTED"] == "Y")
							{
								$value = $arVariant["ID"];
								break;
							}
						}
					}
					echo "<div class='popup-location'>";
					$GLOBALS["APPLICATION"]->IncludeComponent(
						"bitrix:sale.ajax.locations",
						"",
						array(
							"AJAX_CALL" => "N",
							"COUNTRY_INPUT_NAME" => "COUNTRY",
							"REGION_INPUT_NAME" => "REGION",
							"CITY_INPUT_NAME" => $arProperties["FIELD_NAME"],
							"CITY_OUT_LOCATION" => "Y",
							"LOCATION_VALUE" => $value,
							"ORDER_PROPS_ID" => $arProperties["ID"],
							"ONCITYCHANGE" => "",
							"SIZE1" => $arProperties["SIZE1"],
						),
						null,
						array("HIDE_ICONS"=> "Y")
					);
					echo "</div>";
				}
				elseif ($arProperties["TYPE"] == "RADIO")
				{
					echo "<div>";
					foreach($arProperties["VARIANTS"] as $arVariants)
					{
						?>
						<input type="radio" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["VALUE"]?>" value="<?=$arVariants["VALUE"]?>"<?if($arVariants["CHECKED"] == "Y") echo " checked";?>> <label for="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["VALUE"]?>"><?=$arVariants["NAME"]?></label><br />
						<?
					}
					echo "</div>";
				}
				elseif ($arProperties["TYPE"] == "FILE")
				{
					?>
					<div class="bx_block r3x1">
						<?=showFilePropertyField("ORDER_PROP_".$arProperties["ID"], $arProperties, $arProperties["VALUE"], $arProperties["SIZE1"])?>
						<?if (strlen(trim($arProperties["DESCRIPTION"])) > 0):?>
							<div class="bx_description"><?=$arProperties["DESCRIPTION"]?></div>
						<?endif?>
					</div>
					<?
				}
				elseif ($arProperties["TYPE"] == "DATE")
				{
					?>
					<div class="popup-date">
						<?
						global $APPLICATION;

						$APPLICATION->IncludeComponent('bitrix:main.calendar', '', array(
							'SHOW_INPUT' => 'Y',
							'INPUT_NAME' => "ORDER_PROP_".$arProperties["ID"],
							'INPUT_VALUE' => $arProperties["VALUE"],
							'SHOW_TIME' => 'N'
						), null, array('HIDE_ICONS' => 'N'));
						?>
						<div class="clearfix"></div>
					</div>
					<?
				}
				?>
			</div>
			<?
		}
		
		return true;
	}
}
?>

<?if ($arParams["USE_COUNT"] == "Y" && $component->isShop):?>
	<div class="form-group">
		<input type="text" name="quantity" value="1" class="form-control popup-quantity js-popup-quantity">
		<div class="popup-control js-popup-control">
			<button class="popup-control-calc up" data-type="up" type="button" title="<?=Loc::getMessage('SALE_COUNT_UP')?>"></button>
			<button class="popup-control-calc down" data-type="down" type="button" title="<?=Loc::getMessage('SALE_COUNT_DOWN')?>"></button>
		</div>
		<div class="form-group-measure">
			(<?=$arResult["ELEMENTS"]["CATALOG_MEASURE_NAME"]?>)
		</div>
		<div class="clearfix"></div>
	</div>
<?endif;?>

<?PrintPropsForm($arResult["PERSON_TYPE_PROPS"]);?>

<?if ($arParams["USE_COMMENT"] == "Y" && $component->isShop):?>
	<div class="form-group">
		<textarea id="popup-comment" class="form-control" name="USER_DESCRIPTION" placeholder="<?=Loc::getMessage('SALE_COMMENT')?>"></textarea>
	</div>
<?endif;?>

<?if (!$component->isShop):?>
	<div class="form-group">
		<input type="text" maxlength="250" value="" name="name" class="form-control" id="popup-fio" placeholder="<?=Loc::getMessage('SALE_FIO')?>" autofocus>
	</div>
	<div class="form-group">
		<input type="text" maxlength="250" value="" name="phone" class="form-control" id="popup-phone" placeholder="<?=Loc::getMessage('SALE_PHONE')?>">
	</div>
	<div class="form-group">
		<input type="email" maxlength="250" value="<?=$USER->GetEmail()?>" name="mail" class="form-control" id="popup-mail" placeholder="E-mail" required>
	</div>
<?endif;?>

<?if (!empty($arResult["DELIVERY_LIST"]) && $arParams["DELIVERY_LIST_SHOW"] == "Y"):?>
	<div class="form-group">
		<label class="popup-label"><?=Loc::getMessage('SALE_DELIVERY_LIST_TITLE')?></label>
	
		<?foreach ($arResult["DELIVERY_LIST"] as $key => $arDelivery):?>
		<label class="radio-inline">
			<input type="radio" name="deliverylist" value="<?=$arDelivery["ID"]?>" <?=($key == 0)?"checked":"";?>> <?=$arDelivery["NAME"]?>
			<?if ($arDelivery["PRICE"] > 0):?>
				<?=$arDelivery["PRICE_PRINT"]?>
			<?endif;?>
		</label>
		<?endforeach;?>
	</div>
<?endif;?>

<?if (!empty($arResult["PAYSYSTEM_LIST"]) && $arParams["PAYSYSTEM_LIST_SHOW"] == "Y"):?>
	<div class="form-group">
		<label class="popup-label"><?=Loc::getMessage('SALE_PAYSYSTEM_LIST_TITLE')?></label>
	
		<?foreach ($arResult["PAYSYSTEM_LIST"] as $key => $arPaysystem):?>
			<label class="radio-inline">
				<input type="radio" name="paysystemlist" value="<?=$arPaysystem["ID"]?>" <?=($key == 0)?"checked":"";?>> <?=$arPaysystem["NAME"]?>
			</label>
		<?endforeach;?>
	</div>
<?endif;?>

<?if ($arParams["PERSONAL_DATA"] == "Y"):?>
	<div class="popup-personal-data"><?=GetMessage("SALE_PERSONAL_DATA_TEXT")?></div>
<?endif;?>

<?if($arParams["USE_CAPTCHA"] == "Y"):?>
	<div class="form-group">	
		<label for="captcha_word<?=$arParams["PRODUCT_ID"]?>"><span class="required">*</span><?=Loc::getMessage("SALE_CAPTCHA")?></label>
		<div>
			<input type="hidden" id="captcha_sid<?=$arParams["PRODUCT_ID"]?>" name="captcha_sid" value="<?=$arResult["capCode"]?>">
			<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA" id="captha-img<?=$arParams["PRODUCT_ID"]?>">
			<div class="mf-text"><?=Loc::getMessage("SALE_CAPTCHA_CODE")?></div>
			
			<input type="text" name="captcha_word" id="captcha_word<?=$arParams["PRODUCT_ID"]?>" class="form-control popup-form-captha" maxlength="50" value="">
			<input class="popup-form-recaptcha js-popup-recaptcha" type="button" name="reload-captcha" value="" data-id="<?=$arParams["PRODUCT_ID"]?>" title="<?=Loc::getMessage('SALE_CAPTHA_TITLE');?>">
		</div>
	</div>
	<br>
<?endif;?>