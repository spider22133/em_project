<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Персональный раздел");


/**
 * Bitrix vars
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $this
 */
?>
    <div class="board">
        <div class="board-inner">
            <ul class="nav nav-tabs">
                <div class="liner"></div>
                <li class="active">
                    <a href="/personal/" title="" data-original-title="">
                      <span class="round-tabs one">
                              <i class="fa fa-home"></i>
                      </span>
                    </a></li>

                <li><a href="/personal/profile/" title="" data-original-title="Изменить регистрационные данные">
                     <span class="round-tabs two">
                         <i class="fa fa-address-card-o"></i>
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

        <div class="text_info text-center"><p>В личном кабинете Вы можете проверить текущее состояние корзины, ход выполнения Ваших заказов, просмотреть или изменить личную информацию,
                а также подписаться на новости и другие информационные рассылки.</p></div>
        <div class="sign_p">
        <? if (!$USER->IsAuthorized()) { ?>
            <a class="" href="/personal/profile/">Войдите в личный кабинет <i class="fa fa-sign-in" aria-hidden="true"></i></a>
            <?
        } else { ?>
           <a  href="<?= $APPLICATION->GetCurPageParam("logout=yes", array(
                    "login",
                    "logout",
                    "register",
                    "forgot_password",
                    "change_password")) ?>" class="">Выход <i class="fa fa-sign-out" aria-hidden="true"></i></a>
        <? } ?>
        </div>

    </div>

    <br><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>