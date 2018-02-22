<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
IncludeTemplateLangFile($_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/" . SITE_TEMPLATE_ID . "/header.php");
CJSCore::Init(array("fx"));
$curPage = $APPLICATION->GetCurPage(true);
$curPagePurch = $APPLICATION->GetCurPageParam();

$isIndexPage = preg_match("~^" . SITE_DIR . "index.php~", $curPage);
$isCatalogPage = preg_match("~^" . SITE_DIR . "catalog/~", $curPage);
$isCartPage = preg_match("~^/personal/~", $curPage);

$theme = COption::GetOptionString("main", "wizard_eshop_bootstrap_theme_id", "blue", SITE_ID);
?>
<!DOCTYPE html>
<html xml:lang="<?= LANGUAGE_ID ?>" lang="<?= LANGUAGE_ID ?>">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">
    <title><? $APPLICATION->ShowTitle() ?></title>

   <?php //$APPLICATION->ShowLink("canonical", null, true); ?>

    <?php $APPLICATION->ShowProperty('og-title'); ?>
    <?php $APPLICATION->ShowProperty('og-description'); ?>
    <?php $APPLICATION->ShowProperty('og-image'); ?>
    <?php $APPLICATION->ShowProperty('og-image-type'); ?>
    <?php $APPLICATION->ShowProperty('og-image-width'); ?>
    <?php $APPLICATION->ShowProperty('og-image-height'); ?>

    <link rel="shortcut icon" type="image/x-icon" href="<?= SITE_DIR ?>favicon.ico"/>

    <?php $APPLICATION->ShowHead(); ?>
    <?php
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/colors.css");
    $APPLICATION->SetAdditionalCSS("/bitrix/css/main/bootstrap.css");
    $APPLICATION->SetAdditionalCSS("/bitrix/css/main/font-awesome.css");
    $APPLICATION->SetAdditionalCSS("/bitrix/css/main/ionicons.min.css");
    $APPLICATION->SetAdditionalCSS("/bitrix/css/main/megamenu.css");
    $APPLICATION->SetAdditionalCSS("/bitrix/css/main/menu.css");
    $APPLICATION->SetAdditionalCSS("/bitrix/css/main/selectic.css");

//
//    $APPLICATION->ShowHeadScripts('/bitrix/templates/eshop_bootstrap_red/js/bootstrap.min.js');
//    $APPLICATION->ShowHeadScripts('/bitrix/templates/eshop_bootstrap_red/js/modernizr-2.8.3.min.js');
//    $APPLICATION->ShowHeadScripts('/bitrix/templates/eshop_bootstrap_red/js/cart_vs_topmenu.js');
//    $APPLICATION->ShowHeadScripts('/bitrix/templates/eshop_bootstrap_red/js/gem-m-m.min.js');
//    $APPLICATION->ShowHeadScripts('/bitrix/templates/eshop_bootstrap_red/js/megamenu.min.js');
//    $APPLICATION->ShowHeadScripts('/bitrix/templates/eshop_bootstrap_red/js/signup.js');
//    $APPLICATION->ShowHeadScripts('/bitrix/templates/eshop_bootstrap_red/js/jquery.hoverIntent.min.js');
//    $APPLICATION->ShowHeadScripts('/bitrix/templates/eshop_bootstrap_red/js/move-top.js');
//    $APPLICATION->ShowHeadScripts('/bitrix/templates/eshop_bootstrap_red/js/easing.js');
//    $APPLICATION->ShowHeadScripts('/bitrix/templates/eshop_bootstrap_red/js/jquery.selectric.min.js');
//    $APPLICATION->ShowHeadScripts('/bitrix/templates/eshop_bootstrap_red/js/jquery.accordion.min.js');
//    $APPLICATION->ShowHeadScripts('/bitrix/templates/eshop_bootstrap_red/js/jquery.easing.1.3.min.js');
    ?>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,300,100,400italic&amp;subset=latin,cyrillic"
          rel="stylesheet" type="text/css">

    <script language="javascript" src="/bitrix/templates/eshop_bootstrap_red/js/bootstrap.min.js"></script>
    <script language="javascript" src="/bitrix/templates/eshop_bootstrap_red/js/modernizr-2.8.3.min.js"></script>
    <script language="javascript" src="/bitrix/templates/eshop_bootstrap_red/js/cart_vs_topmenu.js"></script>
    <script language="javascript" src="/bitrix/templates/eshop_bootstrap_red/js/gem-m-m.min.js"></script>
    <script language="javascript" src="/bitrix/templates/eshop_bootstrap_red/js/megamenu.min.js"></script>
    <script language="javascript" src="/bitrix/templates/eshop_bootstrap_red/js/signup.js"></script>
    <script language="javascript" src="/bitrix/templates/eshop_bootstrap_red/js/jquery.hoverIntent.min.js"></script>
    <script language="javascript" src="/bitrix/templates/eshop_bootstrap_red/js/move-top.js"></script>
    <script language="javascript" src="/bitrix/templates/eshop_bootstrap_red/js/easing.js"></script>
    <script language="javascript" src="/bitrix/templates/eshop_bootstrap_red/js/jquery.selectric.min.js"></script>
    <script language="javascript" src="/bitrix/templates/eshop_bootstrap_red/js/jquery.accordion.min.js"></script>
    <script language="javascript" src="/bitrix/templates/eshop_bootstrap_red/js/jquery.easing.1.3.min.js"></script>
    <script language="javascript" src="/bitrix/templates/eshop_bootstrap_red/js/instafeed.min.js"></script>


    <script>
        jQuery(document).ready(function ($) {
            $(".scroll").click(function (event) {
                event.preventDefault();
                $('html,body').animate({scrollTop: $(this.hash).offset().top}, 1200);
            });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function () {

            var defaults = {
                containerID: 'toTop', // fading element id
                containerHoverID: 'toTopHover', // fading element hover id
                scrollSpeed: 1200,
                easingType: 'linear'
            };


            $().UItoTop({easingType: 'easeOutQuart'});

        });
    </script>

    <!-- Global Site Tag (gtag.js) - Google Analytics -->
    <script async data-skip-moving="true" src="https://www.googletagmanager.com/gtag/js?id=UA-101713361-1"></script>

    <script data-skip-moving="true">
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments)
        }
        gtag('js', new Date());

        gtag('config', 'UA-101713361-1');
    </script>

    <script data-skip-moving="true">
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '926016264224162');
        fbq('track', 'PageView');
    </script>
    <script data-skip-moving="true" charset="UTF-8" src="//cdn.sendpulse.com/9dae6d62c816560a842268bde2cd317d/js/push/db48b7c6a088719fbd1b257c6296f61f_1.js" async></script>

