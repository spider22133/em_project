<?
$eventManager = \Bitrix\Main\EventManager::getInstance();
 
$eventManager->addEventHandlerCompatible('iblock', 'OnAfterIBlockElementUpdate',
    array('ext1cHandler', 'attributeFieldToProps'));
$eventManager->addEventHandlerCompatible('iblock', 'OnAfterIBlockElementAdd',
    array('ext1cHandler', 'attributeFieldToProps'));
 
class ext1cHandler
{
 
    const CML2_ATTRIBUTES_NAME = 'CML2_ATTRIBUTES';
 
    protected static $iblockProps = null;
 
    /**
     * @param $iblockId
     * @return array|null
     */
    protected static function getIblockProps($iblockId)
    {
        if (self::$iblockProps === null) {
            $resProps = CIBlock::GetProperties($iblockId, Array(), Array());
            if (intval($resProps->SelectedRowsCount()) > 0) {
                self::$iblockProps = array();
                while ($arProp = $resProps->Fetch()) {
                    self::$iblockProps[$arProp['CODE']] = $arProp['ID'];
                }
            }
        }
        return self::$iblockProps;
    }
 
    /**
     * @param $arFields
     */
    public static function attributeFieldToProps($arFields)
    {
 
        if (!self::is1cSync()) return true;
 
        self::getIblockProps($arFields['IBLOCK_ID']);
 
        if (empty(self::$iblockProps) || !is_array(self::$iblockProps)) return;
 
        //получаем массив значений множественного свойства CML2_ATTRIBUTES в которое стандартно выгружаются характеристики ТП из 1С
        $resCml2Attributes = CIBlockElement::GetProperty($arFields['IBLOCK_ID'], $arFields['ID'], array('sort' => 'asc'), array('CODE' => self::CML2_ATTRIBUTES_NAME));
 
        while ($arCml2Attribute = $resCml2Attributes->GetNext()) {
 
            $cml2AttributeName = $arCml2Attribute['DESCRIPTION']; //название характеристики
            $cml2AttributeValue = $arCml2Attribute['VALUE']; //значение характеристики
 
            // создание свойства
            $codeNewProp = self::getTranslit($cml2AttributeName);
 
            if (!isset(self::$iblockProps[$codeNewProp])) {
 
                $arFieldsProp = array(
                    'NAME' => $cml2AttributeName,
                    'ACTIVE' => 'Y',
                    'SORT' => '500',
                    'CODE' => $codeNewProp,
                    'PROPERTY_TYPE' => 'L',
                    'IBLOCK_ID' => $arFields['IBLOCK_ID'],
                    'VALUES' => array(),
                );
 
                $ibp = new CIBlockProperty;
                if ($propId = $ibp->Add($arFieldsProp)) {
                    self::$iblockProps[$codeNewProp] = $propId;
                }
 
            }
 
            if (isset(self::$iblockProps[$codeNewProp])) {
 
                self::getEnumListProp($arFields['IBLOCK_ID'], self::$iblockProps[$codeNewProp]);
 
                $xmlIdPropValue = self::getTranslit($cml2AttributeValue);
 
                if (!isset(self::$enumListProps[self::$iblockProps[$codeNewProp]][$xmlIdPropValue])) {
 
                    $ibpenum = new CIBlockPropertyEnum;
                    $arFieldsEnum = array(
                        'XML_ID' => $xmlIdPropValue,
                        'PROPERTY_ID' => self::$iblockProps[$codeNewProp],
                        'VALUE' => $cml2AttributeValue
                    );
 
                    if ($enumPropValueId = $ibpenum->Add($arFieldsEnum)) {
                        self::$enumListProps[self::$iblockProps[$codeNewProp]][$xmlIdPropValue] = $enumPropValueId;
                    }
 
                }
 
                if (isset(self::$enumListProps[self::$iblockProps[$codeNewProp]][$xmlIdPropValue])) {
                    CIBlockElement::SetPropertyValues($arFields['ID'], $arFields['IBLOCK_ID'], array(
                        'VALUE' => self::$enumListProps[self::$iblockProps[$codeNewProp]][$xmlIdPropValue]
                    ), self::$iblockProps[$codeNewProp]);
                }
 
            }
 
        }
 
    }
 
    private static $enumListProps = array();
 
    /**
     * @param $iblockId
     * @param $propId
     * @return array
     */
    protected static function getEnumListProp($iblockId, $propId)
    {
 
        if (!isset(self::$enumListProps[$propId])) {
            $resEnumField = CIBlockPropertyEnum::GetList(array('SORT' => 'ASC'), array('IBLOCK_ID' => $iblockId, 'PROPERTY_ID' => $propId));
            if (intval($resEnumField->SelectedRowsCount()) > 0) {
                self::$enumListProps[$propId] = array();
                while ($arEnumField = $resEnumField->Fetch()) {
                    self::$enumListProps[$propId][$arEnumField['XML_ID']] = $arEnumField['ID'];
                }
            }
        }
        return self::$enumListProps;
 
    }
 
    /**
     * @param $text
     * @param string $lang
     * @return string
     */
    private static function getTranslit($text, $lang = 'ru')
    {
 
        $resultString = CUtil::translit($text, $lang, array(
                'max_len' => 50,
                'change_case' => 'U',
                'replace_space' => '_',
                'replace_other' => '_',
                'delete_repeat_replace' => true,
            )
        );
 
        if (preg_match('/^[0-9]/', $resultString)) {
            $resultString = '_' . $resultString;
        }
 
        return $resultString;
    }
 
    /**
     * @return bool
     */
    private static function is1cSync()
    {
        static $is1C = null;
        if ($is1C === null) {
            $is1C = (isset($_GET['type'], $_GET['mode']) && $_GET['type'] === '1с_catalog' && $_GET['mode'] === 'import');
        }
        return $is1C;
    }
 
}
?>