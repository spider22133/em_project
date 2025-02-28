<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */
use Bitrix\Main\Loader,
    Bitrix\Main\ModuleManager;

if ($isFilter || $isSidebar):?>
    <div class="col-md-3 col-sm-4 ">
        <? if ($isFilter): ?>
            <div class="bx-sidebar-block">
                <? $APPLICATION->IncludeComponent(
                    "bitrix:catalog.smart.filter",
                    "filter_gem",
                    array(
                        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                        "SECTION_ID" => $arCurSection['ID'],
                        "FILTER_NAME" => $arParams["FILTER_NAME"],
                        "PRICE_CODE" => $arParams["PRICE_CODE"],
                        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                        "CACHE_TIME" => $arParams["CACHE_TIME"],
                        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                        "SAVE_IN_SESSION" => "N",
                        "FILTER_VIEW_MODE" => $arParams["FILTER_VIEW_MODE"],
                        "XML_EXPORT" => "Y",
                        "SECTION_TITLE" => "NAME",
                        "SECTION_DESCRIPTION" => "DESCRIPTION",
                        'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
                        "TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
                        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                        'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                        "SEF_MODE" => $arParams["SEF_MODE"],
                        "SEF_RULE" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["smart_filter"],
                        "SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
                        "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
                    ),
                    $component,
                    array('HIDE_ICONS' => 'Y')
                ); ?>
            </div>
        <?endif;
        if ($isSidebar):?>
            <? $APPLICATION->IncludeComponent(
                "bitrix:main.include",
                "",
                Array(
                    "AREA_FILE_SHOW" => "file",
                    "PATH" => $arParams["SIDEBAR_PATH"],
                    "AREA_FILE_RECURSIVE" => "N",
                    "EDIT_MODE" => "html",
                ),
                false,
                array('HIDE_ICONS' => 'Y')
            ); ?>
        <? endif ?>
    </div>
