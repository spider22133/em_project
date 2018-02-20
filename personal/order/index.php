<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Заказы");
?><div class="board short">
	<div class="board-inner">
		<ul class="nav nav-tabs" id="myTab">
			<div class="liner">
			</div>
			<li> <a href="/personal/" title="" data-original-title=""> <span class="round-tabs one"> <i class="fa fa-home"></i> </span> </a></li>
			<li><a href="/personal/profile/" title="" data-original-title="Изменить регистрационные данные"> <span class="round-tabs two"> <i class="fa fa-address-card-o"></i> </span> </a> </li>
			<li><a href="/personal/cart/" title="" data-original-title="Посмотреть содержимое корзины"> <span class="round-tabs three"> <i class="fa fa-shopping-cart"></i> </span> </a> </li>
			<li class="active"><a href="/personal/order/" title="" data-original-title="Ознакомиться с состоянием заказов"> <span class="round-tabs four"> <i class="fa fa-list-ul"></i> </span> </a></li>
			<li><a href="/personal/wishlist/" title="" data-original-title="Посмотреть список желаний"> <span class="round-tabs five"> <i class="fa fa-heart-o"></i> </span> </a> </li>
		</ul>
	</div>
</div>
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.order",
	"order_gem",
	Array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"COMPONENT_TEMPLATE" => "order_gem",
		"CUSTOM_SELECT_PROPS" => array(),
		"HISTORIC_STATUSES" => array(0=>"F",),
		"NAV_TEMPLATE" => "arrows",
		"ORDERS_PER_PAGE" => "10",
		"PATH_TO_BASKET" => "/personal/cart/",
		"PATH_TO_PAYMENT" => "/personal/order/payment/",
		"PROP_1" => array(),
		"SAVE_IN_SESSION" => "N",
		"SEF_FOLDER" => "/personal/order/",
		"SEF_MODE" => "Y",
		"SEF_URL_TEMPLATES" => array("list"=>"index.php","detail"=>"detail/#ID#/","cancel"=>"cancel/#ID#/",),
		"SET_TITLE" => "Y",
		"SHOW_ACCOUNT_NUMBER" => "Y",
		"STATUS_COLOR_F" => "gray",
		"STATUS_COLOR_N" => "green",
		"STATUS_COLOR_P" => "yellow",
		"STATUS_COLOR_PSEUDO_CANCELLED" => "red",
		"STATUS_COLOR_S" => "gray"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>