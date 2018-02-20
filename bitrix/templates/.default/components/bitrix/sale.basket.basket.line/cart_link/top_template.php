<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();?>

<a href="<?=$arParams['PATH_TO_BASKET']?>" class="img-replace"><?=GetMessage('TSB1_CART')?>
	<?if ($arResult['NUM_PRODUCTS'] == 0):?>
		<span class="disabled"></span>
	<?else:?>
		<span class="xhr">
						<?if ($arParams['SHOW_NUM_PRODUCTS'] == 'Y' && ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] == 'Y')):?>
							<?=$arResult['NUM_PRODUCTS']?>
						<?endif?>
					</span>
	<?endif;?>
</a>

<div id="cart">

	<?if($arResult['NUM_PRODUCTS'] > 0):?>

		<div class="popup-cart-full">
			<ul class="cart-items">
				<li>
					В корзине
				<span>
					<?if ($arParams['SHOW_NUM_PRODUCTS'] == 'Y' && ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] == 'Y')):?>
						<?=$arResult['NUM_PRODUCTS'].' '.$arResult['PRODUCT(S)']?>
					<?endif?>
				</span>
					<?if ($arParams['SHOW_TOTAL_PRICE'] == 'Y'):?>
						<div class="price">
				<span class="qty"><?=GetMessage('TSB1_TOTAL_PRICE')?>
					<?if ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] == 'Y'):?>
						<strong><?=$arResult['TOTAL_PRICE']?></strong>
					<?endif?>
				</span>
						</div>
					<?endif?>
				</li>
			</ul>


			<a href="<?=$arParams["PATH_TO_ORDER"]?>" class="checkout-btn"><?=GetMessage("TSB1_2ORDER")?></a>

			<p class="go-to-cart"><i class="fa fa-shopping-cart"></i><a href="<?=$arParams['PATH_TO_BASKET']?>"><?=GetMessage('TSB1_TO_CART')?></a></p>



		</div>


		<?
	else:
		?>

		<div  class="popup-cart-empty">
			<p class="popup-info-title">Ваша корзина пуста</p>
		</div>

	<?endif?>

</div>
