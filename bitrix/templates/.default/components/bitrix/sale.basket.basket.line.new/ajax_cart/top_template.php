<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();?>

<div class="bx-hdr-profile">
	

	<div class="bx-basket-block">
		<?if ($arParams['SHOW_NUM_PRODUCTS'] == 'Y' && ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] == 'Y')):?>
			<?=$arResult['NUM_PRODUCTS'].' '.$arResult['PRODUCT(S)']?>
		<?endif?>
		<?if ($arParams['SHOW_TOTAL_PRICE'] == 'Y'):?>
			<br <?if ($arParams['POSITION_FIXED'] == 'Y'):?>class="hidden-xs"<?endif?>/>
			<span>
				<?=GetMessage('TSB1_TOTAL_PRICE')?>
				<?if ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] == 'Y'):?>
					<strong><?=$arResult['TOTAL_PRICE']?></strong>
				<?endif?>
			</span>
		<?endif?>
		<?if ($arParams['SHOW_PERSONAL_LINK'] == 'Y'):?>
			<br>
			<span class="icon_info"></span>
			<a href="<?=$arParams['PATH_TO_PERSONAL']?>"><?=GetMessage('TSB1_PERSONAL')?></a>
		<?endif?>
	</div>
</div>
