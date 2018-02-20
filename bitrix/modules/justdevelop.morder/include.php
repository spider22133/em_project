<?
IncludeModuleLangFile(__FILE__);


global $DB;
$db_type = strtolower($DB->type);
CModule::AddAutoloadClasses(
    "justdevelop.morder",
    array()
);

require_once dirname(__FILE__) . '/classes/general/JUSTDEVELOP_Send.php';

class CJUSTDEVELOP
{
    function OnSaleComponentOrderOneStepCompleteHandler($id, $arFields)
    {
        $add_phone = COption::GetOptionString('justdevelop.morder', 'add_phone_new' . $arFields['LID']);
        $arPhone = explode(",", $add_phone);

        if (count($arPhone) > 0 && !empty($arPhone[0])) {

            $db_props = CSaleOrderProps::GetList();
            $arReplaces = array();
            while ($arProps = $db_props->Fetch()) {
                if (!in_array($arProps['CODE'], $arReplaces) && strlen($arProps['CODE']) > 0) $arReplaces['#PROP_' . $arProps['CODE'] . '#'] = $arProps['CODE'];
            }
            $arFilter = array("ORDER_ID" => $arFields['ID']);

            $db_order = CSaleOrderPropsValue::GetList(array(), $arFilter);
            $phone = '';
            while ($arOrder = $db_order->Fetch()) {
                if (in_array($arOrder['CODE'], $arReplaces)) $arReplaces['#PROP_' . $arOrder['CODE'] . '#'] = $arOrder['VALUE'];
            }

            $obStatus = CSaleStatus::GetList(array(), array('ID' => $arFields['STATUS_ID'], 'LID' => LANGUAGE_ID));
            $status = '';
            if ($arStat = $obStatus->Fetch()) $status = $arStat['NAME'];
            $delivery = CSaleDelivery::GetByID($arFields['DELIVERY_ID']);
            $PaySystem = CSalePaySystem::GetByID($arFields['PAY_SYSTEM_ID']);
            $items = '';

            $arOrder = CSaleOrder::GetByID($arFields['ID']);
            $description = htmlspecialcharsEx($arOrder["USER_DESCRIPTION"]);

            $dbBasketItems = CSaleBasket::GetList(
                array("NAME" => "ASC"),
                array("ORDER_ID" => $id),
                false,
                false,
                array("NAME", "QUANTITY", "PRICE", "PRODUCT_ID")
            );




            while ($arBasketItems = $dbBasketItems->Fetch()) {

                $db_product_props = CIBlockElement::GetProperty('52', $arBasketItems['PRODUCT_ID'], "sort", "asc", array());
                $PROPS = array();

                while ($ar_props = $db_product_props->Fetch()) {
                    $PROPS[$ar_props['CODE']] = $ar_props['VALUE'];
                }

                $items .= $arBasketItems['NAME'] . ' ' . GetMessage('JUSTDEVELOP_NEW_ORDER_KOL') . ' КОЛИЧЕСТВО: ' . (int)$arBasketItems['QUANTITY'] . ' ' . GetMessage('JUSTDEVELOP_NEW_ORDER_PRICE')
                    . ' ЦЕНА: ' . round($arBasketItems['PRICE'], 2) . ' грн.  ' . ' АРТИКУЛ: ' . $PROPS['CML2_ARTICLE'] . "\n \n";

            }


            $arReplacesTemlate = array(
                '#ORDER_NUMBER#' => $arFields['ID'],
                '#ORDER_SUMM#' => round($arFields['PRICE'] - $arFields['PRICE_DELIVERY'], 2),
                '#PRICE_DELIVERY#' => round($arFields['PRICE_DELIVERY'], 2),
                '#PRICE#' => round($arFields['PRICE'], 2),
                '#DELIVERY_DOC_NUM#' => $arFields['DELIVERY_DOC_NUM'],
                '#DELIVERY_DOC_DATE#' => $arFields['DELIVERY_DOC_DATE'],
                '#USER_DESCRIPTION#' => $description,
                '#STATUS_NAME#' => $status,
                '#DELIVERY_NAME#' => ($delivery) ? $delivery['NAME'] : '',
                '#PAY_SYSTEM#' => ($PaySystem) ? $PaySystem['NAME'] : '',
                '#DATE_INSERT#' => $arFields['DATE_INSERT'],
                '#USER_ID#' => $arFields['USER_ID'],
                '#ITEMS#' => trim($items)
            );

            $message = COption::GetOptionString('justdevelop.morder', 'new_order' . $arFields['LID']);
            $message = str_replace(array_keys($arReplacesTemlate), $arReplacesTemlate, $message);
            $message = str_replace(array_keys($arReplaces), $arReplaces, $message);
            $sms = new JUSTDEVELOP_Send;
            foreach ($arPhone as $phone) {
                $sms->Send_SMS($phone, $message);
            }
        }
    }

