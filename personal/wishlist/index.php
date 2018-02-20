<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Закладки");
?>

    <div class="board">
        <div class="board-inner">
            <ul class="nav nav-tabs" id="myTab">
                <div class="liner"></div>
                <li>
                    <a href="/personal/" title="" data-original-title="">
                      <span class="round-tabs one">
                              <i class="fa fa-home"></i>
                      </span>
                    </a></li>

                <li><a href="/personal/profile/" title="" data-original-title="Изменить регистрационные данные">
                     <span class="round-tabs two">
                         <i class="fa fa-address-card-o" ></i>
                     </span>
                    </a>
                </li>
                <li><a href="/personal/cart/" title="" data-original-title="Посмотреть содержимое корзины">
                     <span class="round-tabs three">
                          <i class="fa fa-shopping-cart"></i>
                     </span> </a>
                </li>

                <li><a href="/personal/order/" title="" data-original-title="Ознакомиться с состоянием заказов">
                         <span class="round-tabs four">
                              <i class="fa fa-list-ul"></i>
                         </span>
                    </a></li>

                <li class="active"><a href="/personal/wishlist/" title="" data-original-title="Посмотреть список желаний">
                         <span class="round-tabs five">
                              <i class="fa fa-heart-o"></i>
                         </span> </a>
                </li>

            </ul>
        </div>

        <?$APPLICATION->IncludeComponent(
            "tarakud:wish.list",
            "default_new",
            array(
                "AJAX_MODE" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "AJAX_OPTION_HISTORY" => "N",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "Y",
                "AUTH_URL" => "/auth/",
                "BASKET_URL" => "/personal/basket.php",
                "CACHE_GROUPS" => "Y",
                "CACHE_TIME" => "36000000",
                "CACHE_TYPE" => "A",
                "DISPLAY_BOTTOM_PAGER" => "Y",
                "DISPLAY_TOP_PAGER" => "N",
                "ELEMENT_SORT_FIELD" => "ID",
                "ELEMENT_SORT_ORDER" => "asc",
                "IMG_HEIGHT" => "200",
                "IMG_WIDTH" => "200",
                "PAGER_DESC_NUMBERING" => "N",
                "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                "PAGER_SHOW_ALL" => "N",
                "PAGER_SHOW_ALWAYS" => "N",
                "PAGER_TEMPLATE" => "round",
                "PAGER_TITLE" => "Товары",
                "PAGE_ELEMENT_COUNT" => "15",
                "PRICE_CODE" => array(
                    0 => "Розничная цена",
                ),
                "SET_TITLE" => "Y",
                "SOCIAL" => array(
                ),
                "COMPONENT_TEMPLATE" => ".default"
            ),
            false
        );?><br>

    </div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>