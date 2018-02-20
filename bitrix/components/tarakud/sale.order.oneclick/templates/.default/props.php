<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (!defined("SALE_PROPS"))
{
	function PrintPropsForm($arSource = array())
	{
		if (!empty($arSource))
		{
			foreach($arSource as $arProperties)
			{
				?>
				<tr>
					<td class="title">
						<?if($arProperties["REQUIED"]==="Y"):?>
							<span class="required">*</span>
						<?endif;?>
						<?=$arProperties["NAME"]?>
					</td>
					<td class="field">
						<?
						if($arProperties["TYPE"] == "CHECKBOX")
						{
							?>
							<input type="hidden" name="<?=$arProperties["FIELD_NAME"]?>" value="">
							<input type="checkbox" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" value="Y"<?if ($arProperties["CHECKED"]=="Y") echo " checked";?>>
							<?
						}
						elseif($arProperties["TYPE"] == "TEXT")
						{
							?>
							<input type="text" maxlength="250" size="<?=$arProperties["SIZE1"]?>" value="<?=$arProperties["VALUE"]?>" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>">
							<?
						}
						elseif($arProperties["TYPE"] == "SELECT")
						{
							?>
							<select name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>">
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
							<select multiple name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>">
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
							<textarea rows="<?=$arProperties["SIZE2"]?>" cols="<?=$arProperties["SIZE1"]?>" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>"><?=$arProperties["VALUE"]?></textarea>
							<?
						}
						elseif ($arProperties["TYPE"] == "LOCATION")
						{
							$value = 0;
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

							$GLOBALS["APPLICATION"]->IncludeComponent(
								"bitrix:sale.ajax.locations",
								'.default',
								array(
									"AJAX_CALL" => "N",
									"COUNTRY_INPUT_NAME" => "COUNTRY",
									"REGION_INPUT_NAME" => "REGION",
									"CITY_INPUT_NAME" => $arProperties["FIELD_NAME"],
									"CITY_OUT_LOCATION" => "Y",
									"LOCATION_VALUE" => $value,
									"ORDER_PROPS_ID" => $arProperties["ID"],
									"ONCITYCHANGE" => ($arProperties["IS_LOCATION"] == "Y" || $arProperties["IS_LOCATION4TAX"] == "Y") ? "submitForm()" : "",
									"SIZE1" => $arProperties["SIZE1"],
								),
								null,
								array('HIDE_ICONS' => 'Y')
							);
						}
						elseif ($arProperties["TYPE"] == "RADIO")
						{
							foreach($arProperties["VARIANTS"] as $arVariants)
							{
								?>
								<input type="radio" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["VALUE"]?>" value="<?=$arVariants["VALUE"]?>"<?if($arVariants["CHECKED"] == "Y") echo " checked";?>> <label for="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["VALUE"]?>"><?=$arVariants["NAME"]?></label><br />
								<?
							}
						}
						?>
					</td>
				</tr>
				<?
			}

			return true;
		}

		return false;
	}
	?>
	<div style="display:none;">
		<?$APPLICATION->IncludeComponent(
				"bitrix:sale.ajax.locations",
				$arParams["TEMPLATE_LOCATION"],
				array(
					"AJAX_CALL" => "N",
					"COUNTRY_INPUT_NAME" => "COUNTRY_tmp",
					"REGION_INPUT_NAME" => "REGION_tmp",
					"CITY_INPUT_NAME" => "tmp",
					"CITY_OUT_LOCATION" => "Y",
					"LOCATION_VALUE" => "",
					"ONCITYCHANGE" => "submitForm()",
				),
				null,
				array('HIDE_ICONS' => 'Y')
			);
		?>
	</div>
	<?
}
define("SALE_PROPS", "Y");
?>

<table>
<?if (!empty($arResult["ELEMENTS"]["OFFERS"])):?>
<tr>
	<td class="title"></td>
	<td class="field">
		<?if ($arParams["OFFERS_SHOW"] == "all"):?>
			<?foreach ($arResult["ELEMENTS"]["OFFERS"] as $key => $val):?>
				<div class="order-popup-offers">
					<div class="order-offers-name">
						<input type="radio" name="element_offers" value="<?=$val["ID"]?>" id="element_offers_<?=$val["ID"]?>" <?=($key == 0)?"checked":"";?>>
						<label for="element_offers_<?=$val["ID"]?>"><?=$val["NAME"]?></label>
					</div>
					<?if (!empty($val["DISPLAY_PROPERTIES"])):?>
						<?foreach ($val["DISPLAY_PROPERTIES"] as $item):?>
							<div class="order-offers-props"><?=$item["NAME"]?>: <?=$item["DISPLAY_VALUE"]?></div>
						<?endforeach;?>
					<?endif;?>
					<div class="order-offers-props price"><?=GetMessage("SALE_PRICE")?>: <?=$val["PRICE"]?></div>
				</div>
			<?endforeach;?>
		<?elseif ($arParams["OFFERS_SHOW"] == "list"):?>
			<select name="element_offers" class="oneclick-offers-list">
			<?foreach ($arResult["ELEMENTS"]["OFFERS"] as $key => $val):?>
				<option value="<?=$val["ID"]?>">
					<?=$val["NAME"]." ".$val["PRICE"]?>
				</option>
			<?endforeach;?>
			</select>
		<?endif;?>
	
		
	</td>
