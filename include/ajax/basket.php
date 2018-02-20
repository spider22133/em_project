<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
?>

<?
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");
/* Addition of the goods in a basket at addition in a basket */
if($_POST["ajaxaddid"] && $_POST["ajaxaction"] == 'add'){
    Add2BasketByProductID($_POST["ajaxaddid"], 1, array());
}
/* Goods removal at pressing on to remove in a small basket */
if($_POST["ajaxdeleteid"] && $_POST["ajaxaction"] == 'delete'){
    CSaleBasket::Delete($_POST["ajaxdeleteid"]);
}
/* Changes of quantity of the goods after receipt of inquiry from a small basket */
if($_POST["ajaxbasketcountid"] && $_POST["ajaxbasketcount"] && $_POST["ajaxaction"] == 'update'){
    $arFields = array(
       "QUANTITY" => $_POST["ajaxbasketcount"]
    );
    CSaleBasket::Update($_POST["ajaxbasketcountid"], $arFields);
}

?>

<?$APPLICATION->IncludeComponent(
    "bitrix:sale.basket.basket.small",
    "ajax_cart",
    Array(
        "PATH_TO_BASKET" => "/personal/cart/",
        "PATH_TO_ORDER" => "/personal/order/"
    )
);?>