    function OnSaleComponentOrderCompleteHandler($id, $arFields)
    {
        $add_phone = COption::GetOptionString('justdevelop.morder', 'add_phone_new' . $arFields['LID']);
        $arPhone = explode(",", $add_phone);

        if (count($arPhone) > 0 && !empty($arPhone[0])) {
            $db_props = CSaleOrderProps::GetList();
            $arReplaces = array();
            while ($arProps = $db_props->Fetch()) {
                if (!in_array($arProps['CODE'], $arReplaces) && strlen($arProps['CODE']) > 0) $arReplaces['#PROP_' . $arProps['CODE'] . '#'] = $arProps['CODE'];
            }
            $arFilter = array("ORDER_ID" => $arFields['ID']);

            $db_order = CSaleOrderPropsValue::GetList(array(), $arFilter);
            $phone = '';
            while ($arOrder = $db_order->Fetch()) {
                if ($arOrder['CODE'] == $code) $phone = $arOrder['VALUE'];
                if (in_array($arOrder['CODE'], $arReplaces)) $arReplaces['#PROP_' . $arOrder['CODE'] . '#'] = $arOrder['VALUE'];
            }

            $obStatus = CSaleStatus::GetList(array(), array('ID' => $arFields['STATUS_ID'], 'LID' => LANGUAGE_ID));
            $status = '';
            if ($arStat = $obStatus->Fetch()) $status = $arStat['NAME'];
            $delivery = CSaleDelivery::GetByID($arFields['DELIVERY_ID']);
            $PaySystem = CSalePaySystem::GetByID($arFields['PAY_SYSTEM_ID']);

            $items = '';

            $dbBasketItems = CSaleBasket::GetList(
                array("NAME" => "ASC"),
                array("ORDER_ID" => $id),
                false,
                false,
                array("NAME", "QUANTITY", "PRICE")
            );

            while ($arBasketItems = $dbBasketItems->Fetch())
                $items .= $arBasketItems['NAME'] . ' ' . GetMessage('JUSTDEVELOP_NEW_ORDER_KOL') . ' КОЛИЧЕСТВО: ' . (int)$arBasketItems['QUANTITY'] . ' ' . GetMessage('JUSTDEVELOP_NEW_ORDER_PRICE')
                    . ' ЦЕНА: ' . round($arBasketItems['PRICE'], 2) . 'грн. ';


            $arReplacesTemlate = array(
                '#ORDER_NUMBER#' => $arFields['ID'],
                '#ORDER_SUMM#' => round($arFields['PRICE'] - $arFields['PRICE_DELIVERY'], 2),
                '#PRICE_DELIVERY#' => round($arFields['PRICE_DELIVERY'], 2),
                '#PRICE#' => round($arFields['PRICE'], 2),
                '#DELIVERY_DOC_NUM#' => $arFields['DELIVERY_DOC_NUM'],
                '#DELIVERY_DOC_DATE#' => $arFields['DELIVERY_DOC_DATE'],
                '#STATUS_NAME#' => $status,
                '#DELIVERY_NAME#' => ($delivery) ? $delivery['NAME'] : '',
                '#PAY_SYSTEM#' => ($PaySystem) ? $PaySystem['NAME'] : '',
                '#DATE_INSERT#' => $arFields['DATE_INSERT'],
                '#USER_ID#' => $arFields['USER_ID'],
                '#ITEMS#' => trim($items)
            );

            $message = COption::GetOptionString('justdevelop.morder', 'new_order' . $arFields['LID']);

            $message = str_replace(array_keys($arReplacesTemlate), $arReplacesTemlate, $message);
            $message = str_replace(array_keys($arReplaces), $arReplaces, $message);

            $sms = new JUSTDEVELOP_Send;
            foreach ($arPhone as $phone) {
                $sms->Send_SMS($phone, $message);
            }
        }
    }

