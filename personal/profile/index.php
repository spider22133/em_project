<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Настройки пользователя");
?>

    <div class="board short">
        <div class="board-inner">
            <ul class="nav nav-tabs" id="myTab">
                <div class="liner"></div>
                <li>
                    <a href="/personal/" title="" data-original-title="">
                      <span class="round-tabs one">
                              <i class="fa fa-home"></i>
                      </span>
                    </a></li>

                <li class="active"><a href="/personal/profile/" title="" data-original-title="Изменить регистрационные данные">
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

                <li><a href="/personal/wishlist/" title="" data-original-title="Посмотреть список желаний">
                         <span class="round-tabs five">
                              <i class="fa fa-heart-o"></i>
                         </span> </a>
                </li>

            </ul>
        </div>

    </div>


<?$APPLICATION->IncludeComponent(
	"bitrix:main.profile", 
	"eshop", 
	array(
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CHECK_RIGHTS" => "N",
		"COMPONENT_TEMPLATE" => "eshop",
		"SEND_INFO" => "N",
		"SET_TITLE" => "Y",
		"USER_PROPERTY" => array(
		),
		"USER_PROPERTY_NAME" => ""
	),
	false
);?><br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>