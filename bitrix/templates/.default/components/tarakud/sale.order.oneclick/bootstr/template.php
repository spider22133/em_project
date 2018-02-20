<?
use Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>

<?if ($arResult["HIDE_BUTTON"] != "Y"):?>
	<a class="btn btn-fast-order js-popup-btn-form" data-toggle="modal" data-target="#oneclick" href="#popup-window<?=$arResult["UNIQUE_CODE"]?>"><?=Loc::getMessage("SALE_BTN")?></a>
<?endif;?>

<div class="modal fade" id="oneclick" tabindex="-1" role="dialog" aria-labelledby="oneclick" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">
					<?if (!empty($arParams["TITLE_POPUP"])):?>
						<?=$arParams["TITLE_POPUP"]?>
					<?else:?>
						<?=Loc::getMessage("SALE_TITLE")?>
					<?endif;?>
				</h4>
			</div>
			<div class="modal-body js-modal-body<?=$arResult["UNIQUE_CODE"]?>">
				
				<div class="alert alert-warning js-popup-error<?=$arResult["UNIQUE_CODE"]?>"></div>
				
				<form class="popup-form" action="<?=$APPLICATION->GetCurPage()?>" name="popup-form<?=$arResult["UNIQUE_CODE"]?>" id="popup-form<?=$arResult["UNIQUE_CODE"]?>" method="post" role="form">
					<input type="hidden" name="ajax" value="Y">
					<input type="hidden" name="ajax_params" value='<?=$arResult["PARAMS"]?>'>
					<input type="hidden" name="site_id" value="<?=SITE_ID?>">
					
					<?if (!empty($arResult["ELEMENTS"]) && $arParams["NAME_SHOW"] == "Y"):?>
						<div class="row">
							<?if (strlen($arResult["ELEMENTS"]["RESIZE_PICTURE"]["src"]) > 0):?>
							<div class="col-md-3 popup-pic">
								<img class="img-responsive" src="<?=$arResult["ELEMENTS"]["RESIZE_PICTURE"]["src"]?>" alt="">
							</div>
							<?endif;?>
							<div class="col-md-9 popup-element">
								<?=$arResult["ELEMENTS"]["NAME"]?>
								<div class="popup-price">
									<?if (is_array($arResult["ELEMENTS"]["PRICES_MIN_OFFERS"])
									&& strlen($arResult["ELEMENTS"]["PRICES_MIN_OFFERS"]["VALUE"]) > 0):?>
										<?=Loc::getMessage("SALE_PRICE_FROM")?>:
										<span class="js-price"><?=$arResult["ELEMENTS"]["PRICES_MIN_OFFERS"]["PRINT_DISCOUNT_VALUE"]?></span>
									<?elseif (is_array($arResult["ELEMENTS"]["PRICES"])):?>
										<?=Loc::getMessage("SALE_PRICE")?>:
										<span>
										<?if (!empty($arResult["ELEMENTS"]["PRICES"]["PRICE"]["PRINT_DISCOUNT_VALUE"])):?>
											<?=$arResult["ELEMENTS"]["PRICES"]["PRICE"]["PRINT_DISCOUNT_VALUE"]?>
										<?else:?>
											<?=$arResult["ELEMENTS"]["PRICES"]["PRICE"]["PRINT_PRICE"]?>
										<?endif;?>
										</span>
									<?endif;?>
								</div>
								<?if (!empty($arResult["DELIVERY"])):?>
									<div class="popup-price">
										<?=GetMessage("SALE_PRICE_DELIVERY")?>:
										<span><?=$arResult["DELIVERY"]["PRICE_PRINT"]?></span>
									</div>
								<?endif;?>
								<br>
							</div>
						</div>
					<?endif;?>
					
					<?if (!empty($arResult["ELEMENTS"]["OFFERS"])):
						$arPriceOffer = array();
					?>
						<div class="form-group">
							<?if ($arParams["OFFERS_SHOW"] == "all"):?>
								<?$key = 0;?>
								<?foreach ($arResult["ELEMENTS"]["OFFERS"] as $val):?>
									<div class="radio">
										<label class="js-offer-all">
											<input type="radio" name="element_offers" value="<?=$val["ID"]?>" <?=($key == 0)?"checked":"";?>>
											<?=$val["NAME"]?>
											<?if (!empty($val["DISPLAY_PROPERTIES"])):?>
												<?foreach ($val["DISPLAY_PROPERTIES"] as $item):?>
													<div class="popup-offers-props"><?=$item["NAME"]?>: <?=$item["DISPLAY_VALUE"]?></div>
												<?endforeach;?>
											<?endif;?>
											<div><?=Loc::getMessage("SALE_PRICE")?>: <?=$val["PRICE"]?></div>
										</label>
									</div>
									<?$key = 1;?>
									<?$arPriceOffer[$val["ID"]] = $val["PRICE"];?>
								<?endforeach;?>
							<?endif;?>
							<?if ($arParams["OFFERS_SHOW"] == "list"):?>
								<label for="popup-quantity"><span class="popup-required">*</span> <?=Loc::getMessage('SALE_SKU');?></label>
								<select name="element_offers" class="form-control js-offer" autofocus>
								<?foreach ($arResult["ELEMENTS"]["OFFERS"] as $key => $val):?>
									<option value="<?=$val["ID"]?>">
										<?=$val["NAME"]." ".$val["PRICE"]?>
									</option>
									<?$arPriceOffer[$val["ID"]] = $val["PRICE"];?>
								<?endforeach;?>
								</select>
							<?endif;?>
							<script>
								var arPriceOffer = <?=CUtil::PhpToJSObject($arPriceOffer)?>;
							</script>
						</div>
					<?endif;?>
					
					<?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props.php");?>
				</form>
			</div>
			<div class="modal-footer js-modal-footer">
				<button class="js-popup-order btn btn-primary" type="button" data-id="<?=$arResult["UNIQUE_CODE"]?>"><?=Loc::getMessage('SALE_BTN_ORDER')?></button>
			</div>
		</div>
	</div>
</div>

<?
$dir = str_replace('\\', '/', __DIR__);
include($dir."/lang/".LANGUAGE_ID."/template.php");
if (!empty($arParams["CONFIRM_ORDER"]))
	$MESS["SALE_ORDER"] = $arParams["CONFIRM_ORDER"];
?>
<script type="text/javascript">BX.message(<?=CUtil::PhpToJsObject($MESS)?>);</script>