    function OnSalePayOrderHandler($id, $val)
    {
        if ($val == 'Y') {
            $db_props = CSaleOrderProps::GetList();
            $arReplaces = array();
            while ($arProps = $db_props->Fetch()) {
                if (!in_array($arProps['CODE'], $arReplaces) && strlen($arProps['CODE']) > 0) $arReplaces['#PROP_' . $arProps['CODE'] . '#'] = $arProps['CODE'];
            }
            $arFilter = array("ORDER_ID" => $id);
            $db_order = CSaleOrderPropsValue::GetList(array(), $arFilter);
            while ($arOrder = $db_order->Fetch()) {
                if (in_array($arOrder['CODE'], $arReplaces)) $arReplaces['#PROP_' . $arOrder['CODE'] . '#'] = $arOrder['VALUE'];
            }

            $arFields = CSaleOrder::GetByID($id);

            $add_phone = COption::GetOptionString('justdevelop.morder', 'add_phone_pay' . $arFields['LID']);
            $arPhone = explode(",", $add_phone);
            if (count($arPhone) > 0 && !empty($arPhone[0])) {
                $obStatus = CSaleStatus::GetList(array(), array('ID' => $arFields['STATUS_ID'], 'LID' => LANGUAGE_ID));
                $status = '';
                if ($arStat = $obStatus->Fetch()) $status = $arStat['NAME'];
                $delivery = CSaleDelivery::GetByID($arFields['DELIVERY_ID']);
                $PaySystem = CSalePaySystem::GetByID($arFields['PAY_SYSTEM_ID']);

                $items = '';

                $dbBasketItems = CSaleBasket::GetList(
                    array("NAME" => "ASC"),
                    array("ORDER_ID" => $id),
                    false,
                    false,
                    array("NAME", "QUANTITY", "PRICE")
                );

                while ($arBasketItems = $dbBasketItems->Fetch())
                    $items .= $arBasketItems['NAME'] . ' ' . GetMessage('JUSTDEVELOP_NEW_ORDER_KOL') . ': ' . (int)$arBasketItems['QUANTITY'] . ' ' . GetMessage('JUSTDEVELOP_NEW_ORDER_PRICE') . ': ' . round($arBasketItems['PRICE'], 2) . ' ';

                $arReplacesTemlate = array(
                    '#ORDER_NUMBER#' => $arFields['ID'],
                    '#ORDER_SUMM#' => round($arFields['PRICE'] - $arFields['PRICE_DELIVERY'], 2),
                    '#PRICE_DELIVERY#' => round($arFields['PRICE_DELIVERY'], 2),
                    '#PRICE#' => round($arFields['PRICE'], 2),
                    '#CML2_ARTICLE#' => $arFields['CML2_ARTICLE'],
                    '#DELIVERY_DOC_NUM#' => $arFields['DELIVERY_DOC_NUM'],
                    '#DELIVERY_DOC_DATE#' => $arFields['DELIVERY_DOC_DATE'],
                    '#STATUS_NAME#' => $status,
                    '#DELIVERY_NAME#' => ($delivery) ? $delivery['NAME'] : '',
                    '#PAY_SYSTEM#' => ($PaySystem) ? $PaySystem['NAME'] : '',
                    '#DATE_INSERT#' => $arFields['DATE_INSERT'],
                    '#USER_ID#' => $arFields['USER_ID'],
                    '#ITEMS#' => trim($items)
                );

                $message = COption::GetOptionString('justdevelop.morder', 'on_pay_order' . $arFields['LID']);
                $message = str_replace(array_keys($arReplacesTemlate), $arReplacesTemlate, $message);
                $message = str_replace(array_keys($arReplaces), $arReplaces, $message);

                $sms = new JUSTDEVELOP_Send;
                foreach ($arPhone as $phone) {
                    $sms->Send_SMS($phone, $message);
                }
            }
        }
    }