</tr>
<?endif;?>
<?if ($arParams["USE_COUNT"] == "Y" && $component->isShop):?>
<tr>
	<td class="title"><?=GetMessage('SALE_COUNT');?></td>
	<td class="field">
		<input type="text" name="quantity" id="js-popup-quantity" value="1" class="sale-order-count">
		<div class="order-popup-quantity">
			<a href="#" onClick="return changeQuantity('top');" class="order-popup-top"></a>
			<a href="#" onClick="return changeQuantity('down');" class="order-popup-down"></a>
		</div>
		<div class="order-popup-measure">
			(<?=$arResult["ELEMENTS"]["CATALOG_MEASURE_NAME"]?>)
		</div>
	</td>
</tr>
<?endif;?>

<?PrintPropsForm($arResult["PERSON_TYPE_PROPS"]);?>

<?if ($arParams["USE_COMMENT"] == "Y" && $component->isShop):?>
<tr>
	<td class="title"><?=GetMessage('SALE_COMMENT');?></td>
	<td class="field"><textarea name="USER_DESCRIPTION"></textarea></td>
</tr>
<?endif;?>
<?if (!$component->isShop):?>
	<tr>
		<td class="title"><?=GetMessage('SALE_FIO')?></td>
		<td class="field">
			<input type="text" maxlength="250" value="" name="name">
		</td>
	</tr>
	<tr>
		<td class="title"><?=GetMessage('SALE_PHONE')?></td>
		<td class="field">
			<input type="text" maxlength="250" value="" name="phone">
		</td>
	</tr>
	<tr>
		<td class="title">E-mail</td>
		<td class="field">
			<input type="text" maxlength="250" value="<?=$USER->GetEmail()?>" name="mail">
		</td>
	</tr>
<?endif;?>

<?if (!empty($arResult["DELIVERY_LIST"]) && $arParams["DELIVERY_LIST_SHOW"] == "Y"):?>
	<tr>
		<td class="title"><?=GetMessage('SALE_DELIVERY_LIST_TITLE')?></td>
		<td class="field">
			<?foreach ($arResult["DELIVERY_LIST"] as $key => $arDelivery):?>
			<label class="radio-inline">
				<input type="radio" name="deliverylist" value="<?=$arDelivery["ID"]?>" <?=($key == 0)?"checked":"";?>> <?=$arDelivery["NAME"]?>
				<?if ($arDelivery["PRICE"] > 0):?>
					<?=$arDelivery["PRICE_PRINT"]?>
				<?endif;?>
			</label>
			<?endforeach;?>
		</div>
	</div>
<?endif;?>

<?if (!empty($arResult["PAYSYSTEM_LIST"]) && $arParams["PAYSYSTEM_LIST_SHOW"] == "Y"):?>
	<tr>
		
		<td class="title"><?=GetMessage('SALE_PAYSYSTEM_LIST_TITLE')?></td>
		<td class="field">
			<?foreach ($arResult["PAYSYSTEM_LIST"] as $key => $arPaysystem):?>
				<label class="radio-inline">
					<input type="radio" name="paysystemlist" value="<?=$arPaysystem["ID"]?>" <?=($key == 0)?"checked":"";?>> <?=$arPaysystem["NAME"]?>
				</label>
			<?endforeach;?>
		</td>
	</tr>
<?endif;?>

<?if ($arParams["PERSONAL_DATA"] == "Y"):?>
	<div class="popup-personal-data"><?=GetMessage("SALE_PERSONAL_DATA_TEXT")?></div>
<?endif;?>

<?if($arParams["USE_CAPTCHA"] == "Y"):?>
<tr>
	<td>&nbsp;</td>
	<td class="field">
		<table class="captcha">
		<tr>
			<td class="field"><br>
				<div><?=GetMessage("SALE_CAPTCHA")?><span class="required">*</span></div>
				<input type="hidden" id="captcha_sid<?=$arResult["ELEMENTS"]["ID"]?>" name="captcha_sid" value="<?=$arResult["capCode"]?>">
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA" id="captha-img<?=$arResult["ELEMENTS"]["ID"]?>">
				<div class="mf-text"><?=GetMessage("SALE_CAPTCHA_CODE")?></div>
				<input type="text" name="captcha_word" id="captcha_word<?=$arResult["ELEMENTS"]["ID"]?>" class="captha" maxlength="50" value="">

				<input type="button" name="reload-captcha" class="sale-reload-captcha" value="" onClick="reloadCaptcha('<?=$arResult["ELEMENTS"]["ID"]?>');" title="<?=GetMessage('SALE_CAPTHA_TITLE');?>">
			</td>
		</tr>
		</table>
	</td>
</tr>
<?endif;?>
</table>