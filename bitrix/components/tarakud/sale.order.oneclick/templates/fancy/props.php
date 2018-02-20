<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (!defined("SALE_PROPS"))
{
	function PrintPropsForm($arSource = array())
	{
		foreach($arSource as $arProperties)
		{
			?>
			<div class="form-group">
				<label for="<?=$arProperties["FIELD_NAME"]?>">
					<?if($arProperties["REQUIED"]==="Y"):?>
						<span class="required">*</span>
					<?endif;?>
					<?=$arProperties["NAME"]?>
				</label>
				
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
					<input type="text" maxlength="250" size="<?=$arProperties["SIZE1"]?>" value="<?=$arProperties["VALUE"]?>" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" class="form-control">
					<?
				}
				elseif($arProperties["TYPE"] == "SELECT")
				{
					?>
					<select name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>" class="form-control">
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
					<select multiple name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>" class="form-control">
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
					<textarea rows="<?=$arProperties["SIZE2"]?>" cols="<?=$arProperties["SIZE1"]?>" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" class="form-control"><?=$arProperties["VALUE"]?></textarea>
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
					echo "<div class='popup-location'>";
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
					echo '</div>';
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

define("SALE_PROPS", "Y");
?>

<?if ($arParams["USE_COUNT"] == "Y" && $component->isShop):?>
	<div class="form-group">
		<label for="popup-quantity"><?=GetMessage('SALE_COUNT');?></label>
		<div class="clearfix"></div>
		<input type="text" name="quantity" id="popup-quantity" value="1" class="popup-quantity form-control js-popup-quantity">
		<div class="popup-quantity-calc">
			<a href="#" onClick="return changeQuantity('top');" class="popup-quantity-calc-top"></a>
			<a href="#" onClick="return changeQuantity('down');" class="popup-quantity-calc-down"></a>
		</div>
		<div class="popup-quantity-measure">
			(<?=$arResult["ELEMENTS"]["CATALOG_MEASURE_NAME"]?>)
		</div>
		<div class="clearfix"></div>
	</div>
<?endif;?>

<?PrintPropsForm($arResult["PERSON_TYPE_PROPS"]);?>

<?if ($arParams["USE_COMMENT"] == "Y" && $component->isShop):?>
	<div class="form-group">
		<label for="popup-comment"><?=GetMessage('SALE_COMMENT');?></label>
		<textarea name="USER_DESCRIPTION" class="form-control" id="popup-comment"></textarea>
	</div>
<?endif;?>

<?if (!$component->isShop):?>
	<div class="form-group">
		<label for="popup-fio"><?=GetMessage('SALE_FIO')?></label>
		<input type="text" maxlength="250" value="" name="name" class="form-control" id="popup-fio">
	</div>
	<div class="form-group">
		<label for="popup-phone"><?=GetMessage('SALE_PHONE')?></label>
		<input type="text" maxlength="250" value="" name="phone" class="form-control" id="popup-phone">
	</div>
	<div class="form-group">
		<label for="popup-mail">E-mail</label>
		<input type="text" maxlength="250" value="<?=$USER->GetEmail()?>" name="mail" class="form-control" id="popup-mail">
	</div>
<?endif;?>

<?if (!empty($arResult["DELIVERY_LIST"]) && $arParams["DELIVERY_LIST_SHOW"] == "Y"):?>
	<div class="form-group">
		<label class="popup-label"><?=GetMessage('SALE_DELIVERY_LIST_TITLE')?></label>
	
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
		<label class="popup-label"><?=GetMessage('SALE_PAYSYSTEM_LIST_TITLE')?></label>
	
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
		<label for="captcha_word<?=$arParams["PRODUCT_ID"]?>"><span class="required">*</span><?=GetMessage("SALE_CAPTCHA")?></label>
		<div>
			<input type="hidden" id="captcha_sid<?=$arParams["PRODUCT_ID"]?>" name="captcha_sid" value="<?=$arResult["capCode"]?>">
			<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA" id="captha-img<?=$arParams["PRODUCT_ID"]?>">
			<div class="mf-text"><?=GetMessage("SALE_CAPTCHA_CODE")?></div>
			<input type="text" name="captcha_word" id="captcha_word<?=$arParams["PRODUCT_ID"]?>" class="form-control popup-captha pull-left" maxlength="50" value="">

			<input type="button" name="reload-captcha" class="popup-reload-captcha pull-left" value="" onClick="reloadCaptcha('<?=$arParams["PRODUCT_ID"]?>');" title="<?=GetMessage('SALE_CAPTHA_TITLE');?>">
		</div>
	</div>
<?endif;?>