    function OnSaleDeliveryOrderHandler($id, $val)
    {
        if ($val == 'Y') {
            $db_props = CSaleOrderProps::GetList();
            $arReplaces = array();
            while ($arProps = $db_props->Fetch()) {
                if (!in_array($arProps['CODE'], $arReplaces) && strlen($arProps['CODE']) > 0) $arReplaces['#PROP_' . $arProps['CODE'] . '#'] = $arProps['CODE'];
            }
            $arFilter = array("ORDER_ID" => $id);
            $db_order = CSaleOrderPropsValue::GetList(array(), $arFilter);
            $phone = '';
            while ($arOrder = $db_order->Fetch()) {
                if ($arOrder['CODE'] == $code) $phone = $arOrder['VALUE'];
                if (in_array($arOrder['CODE'], $arReplaces)) $arReplaces['#PROP_' . $arOrder['CODE'] . '#'] = $arOrder['VALUE'];
            }

            $arFields = CSaleOrder::GetByID($id);
            $add_phone = COption::GetOptionString('justdevelop.morder', 'add_phone_delivery' . $arFields['LID']);
            $arPhone = explode(",", $add_phone);

            if (count($arPhone) > 0 && !empty($arPhone[0])) {
                $obStatus = CSaleStatus::GetList(array(), array('ID' => $arFields['STATUS_ID'], 'LID' => LANGUAGE_ID));
                $status = '';
                if ($arStat = $obStatus->Fetch()) $status = $arStat['NAME'];
                $delivery = CSaleDelivery::GetByID($arFields['DELIVERY_ID']);
                $PaySystem = CSalePaySystem::GetByID($arFields['PAY_SYSTEM_ID']);
                $items = '';

                $dbBasketItems = CSaleBasket::GetList(
                    array("NAME" => "ASC"),
                    array("ORDER_ID" => $id),
                    false,
                    false,
                    array("NAME", "QUANTITY", "PRICE")
                );

                while ($arBasketItems = $dbBasketItems->Fetch())
                    $items .= $arBasketItems['NAME'] . ' ' . GetMessage('JUSTDEVELOP_NEW_ORDER_KOL') . ': ' . (int)$arBasketItems['QUANTITY'] . ' ' . GetMessage('JUSTDEVELOP_NEW_ORDER_PRICE') . ': ' . round($arBasketItems['PRICE'], 2) . ' ';

                $arReplacesTemlate = array(
                    '#ORDER_NUMBER#' => $arFields['ID'],
                    '#ORDER_SUMM#' => round($arFields['PRICE'] - $arFields['PRICE_DELIVERY'], 2),
                    '#PRICE_DELIVERY#' => round($arFields['PRICE_DELIVERY'], 2),
                    '#PRICE#' => round($arFields['PRICE'], 2),
                    '#CML2_ARTICLE#' => $arFields['CML2_ARTICLE'],
                    '#DELIVERY_DOC_NUM#' => $arFields['DELIVERY_DOC_NUM'],
                    '#DELIVERY_DOC_DATE#' => $arFields['DELIVERY_DOC_DATE'],
                    '#STATUS_NAME#' => $status,
                    '#DELIVERY_NAME#' => ($delivery) ? $delivery['NAME'] : '',
                    '#PAY_SYSTEM#' => ($PaySystem) ? $PaySystem['NAME'] : '',
                    '#DATE_INSERT#' => $arFields['DATE_INSERT'],
                    '#USER_ID#' => $arFields['USER_ID'],
                    '#ITEMS#' => trim($items)
                );

                $message = COption::GetOptionString('justdevelop.morder', 'order_delivery' . $arFields['LID']);

                $message = str_replace(array_keys($arReplacesTemlate), $arReplacesTemlate, $message);
                $message = str_replace(array_keys($arReplaces), $arReplaces, $message);


                $sms = new JUSTDEVELOP_Send;
                foreach ($arPhone as $phone) {
                    $sms->Send_SMS($phone, $message);
                }
            }
        }
    }

