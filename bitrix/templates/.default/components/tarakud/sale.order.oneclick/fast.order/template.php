<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>

<div id="oneclick-popup<?=$arResult["UNIQUE_CODE"]?>" class="oneclick-popup">
	<form action="<?=$APPLICATION->GetCurPage()?>" name="oneclick-form<?=$arResult["ELEMENTS"]["ID"]?>" id="oneclick-form<?=$arResult["UNIQUE_CODE"]?>" method="post">
	<input type="hidden" name="ajax" value="Y">
	<input type="hidden" name="ajax_params" value='<?=$arResult["PARAMS"]?>'>
	<input type="hidden" name="ajax_send" id="ajax_send<?=$arResult["UNIQUE_CODE"]?>" value="N">
	<input type="hidden" name="site_id" value="<?=SITE_ID?>">

	<div id="sale-error<?=$arResult["UNIQUE_CODE"]?>"></div>

	<table class="order-popup-table">
		<?if (!empty($arResult["ELEMENTS"])):?>
			<tr>
				<td colspan="2" class="order-popup-title"><?= html_entity_decode($arResult["ELEMENTS"]["NAME"])?></td>
			</tr>
		<?endif;?>
		<tr>
			<?if (!empty($arResult["ELEMENTS"])):?>
				<td class="oneclick-elements">
					<?if (strlen($arResult["ELEMENTS"]["RESIZE_PICTURE"]["src"]) > 0):?>
						<img src="<?=$arResult["ELEMENTS"]["RESIZE_PICTURE"]["src"]?>" alt="">
					<?else:?>
						<img src="<?=$templateFolder?>/images/no_photo.gif" alt="">
					<?endif;?>

					<?if (!empty($arResult["DELIVERY"])):?>
						<div class="order-popup-price">
							<?=GetMessage("SALE_PRICE_DELIVERY")?>:
							<span><?=$arResult["DELIVERY"]["PRICE_PRINT"]?></span>
						</div>
					<?endif;?>
				</td>
			<?endif;?>
			<td>
                <div class="order-popup-price">
                    <?if (is_array($arResult["ELEMENTS"]["PRICES_MIN_OFFERS"])
                        && strlen($arResult["ELEMENTS"]["PRICES_MIN_OFFERS"]["VALUE"]) > 0):?>
                        <?=GetMessage("SALE_PRICE_FROM")?>:
                        <span><?=$arResult["ELEMENTS"]["PRICES_MIN_OFFERS"]["PRINT_DISCOUNT_VALUE"]?></span>
                    <?elseif (is_array($arResult["ELEMENTS"]["PRICES"])):?>
                        <?/*=GetMessage("SALE_PRICE")*/?>
                        <span>
							<?if (!empty($arResult["ELEMENTS"]["PRICES"]["PRICE"]["PRINT_DISCOUNT_VALUE"])):?>
                                <?=$arResult["ELEMENTS"]["PRICES"]["PRICE"]["PRINT_DISCOUNT_VALUE"]?>
                            <?else:?>
                                <?=$arResult["ELEMENTS"]["PRICES"]["PRICE"]["PRINT_PRICE"]?>
                            <?endif;?>
							</span>
                    <?endif;?>
                </div>
				<?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props.php");?>
			</td>
		</tr>
	</table>
	</form>
</div>

<?if ($arResult["HIDE_BUTTON"] != "Y"):?>
    <a title="<?=GetMessage("SALE_BTN")?>"><input class="btn btn-fast-order" type="submit" name="oneclickOrder" value="<?=GetMessage("SALE_BTN")?>"
	onClick="oneclickPopup(
	'<?=$arParams["TITLE_POPUP"];?>',
	'<?=GetMessage('SALE_BTN_ORDER');?>',
	'<?=GetMessage('SALE_BTN_CLOSE')?>',
	'<?=$arResult["AJAX_URL"]?>',
	'<?=$arResult["UNIQUE_CODE"]?>',
	'<?=$arParams["CONFIRM_ORDER"]?>'
	);"></a>
<?endif;?>