<? endif ?>
<div class="<?= (($isFilter || $isSidebar) ? "no-img col-md-9 col-sm-8 " : "col-xs-12") ?>">
    <div class="row">
        <div class="col-xs-12">
            <? $APPLICATION->IncludeComponent(
                "bitrix:catalog.section.list",
                "",
                array(
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                    "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                    "COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
                    "TOP_DEPTH" => $arParams["SECTION_TOP_DEPTH"],
                    "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
                    "VIEW_MODE" => $arParams["SECTIONS_VIEW_MODE"],
                    "SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
                    "HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
                    "ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : '')
                ),
                $component,
                array("HIDE_ICONS" => "Y")
            ); ?><?
            if ($arParams["USE_COMPARE"] == "Y") {
                ?><? $APPLICATION->IncludeComponent(
                    "bitrix:catalog.compare.list",
                    "",
                    array(
                        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                        "NAME" => $arParams["COMPARE_NAME"],
                        "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
                        "COMPARE_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["compare"],
                        "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                        "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                        'POSITION_FIXED' => isset($arParams['COMPARE_POSITION_FIXED']) ? $arParams['COMPARE_POSITION_FIXED'] : '',
                        'POSITION' => isset($arParams['COMPARE_POSITION']) ? $arParams['COMPARE_POSITION'] : ''
                    ),
                    $component,
                    array("HIDE_ICONS" => "Y")
                ); ?><?
            }

            if (isset($arParams['USE_COMMON_SETTINGS_BASKET_POPUP']) && $arParams['USE_COMMON_SETTINGS_BASKET_POPUP'] == 'Y')
                $basketAction = (isset($arParams['COMMON_ADD_TO_BASKET_ACTION']) ? $arParams['COMMON_ADD_TO_BASKET_ACTION'] : '');
            else
                $basketAction = (isset($arParams['SECTION_ADD_TO_BASKET_ACTION']) ? $arParams['SECTION_ADD_TO_BASKET_ACTION'] : '');

            $intSectionID = 0;
            ?>


            <div class="sorting-items">


                <div class="row">
                    <div class="col-xs-12" style="margin-bottom: 10px">
                        <form class="form-inline" id="sort">
                            <div class="col-xs-12 col-md-6">
                                <div class="row">
                                    <div class="form-group">
                                        <label for="sort">Сортировать по:</label>
                                        <select class="sort-select" id="sort" name="sort"
                                                onchange="change_url(this.value);">
                                            <option <? if ($_GET["sort"] == "shows"): ?> selected <? endif; ?>
                                                    value="?sort=shows&method=desc">
                                                Популярности
                                            </option>
                                            <option <? if ($_GET["sort"] == "property_CML2_ARTICLE"): ?> selected <? endif; ?>
                                                    value="?sort=property_CML2_ARTICLE&method=desc">
                                                Новизне
                                            </option>
                                            <option <? if ($_GET["sort"] == "NAME"): ?> selected <? endif; ?>
                                                    value="?sort=NAME&method=asc">
                                                Наименованию
                                            </option>
                                            <option <? if ($_GET["sort"] == "catalog_PRICE_1-asc"): ?> selected <? endif; ?>
                                                    value="?sort=catalog_PRICE_1-asc&method=asc">
                                                Возрастанию цены
                                            </option>
                                            <option <? if ($_GET["sort"] == "catalog_PRICE_1-decs"): ?> selected <? endif; ?>
                                                    value="?sort=catalog_PRICE_1-decs&method=desc"
                                            >Убыванию цены
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <script>
                                function change_url(val) {
                                    window.location = val;
                                }

                                $('.sort-select').selectric();
                            </script>


                            <? if ($_GET["sort"] == "property_CML2_ARTICLE" ||
                                $_GET["sort"] == "catalog_PRICE_1-asc" ||
                                $_GET["sort"] == "catalog_PRICE_1-decs" ||
                                $_GET["sort"] == "shows" ||
                                $_GET["sort"] == "NAME"
                            ) {
                                $arParams["ELEMENT_SORT_FIELD"] = $_GET["sort"];
                                $arParams["ELEMENT_SORT_ORDER"] = $_GET["method"];
                            } else {
                                $arParams["ELEMENT_SORT_FIELD"] = 'shows';
                                $arParams["ELEMENT_SORT_ORDER"] = 'desc';
                            } ?>

                            <?php
                            // SORT ITEMS
                            $pageElementCount = $arParams["PAGE_ELEMENT_COUNT"];
                            if (array_key_exists("showBy", $_REQUEST)) {
                                if (intVal($_REQUEST["showBy"]) && in_array(intVal($_REQUEST["showBy"]), array(18, 36, 54, 72))) {
                                    $pageElementCount = intVal($_REQUEST["showBy"]);
                                    $_SESSION["showBy"] = $pageElementCount;
                                } elseif ($_SESSION["showBy"]) {
                                    $pageElementCount = intVal($_SESSION["showBy"]);
                                }
                            }
                            ?>
                            <div class="col-xs-12 col-md-6 it-on-page">
                                <div class="row">
                                    <span class="show_title">Выводить по: </span>
                                    <span class="number_list">
                                    <? for ($i = 18; $i <= 72; $i += 18) : ?>
                                        <a rel="nofollow" <? if ($i == $pageElementCount): ?>class="active"<? endif; ?>
                                           href="<?= $APPLICATION->GetCurPageParam('showBy=' . $i, array('showBy', 'mode')) ?>">
                                            <span><?= $i ?></span>
                                        </a>
                                    <? endfor;
                                    // END SORT ITEMS
                                    ?>
                                </span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <? $intSectionID = $APPLICATION->IncludeComponent(
                    "bitrix:catalog.section",
                    "",
                    array(
                        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                        "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
                        "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
                        "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
                        "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
                        "PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
                        "META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
                        "META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
                        "BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
                        "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
                        "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
                        "BASKET_URL" => $arParams["BASKET_URL"],
                        "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                        "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                        "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
                        "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                        "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                        "FILTER_NAME" => $arParams["FILTER_NAME"],
                        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                        "CACHE_TIME" => $arParams["CACHE_TIME"],
                        "CACHE_FILTER" => $arParams["CACHE_FILTER"],
                        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                        "SET_TITLE" => $arParams["SET_TITLE"],
                        "MESSAGE_404" => $arParams["MESSAGE_404"],
                        "SET_STATUS_404" => $arParams["SET_STATUS_404"],
                        "SHOW_404" => $arParams["SHOW_404"],
                        "FILE_404" => $arParams["FILE_404"],
                        "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                        "PAGE_ELEMENT_COUNT" => $pageElementCount,  // SORT ITEMS
                        "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
                        "PRICE_CODE" => $arParams["PRICE_CODE"],
                        "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                        "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],


                        "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                        "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                        "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                        "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                        "PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

                        "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
                        "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
                        "PAGER_TITLE" => $arParams["PAGER_TITLE"],
                        "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
                        "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
                        "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
                        "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
                        "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
                        "PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
                        "PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
                        "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],

                        "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
                        "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
                        "OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
                        "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
                        "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
                        "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
                        "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
                        "OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],

                        "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                        "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                        "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
                        "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
                        "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
                        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                        'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                        'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],

                        'LABEL_PROP' => $arParams['LABEL_PROP'],
                        'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
                        'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

                        'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
                        'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
                        'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
                        'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
                        'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
                        'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
                        'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
                        'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
                        'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
                        'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],

                        'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                        "ADD_SECTIONS_CHAIN" => "N",
                        'ADD_TO_BASKET_ACTION' => $basketAction,
                        'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
                        'COMPARE_PATH' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['compare'],
                        'BACKGROUND_IMAGE' => (isset($arParams['SECTION_BACKGROUND_IMAGE']) ? $arParams['SECTION_BACKGROUND_IMAGE'] : ''),
                        'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : '')
                    ),
                    $component
                ); ?>
            </div>
            <?
            $GLOBALS['CATALOG_CURRENT_SECTION_ID'] = $intSectionID;
            unset($basketAction);

            if (ModuleManager::isModuleInstalled("sale")) {
                $arRecomData = array();
                $recomCacheID = array('IBLOCK_ID' => $arParams['IBLOCK_ID']);
                $obCache = new CPHPCache();
                if ($obCache->InitCache(36000, serialize($recomCacheID), "/sale/bestsellers")) {
                    $arRecomData = $obCache->GetVars();
                } elseif ($obCache->StartDataCache()) {
                    if (Loader::includeModule("catalog")) {
                        $arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
                        $arRecomData['OFFER_IBLOCK_ID'] = (!empty($arSKU) ? $arSKU['IBLOCK_ID'] : 0);
                    }
                    $obCache->EndDataCache($arRecomData);
                }
                if (!empty($arRecomData)) {
                    if (!isset($arParams['USE_SALE_BESTSELLERS']) || $arParams['USE_SALE_BESTSELLERS'] != 'N') {
                        ?>
                        <div class="col-xs-12">
                            <? $APPLICATION->IncludeComponent("bitrix:sale.bestsellers", "", array(
                                "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                                "PAGE_ELEMENT_COUNT" => "5",
                                "SHOW_DISCOUNT_PERCENT" => $arParams['SHOW_DISCOUNT_PERCENT'],
                                "PRODUCT_SUBSCRIPTION" => $arParams['PRODUCT_SUBSCRIPTION'],
                                "SHOW_NAME" => "Y",
                                "SHOW_IMAGE" => "Y",
                                "MESS_BTN_BUY" => $arParams['MESS_BTN_BUY'],
                                "MESS_BTN_DETAIL" => $arParams['MESS_BTN_DETAIL'],
                                "MESS_NOT_AVAILABLE" => $arParams['MESS_NOT_AVAILABLE'],
                                "MESS_BTN_SUBSCRIBE" => $arParams['MESS_BTN_SUBSCRIBE'],
                                "LINE_ELEMENT_COUNT" => 5,
                                "TEMPLATE_THEME" => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                                "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
                                "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                "CACHE_TIME" => $arParams["CACHE_TIME"],
                                "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                                "BY" => array(
                                    0 => "AMOUNT",
                                ),
                                "PERIOD" => array(
                                    0 => "15",
                                ),
                                "FILTER" => array(
                                    0 => "CANCELED",
                                    1 => "ALLOW_DELIVERY",
                                    2 => "PAYED",
                                    3 => "DEDUCTED",
                                    4 => "N",
                                    5 => "P",
                                    6 => "F",
                                ),
                                "FILTER_NAME" => $arParams["FILTER_NAME"],
                                "ORDER_FILTER_NAME" => "arOrderFilter",
                                "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                                "SHOW_OLD_PRICE" => $arParams['SHOW_OLD_PRICE'],
                                "PRICE_CODE" => $arParams["PRICE_CODE"],
                                "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
                                "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                                "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                                "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                                "BASKET_URL" => $arParams["BASKET_URL"],
                                "ACTION_VARIABLE" => (!empty($arParams["ACTION_VARIABLE"]) ? $arParams["ACTION_VARIABLE"] : "action") . "_slb",
                                "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                                "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                                "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                                "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                                "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                                "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                                "SHOW_PRODUCTS_" . $arParams["IBLOCK_ID"] => "Y",
                                "OFFER_TREE_PROPS_" . $arRecomData['OFFER_IBLOCK_ID'] => $arParams["OFFER_TREE_PROPS"],
                                "ADDITIONAL_PICT_PROP_" . $arParams['IBLOCK_ID'] => $arParams['ADD_PICT_PROP'],
                                "ADDITIONAL_PICT_PROP_" . $arRecomData['OFFER_IBLOCK_ID'] => $arParams['OFFER_ADD_PICT_PROP']
                            ),
                                $component,
                                array("HIDE_ICONS" => "Y")
                            ); ?>
                        </div>
                        <?
                    }
                    if (!isset($arParams['USE_BIG_DATA']) || $arParams['USE_BIG_DATA'] != 'N') {
                        ?>
                        <div class="col-xs-12">
                            <? $APPLICATION->IncludeComponent("bitrix:catalog.bigdata.products", "", array(
                                "LINE_ELEMENT_COUNT" => 5,
                                "TEMPLATE_THEME" => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                                "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
                                "BASKET_URL" => $arParams["BASKET_URL"],
                                "ACTION_VARIABLE" => (!empty($arParams["ACTION_VARIABLE"]) ? $arParams["ACTION_VARIABLE"] : "action") . "_cbdp",
                                "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                                "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                                "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                                "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                                "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                                "SHOW_OLD_PRICE" => $arParams['SHOW_OLD_PRICE'],
                                "SHOW_DISCOUNT_PERCENT" => $arParams['SHOW_DISCOUNT_PERCENT'],
                                "PRICE_CODE" => $arParams["PRICE_CODE"],
                                "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
                                "PRODUCT_SUBSCRIPTION" => $arParams['PRODUCT_SUBSCRIPTION'],
                                "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                                "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                                "SHOW_NAME" => "Y",
                                "SHOW_IMAGE" => "Y",
                                "MESS_BTN_BUY" => $arParams['MESS_BTN_BUY'],
                                "MESS_BTN_DETAIL" => $arParams['MESS_BTN_DETAIL'],
                                "MESS_BTN_SUBSCRIBE" => $arParams['MESS_BTN_SUBSCRIBE'],
                                "MESS_NOT_AVAILABLE" => $arParams['MESS_NOT_AVAILABLE'],
                                "PAGE_ELEMENT_COUNT" => 5,
                                "SHOW_FROM_SECTION" => "Y",
                                "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                                "DEPTH" => "2",
                                "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                "CACHE_TIME" => $arParams["CACHE_TIME"],
                                "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                                "SHOW_PRODUCTS_" . $arParams["IBLOCK_ID"] => "Y",
                                "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                                "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                                "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                                "SECTION_ID" => $intSectionID,
                                "SECTION_CODE" => "",
                                "SECTION_ELEMENT_ID" => "",
                                "SECTION_ELEMENT_CODE" => "",
                                "LABEL_PROP_" . $arParams["IBLOCK_ID"] => $arParams['LABEL_PROP'],
                                "PROPERTY_CODE_" . $arParams["IBLOCK_ID"] => $arParams["LIST_PROPERTY_CODE"],
                                "PROPERTY_CODE_" . $arRecomData['OFFER_IBLOCK_ID'] => $arParams["LIST_OFFERS_PROPERTY_CODE"],
                                "CART_PROPERTIES_" . $arParams["IBLOCK_ID"] => $arParams["PRODUCT_PROPERTIES"],
                                "CART_PROPERTIES_" . $arRecomData['OFFER_IBLOCK_ID'] => $arParams["OFFERS_CART_PROPERTIES"],
                                "ADDITIONAL_PICT_PROP_" . $arParams["IBLOCK_ID"] => $arParams['ADD_PICT_PROP'],
                                "ADDITIONAL_PICT_PROP_" . $arRecomData['OFFER_IBLOCK_ID'] => $arParams['OFFER_ADD_PICT_PROP'],
                                "OFFER_TREE_PROPS_" . $arRecomData['OFFER_IBLOCK_ID'] => $arParams["OFFER_TREE_PROPS"],
                                "RCM_TYPE" => (isset($arParams['BIG_DATA_RCM_TYPE']) ? $arParams['BIG_DATA_RCM_TYPE'] : '')
                            ),
                                $component,
                                array("HIDE_ICONS" => "Y")
                            ); ?>
                        </div>
                        <?
                    }
                }
            }
            ?>
        </div>
        <?php $APPLICATION->ShowViewContent('desc_r'); ?>
    </div>