    function OnSaleCancelOrderHandler($id, $val)
    {

        $add_phone = COption::GetOptionString('justdevelop.morder', 'add_phone_cancel' . SITE_ID);
        $arPhone = explode(",", $add_phone);

        if (count($arPhone) > 0 && !empty($arPhone[0]) && $val == 'Y') {
            $db_props = CSaleOrderProps::GetList();
            $arReplaces = array();
            while ($arProps = $db_props->Fetch()) {
                if (!in_array($arProps['CODE'], $arReplaces) && strlen($arProps['CODE']) > 0) $arReplaces['#PROP_' . $arProps['CODE'] . '#'] = $arProps['CODE'];
            }
            $arFilter = array("ORDER_ID" => $id);
            $db_order = CSaleOrderPropsValue::GetList(array(), $arFilter);
            while ($arOrder = $db_order->Fetch()) {
                if (in_array($arOrder['CODE'], $arReplaces)) $arReplaces['#PROP_' . $arOrder['CODE'] . '#'] = $arOrder['VALUE'];
            }

            $arFields = CSaleOrder::GetByID($id);

            $obStatus = CSaleStatus::GetList(array(), array('ID' => $arFields['STATUS_ID'], 'LID' => LANGUAGE_ID));
            $status = '';
            if ($arStat = $obStatus->Fetch()) $status = $arStat['NAME'];
            $delivery = CSaleDelivery::GetByID($arFields['DELIVERY_ID']);
            $PaySystem = CSalePaySystem::GetByID($arFields['PAY_SYSTEM_ID']);

            $items = '';

            $dbBasketItems = CSaleBasket::GetList(
                array("NAME" => "ASC"),
                array("ORDER_ID" => $id),
                false,
                false,
                array("NAME", "QUANTITY", "PRICE")
            );

            while ($arBasketItems = $dbBasketItems->Fetch())
                $items .= $arBasketItems['NAME'] . ' ' . GetMessage('JUSTDEVELOP_NEW_ORDER_KOL') . ': ' . (int)$arBasketItems['QUANTITY'] . ' ' . GetMessage('JUSTDEVELOP_NEW_ORDER_PRICE') . ': ' . round($arBasketItems['PRICE'], 2) . ' ';

            $arReplacesTemlate = array(
                '#ORDER_NUMBER#' => $arFields['ID'],
                '#ORDER_SUMM#' => round($arFields['PRICE'] - $arFields['PRICE_DELIVERY'], 2),
                '#PRICE_DELIVERY#' => round($arFields['PRICE_DELIVERY'], 2),
                '#PRICE#' => round($arFields['PRICE'], 2),
                '#CML2_ARTICLE#' => $arFields['CML2_ARTICLE'],
                '#DELIVERY_DOC_NUM#' => $arFields['DELIVERY_DOC_NUM'],
                '#DELIVERY_DOC_DATE#' => $arFields['DELIVERY_DOC_DATE'],
                '#STATUS_NAME#' => $status,
                '#DELIVERY_NAME#' => ($delivery) ? $delivery['NAME'] : '',
                '#PAY_SYSTEM#' => ($PaySystem) ? $PaySystem['NAME'] : '',
                '#DATE_INSERT#' => $arFields['DATE_INSERT'],
                '#USER_ID#' => $arFields['USER_ID'],
                '#ITEMS#' => trim($items)
            );

            $message = COption::GetOptionString('justdevelop.morder', 'order_cancel' . $arFields['LID']);
            $message = str_replace(array_keys($arReplacesTemlate), $arReplacesTemlate, $message);
            $message = str_replace(array_keys($arReplaces), $arReplaces, $message);

            $sms = new JUSTDEVELOP_Send;
            foreach ($arPhone as $phone) {
                $sms->Send_SMS($phone, $message);
            }
        }
    }