</head>
<body class="bx-background-image bx-<?= $theme ?>">
<div id="fb-root"></div>
<script>(function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/ru_RU/sdk.js#xfbml=1&version=v2.9&appId=353258328386429";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
<div id="panel"><? $APPLICATION->ShowPanel(); ?></div>

<div class="bx-wrapper" id="bx_eshop_wrap">


    <!---------- HEADER ----------->
    <header>
        <div class="header-middle">
            <div class="header-top backcolor">
                <div class="container">
                    <div class="row">
                        <div class="img_right"><img src="<?= SITE_TEMPLATE_PATH ?>/img/right-flower.png" alt=""/></div>
                        <div class="img_left"><img src="<?= SITE_TEMPLATE_PATH ?>/img/left-flower.png" alt=""/></div>
                        <div class="col-sm-8 col-md-8">
                            <div class="row top-nav-desc">
                                <nav id="top-nav">
                                    <? $APPLICATION->IncludeComponent(
                                        "bitrix:menu",
                                        "top_menu",
                                        array(
                                            "ALLOW_MULTI_SELECT" => "N",
                                            "CHILD_MENU_TYPE" => "",
                                            "COMPONENT_TEMPLATE" => "top_menu",
                                            "DELAY" => "N",
                                            "MAX_LEVEL" => "1",
                                            "MENU_CACHE_GET_VARS" => array(),
                                            "MENU_CACHE_TIME" => "3600",
                                            "MENU_CACHE_TYPE" => "N",
                                            "MENU_CACHE_USE_GROUPS" => "Y",
                                            "ROOT_MENU_TYPE" => "top",
                                            "USE_EXT" => "N"
                                        ),
                                        false
                                    ); ?>
                                </nav>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 pull-right-sm">
                            <div id="cart-trigger">
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:sale.basket.basket.line",
                                    "cart_link",
                                    Array(
                                        "HIDE_ON_BASKET_PAGES" => "N",
                                        "PATH_TO_BASKET" => SITE_DIR . "personal/cart/",
                                        "PATH_TO_ORDER" => SITE_DIR . "personal/order/make/",
                                        "PATH_TO_PERSONAL" => SITE_DIR . "personal/",
                                        "PATH_TO_PROFILE" => SITE_DIR . "personal/",
                                        "PATH_TO_REGISTER" => SITE_DIR . "login/",
                                        "POSITION_FIXED" => "N",
                                        "SHOW_AUTHOR" => "N",
                                        "SHOW_EMPTY_VALUES" => "Y",
                                        "SHOW_NUM_PRODUCTS" => "Y",
                                        "SHOW_PERSONAL_LINK" => "N",
                                        "SHOW_PRODUCTS" => "N",
                                        "SHOW_TOTAL_PRICE" => "Y"
                                    )
                                ); ?>
                            </div>

                            <div id="wishlist-trigger">
                                <? $APPLICATION->IncludeComponent(
	"tarakud:wish.list",
	"wishlist.line",
	array(
		"AUTH_URL" => "/auth/",
		"BASKET_URL" => "/personal/basket.php",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"ELEMENT_SORT_FIELD" => "ID",
		"ELEMENT_SORT_ORDER" => "asc",
		"IMG_HEIGHT" => "100",
		"IMG_WIDTH" => "100",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "Y",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Товары",
		"PAGE_ELEMENT_COUNT" => "3",
		"PRICE_CODE" => array(
			0 => "Розничная цена",
		),
		"SET_TITLE" => "N",
		"SOCIAL" => array(
		),
		"COMPONENT_TEMPLATE" => "wishlist.line",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "undefined"
	),
	false
); ?>
                            </div>
                            <ul class="user-nav">

                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:system.auth.form",
                                    "link",
                                    Array(
                                        "COMPONENT_TEMPLATE" => "link",
                                        "FORGOT_PASSWORD_URL" => "/login/",
                                        "PROFILE_URL" => "/personal/",
                                        "REGISTER_URL" => "/login/",
                                        "SHOW_ERRORS" => "N"
                                    )
                                ); ?>
                            </ul>


                        </div>

                        <div id="hamburger-menu"><a class="img-replace" href="#0">Меню</a></div>
                        <a class="logo-m"
                           href="<?= SITE_DIR ?>"><? $APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/company_logo_mobile.php"), false); ?></a>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-sm-6 col-md-4 hidden-xs">
                        <div class="row"><a id="logo" href="<?= SITE_DIR ?>">
                                <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/company_logo.php"), false); ?>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        <div class="contactinfo">
                            <ul>
                                <li>
                                    <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/telephone.php"), false); ?>
                                </li>
                            </ul>
                        </div>
                        <div id="search">
                            <div class="catalog-search">
                                <? $APPLICATION->IncludeComponent(
	"bitrix:search.title", 
	"visual", 
	array(
		"SHOW_INPUT" => "Y",
		"INPUT_ID" => "title-search-input",
		"CONTAINER_ID" => "title-search",
		"PRICE_CODE" => array(
			0 => "Розничная цена",
		),
		"PRICE_VAT_INCLUDE" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "150",
		"SHOW_PREVIEW" => "Y",
		"PREVIEW_WIDTH" => "120",
		"PREVIEW_HEIGHT" => "120",
		"CONVERT_CURRENCY" => "Y",
		"CURRENCY_ID" => "UAH",
		"PAGE" => "#SITE_DIR#catalog/",
		"NUM_CATEGORIES" => "2",
		"TOP_COUNT" => "6",
		"ORDER" => "rank",
		"USE_LANGUAGE_GUESS" => "Y",
		"CHECK_DATES" => "Y",
		"SHOW_OTHERS" => "N",
		"CATEGORY_0_TITLE" => GetMessage("SEARCH_GOODS"),
		"CATEGORY_0" => array(
			0 => "iblock_1c_catalog",
		),
		"CATEGORY_0_iblock_news" => array(
			0 => "all",
		),
		"CATEGORY_OTHERS_TITLE" => "Прочее",
		"COMPONENT_TEMPLATE" => "visual",
		"CATEGORY_0_iblock_1c_catalog" => array(
			0 => "52",
		),
		"CATEGORY_1_TITLE" => "Новости",
		"CATEGORY_1" => array(
			0 => "iblock_news",
		),
		"CATEGORY_1_iblock_news" => array(
			0 => "1",
		)
	),
	false
); ?>
                            </div>
                        </div>
                        <div id="lng">
                            <!--li>???<span class="arrow"></span>
                                <ul>
                                    <li><a href="/">???</a></li>
                                </ul>
                            </li-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-bottom" data-spy="affix" data-offset-top="140">
            <div class="container">
                <div class="row">
                    <? $APPLICATION->IncludeComponent(
                        "bitrix:menu",
                        "catalog_horizontal",
                        array(
                            "ALLOW_MULTI_SELECT" => "N",
                            "CHILD_MENU_TYPE" => "1c_catalog",
                            "COMPONENT_TEMPLATE" => "catalog_horizontal",
                            "DELAY" => "N",
                            "MAX_LEVEL" => "3",
                            "MENU_CACHE_GET_VARS" => array(),
                            "MENU_CACHE_TIME" => "3600",
                            "MENU_CACHE_TYPE" => "N",
                            "MENU_CACHE_USE_GROUPS" => "Y",
                            "MENU_THEME" => "site",
                            "ROOT_MENU_TYPE" => "left",
                            "USE_EXT" => "Y"
                        ),
                        false
                    ); ?>
                </div>
            </div>
        </div>



    </header>


    <? if ($curPage != SITE_DIR . "index.php"):
        ; ?>
        <div class="bx-header">
            <div class="container bx-header-section">
                <div class="row">
                    <div class="col-lg-12" id="navigation">
                        <? $APPLICATION->IncludeComponent("bitrix:breadcrumb", "", array(
                            "START_FROM" => "0",
                            "PATH" => "",
                            "SITE_ID" => "s1"
                        ),
                            false,
                            Array('HIDE_ICONS' => 'Y')
                        ); ?>
                    </div>
                </div>
                <?php if(!$isCatalogPage) { ?>
                <h1 class="bx-title dbg_title"><?= $APPLICATION->ShowTitle(false); ?></h1>
                <?php } ?>
            </div>
        </div>
    <? endif ?>

    <? $APPLICATION->IncludeComponent(
        "bitrix:system.auth.form",
        "form",
        array(
            "COMPONENT_TEMPLATE" => "form",
            "FORGOT_PASSWORD_URL" => "/login/",
            "PROFILE_URL" => "/personal/",
            "REGISTER_URL" => "/login/",
            "SHOW_ERRORS" => "N"
        ),
        false,
        array(
            "ACTIVE_COMPONENT" => "Y"
        )
    ); ?>

    <div id="shadow-layer"></div>


    <!---------- END HEADER ----------->


    <div class="workarea">
        <?php
        $isTestPage = preg_match("~^" . SITE_DIR . "test-slider.php~", $curPage);;
        if($isIndexPage) {?>
        <div class="container-fluid">
            <div class="row"><? $APPLICATION->IncludeComponent(
	"bisexpert:owlslider", 
	"slider", 
	array(
		"ADVERT_TYPE" => "MAIN",
		"AUTO_HEIGHT" => "Y",
		"AUTO_PLAY" => "Y",
		"AUTO_PLAY_SPEED" => "5000",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"COMPONENT_TEMPLATE" => "slider",
		"COMPOSITE" => "N",
		"COUNT" => "5",
		"DISABLE_LINK_DEV" => "Y",
		"DRAG_BEFORE_ANIM_FINISH" => "N",
		"ENABLE_JQUERY" => "N",
		"ENABLE_OWL_CSS_AND_JS" => "Y",
		"HEIGHT_RESIZE" => "",
		"IMAGE_CENTER" => "Y",
		"IS_PROPORTIONAL" => "Y",
		"ITEMS_SCALE_UP" => "N",
		"MAIN_TYPE" => "iblock",
		"MOUSE_DRAG" => "N",
		"NAVIGATION" => "Y",
		"NAVIGATION_TYPE" => "arrows",
		"PAGINATION" => "N",
		"PAGINATION_NUMBERS" => "N",
		"PAGINATION_SPEED" => "2000",
		"RANDOM" => "Y",
		"RANDOM_TRANSITION" => "N",
		"RESPONSIVE" => "Y",
		"REWIND_SPEED" => "2000",
		"SCROLL_COUNT" => "1",
		"SHOW_DESCRIPTION_BLOCK" => "Y",
		"SLIDE_SPEED" => "300",
		"SPECIAL_CODE" => "unic",
		"STOP_ON_HOVER" => "N",
		"TOUCH_DRAG" => "N",
		"TRANSITION_TYPE_FOR_ONE_ITEM" => "fade",
		"WIDTH_RESIZE" => "",
		"IBLOCK_TYPE" => "slider",
		"IBLOCK_ID" => "56",
		"LINK_URL_PROPERTY_ID" => "883",
		"TEXT_PROPERTY_ID" => "884",
		"SECTION_ID" => "0",
		"INCLUDE_SUBSECTIONS" => "Y",
		"SORT_FIELD_1" => "name",
		"SORT_DIR_1" => "asc",
		"SORT_FIELD_2" => "",
		"SORT_DIR_2" => "asc",
		"SORT_FIELD_3" => "",
		"SORT_DIR_3" => "asc",
		"NAVIGATION_TEXT_BACK" => "назад",
		"NAVIGATION_TEXT_NEXT" => "вперед"
	),
	false
); ?></div>
        </div>
            <script>
                $(document).ready(function() {
                    $('.owl-wrapper .owl-item').eq(0).addClass('act_animation');
                });
            </script>
        <?php } ?>

        <div class="container bx-content-seection">
            <div class="row">



                <div class="bx-content <?= (($isCatalogPage || $isIndexPage || $isCartPage) ? "col-xs-12" : "col-md-9 col-sm-8") ?>">