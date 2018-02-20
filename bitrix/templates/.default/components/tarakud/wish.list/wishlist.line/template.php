<?
use Bitrix\Main\Localization\Loc;
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$this->addExternalCss("/bitrix/css/main/bootstrap.min.css");
$this->addExternalCss("/bitrix/css/main/font-awesome.min.css");

$count = count($arResult["ITEMS"]);
?>

	<a class="img-replace" href="<?=$arParams["WISHLIST_URL"]?>">Закладки
		<?if ($count == 0):?>
			<span class="disabled"></span>
		<?else:?>
			<span class="xhr">

							<?=$count?>

					</span>
		<?endif;?>
	</a>