    function OnSaleStatusOrderHandler($id, $val)
    {
        $add_phone = COption::GetOptionString('justdevelop.morder', 'add_phone_status_' . SITE_ID);
        $arPhone = explode(",", $add_phone);

        if (count($arPhone) > 0 && !empty($arPhone[0])) {
            $obStatus = CSaleStatus::GetList(array(), array('ID' => $val, 'LID' => LANGUAGE_ID));
            $status = '';
            if ($arStat = $obStatus->Fetch()) $status = $arStat['NAME'];

            $db_props = CSaleOrderProps::GetList();
            $arReplaces = array();
            while ($arProps = $db_props->Fetch()) {
                if (!in_array($arProps['CODE'], $arReplaces) && strlen($arProps['CODE']) > 0) $arReplaces['#PROP_' . $arProps['CODE'] . '#'] = $arProps['CODE'];
            }
            $arFilter = array("ORDER_ID" => $id);
            $db_order = CSaleOrderPropsValue::GetList(array(), $arFilter);
            while ($arOrder = $db_order->Fetch()) {
                if (in_array($arOrder['CODE'], $arReplaces)) $arReplaces['#PROP_' . $arOrder['CODE'] . '#'] = $arOrder['VALUE'];
            }

            $arFields = CSaleOrder::GetByID($id);
            $delivery = CSaleDelivery::GetByID($arFields['DELIVERY_ID']);
            $PaySystem = CSalePaySystem::GetByID($arFields['PAY_SYSTEM_ID']);
            $items = '';

            $dbBasketItems = CSaleBasket::GetList(
                array("NAME" => "ASC"),
                array("ORDER_ID" => $id),
                false,
                false,
                array("NAME", "QUANTITY", "PRICE")
            );

            while ($arBasketItems = $dbBasketItems->Fetch())
                $items .= $arBasketItems['NAME'] . ' ' . GetMessage('JUSTDEVELOP_NEW_ORDER_KOL') . ': ' . (int)$arBasketItems['QUANTITY'] . ' ' . GetMessage('JUSTDEVELOP_NEW_ORDER_PRICE') . ': ' . round($arBasketItems['PRICE'], 2) . ' ';

            $arReplacesTemlate = array(
                '#ORDER_NUMBER#' => $arFields['ID'],
                '#ORDER_SUMM#' => round($arFields['PRICE'] - $arFields['PRICE_DELIVERY'], 2),
                '#PRICE_DELIVERY#' => round($arFields['PRICE_DELIVERY'], 2),
                '#PRICE#' => round($arFields['PRICE'], 2),
                '#CML2_ARTICLE#' => $arFields['CML2_ARTICLE'],
                '#DELIVERY_DOC_NUM#' => $arFields['DELIVERY_DOC_NUM'],
                '#DELIVERY_DOC_DATE#' => $arFields['DELIVERY_DOC_DATE'],
                '#STATUS_NAME#' => $status,
                '#DELIVERY_NAME#' => ($delivery) ? $delivery['NAME'] : '',
                '#PAY_SYSTEM#' => ($PaySystem) ? $PaySystem['NAME'] : '',
                '#DATE_INSERT#' => $arFields['DATE_INSERT'],
                '#USER_ID#' => $arFields['USER_ID'],
                '#ITEMS#' => trim($items)
            );


            $message = COption::GetOptionString('justdevelop.morder', 'status_' . $val . $arFields['LID']);
            $message = str_replace(array_keys($arReplacesTemlate), $arReplacesTemlate, $message);
            $message = str_replace(array_keys($arReplaces), $arReplaces, $message);


            $sms = new JUSTDEVELOP_Send;
            foreach ($arPhone as $phone) {
                $sms->Send_SMS($phone, $message);
            }
        }
    }

    function OnBuildGlobalMenu()
    {

    }
}