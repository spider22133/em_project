<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="h2">Новости</div>
<!-- <p class="searchform">Получайте самые последние<br/> обновления на нашем сайте!</p> -->
<div class="bx-subscribe">
	<?$APPLICATION->IncludeComponent("bitrix:sender.subscribe", "sender", array(
		"SET_TITLE" => "N"
	));?>
</div>
