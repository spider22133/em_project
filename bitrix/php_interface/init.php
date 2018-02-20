<?
session_start();
CModule::IncludeModule("highloadblock");
CModule::IncludeModule("catalog");

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

 CModule::AddAutoloadClasses( '', array(
//     'ext1cHandler' => '/bitrix/php_interface/split_attributes.php',
//     'StringInput' => '/bitrix/php_interface/stringinput.php'
) );

///**
// * Отключаем тест скорости
// */
//AddEventHandler("main", "OnEndBufferContent", "sectionNoSpeedTest");
//function sectionNoSpeedTest(&$content){$js = 'ba.src = (document.location.protocol == "https:" ? "https://" : "http://") + "bitrix.info/ba.js";';$content = str_replace($js, '', $content);}

/**
 * Удаляем лишние пробелы перед выводом
 */
AddEventHandler("main", "OnEndBufferContent", "ChangeMyContent");
function ChangeMyContent(&$content){$content = sanitize_output($content);}
function sanitize_output($buffer){return preg_replace('~>\s*\n\s*<~', '><', $buffer);}

AddEventHandler("main", "OnEpilog", "Redirect404");
function Redirect404() {
    if(
        !defined('ADMIN_SECTION') &&
        defined("ERROR_404") &&
        defined("PATH_TO_404") &&
        file_exists($_SERVER["DOCUMENT_ROOT"].PATH_TO_404)
    ) {
        //LocalRedirect("/404.php", "404 Not Found");
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        CHTTP::SetStatus("404 Not Found");
        include($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/header.php");
        include($_SERVER["DOCUMENT_ROOT"].PATH_TO_404);
        include($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/footer.php");
    }
}

//AddEventHandler("catalog", "OnSuccessCatalogImport1C", "createDiscounts");
function createDiscounts()
{
    $hl_data = HL\HighloadBlockTable::getById(9)->fetch();

    $entity = HL\HighloadBlockTable::compileEntity($hl_data);
    $entityDataClass = $entity->getDataClass();


    $discountList = $entityDataClass::getList(array(
        'select' => array('ID', 'UF_KODKARTYSHTRIKHKO', 'UF_SKIDKA', 'UF_NAME'),
        'order' => array('UF_KODKARTYSHTRIKHKO' => 'ASC')
    ));


                    while ($arData = $discountList->fetch()) {
                        if (CModule::IncludeModule("catalog"))
                        {
                            // Check if ID exists
    //                        $dbCoupon = CCatalogDiscountCoupon::GetList(array(
    //                            'select' => array('COUPON'),
    //                        ));
    //                        $COUPON = '';
    //                        while ($arCoupon = $dbCoupon->Fetch())
    //                        {
    //                            if($dbCoupon['COUPON'] == $arData['UF_KODKARTYSHTRIKHKO']) {
    //
    //                            } else {
    //                                $COUPON .= $arData['UF_KODKARTYSHTRIKHKO'];
    //                            }
    //                        }
                            $COUPON = $arData['UF_KODKARTYSHTRIKHKO'];
                            $discountID = '';
                            if($arData['UF_SKIDKA'] == 5) {
                                $discountID .= 2;
                            } elseif ($arData['UF_SKIDKA'] == 10) {
                                $discountID .= 3;
                            }
                            elseif ($arData['UF_SKIDKA'] == 100) {
                                $discountID .= 4;
                            }

                            $arCouponFields = array(
                                "DISCOUNT_ID" => $discountID,
                                "ACTIVE" => "Y",
                                "ONE_TIME" => "N",
                                "COUPON" => $COUPON,
                                "DATE_APPLY" => false,
                                "DESCRIPTION" => $arData['UF_NAME'],
                            );

                            $CID = CCatalogDiscountCoupon::Add($arCouponFields);
                            $CID = IntVal($CID);
    //                        if ($CID <= 0)
    //                        {
    //                            $ex = $APPLICATION->GetException();
    //                            $errorMessage = $ex->GetString();
    //                            echo $errorMessage;
    //                        }
                        }
                    }


}
?>