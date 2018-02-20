<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>


<?
foreach ($arResult['ITEMS'] as $key => $val):
    ?>
    <?
    $this->AddEditAction($val['ID'], $val['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($val['ID'], $val['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('FAQ_DELETE_CONFIRM', array("#ELEMENT#" => CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_NAME")))));
    ?>
    <?
    if ($key > 0):
        ?>
        <div class="hr"></div>
        <?
    endif;
    ?>
    <div id="<?= $this->GetEditAreaId($val['ID']); ?>" class="" style="padding: 25px; background-color: #f9f9f9; margin-top: 10px">
        <a name="<?= $val["ID"] ?>"></a>
        <h4 style="padding: 10px 0; font-size:30px;text-align:left;"><span
                    style="color: #D9017A; font-weight: 800;"><?= $val['NAME'] ?></span></h4>
        <p>
            <?= $val['PREVIEW_TEXT'] ?>
            <?= $val['DETAIL_TEXT'] ?>
        </p>

    </div>
<? endforeach; ?>