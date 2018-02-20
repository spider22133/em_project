<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$this->setFrameMode(true);

if (empty($arResult["ALL_ITEMS"]))
	return;

CUtil::InitJSCore();

if (file_exists($_SERVER["DOCUMENT_ROOT"].$this->GetFolder().'/themes/'.$arParams["MENU_THEME"].'/colors.css'))
	$APPLICATION->SetAdditionalCSS($this->GetFolder().'/themes/'.$arParams["MENU_THEME"].'/colors.css');

$menuBlockId = "catalog_menu_".$this->randString();
?>

<div class="catalog-container" id="<?=$menuBlockId?>">
	<div class="catalog" id="cont_<?=$menuBlockId?>">
		<ul class="bx-nav-list-1-lvl" id="ul_<?=$menuBlockId?>">
			<?foreach($arResult["MENU_STRUCTURE"] as $itemID => $arColumns):?>     <!-- first level-->
			<?$existPictureDescColomn = ($arResult["ALL_ITEMS"][$itemID]["PARAMS"]["picture_src"] || $arResult["ALL_ITEMS"][$itemID]["PARAMS"]["description"]) ? true : false; ?>
			<li class="bx-nav-1-lvl bx-nav-list-<?=($existPictureDescColomn) ? count($arColumns)+1 : count($arColumns)?>-col <?if($arResult["ALL_ITEMS"][$itemID]["SELECTED"]):?>bx-active<?endif?><?if (is_array($arColumns) && count($arColumns) > 0):?> bx-nav-parent<?endif?>"
            >
                <a
                    href="<?=$arResult["ALL_ITEMS"][$itemID]["LINK"]?>"
                    <?if (is_array($arColumns) && count($arColumns) > 0 && $existPictureDescColomn):?>
                        onmouseover="obj_<?=$menuBlockId?>.changeSectionPicure(this, '<?=$itemID?>');"
                    <?endif?>
                >
                    <?=$arResult["ALL_ITEMS"][$itemID]["TEXT"]?>
                </a>

				<?if (is_array($arColumns) && count($arColumns) > 0):?>
                    <ul class="dropd_menu">
				<div class="col-sm-8">
					<div class="row">
                    <?foreach($arColumns as $key=>$arRow):?>
                            <?foreach($arRow as $itemIdLevel_2=>$arLevel_3):?>  <!-- second level-->
						<li class="col-sm-3" style="">
                            <a
                                href="<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["LINK"]?>"
                                <?if ($existPictureDescColomn):?>
                                    onmouseover="obj_<?=$menuBlockId?>.changeSectionPicure(this, '<?=$itemIdLevel_2?>');"
                                <?endif?>
                               data-picture="<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["PARAMS"]["picture_src"]?>"
                            >
                                <?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["TEXT"]?>
                            </a>
                                <?if (is_array($arLevel_3) && count($arLevel_3) > 0):?>
                            <ul class="dropd_items">
                                    <?foreach($arLevel_3 as $itemIdLevel_3):?>	<!-- third level-->
                                <li>
                                    <a
                                        href="<?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["LINK"]?>"
                                        <?if ($existPictureDescColomn):?>
                                            onmouseover="obj_<?=$menuBlockId?>.changeSectionPicure(this, '<?=$itemIdLevel_3?>');return false;"
                                        <?endif?>
                                        data-picture="<?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["PARAMS"]["picture_src"]?>"
                                    >
                                        <?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["TEXT"]?>
                                    </a>
                                </li>
                                    <?endforeach;?>
                            </ul>
                                <?endif?>
                        </li>
                            <?endforeach;?>
                    <?endforeach;?>
					</div>
				</div>

						<?if ($existPictureDescColomn):?>
							<div class="col-sm-4 drop_image" data-role="desc-img-block">
                                <div class="row">
                                    <a href="<?=$arResult["ALL_ITEMS"][$itemID]["LINK"]?>">
                                        <img class="img-responsive" src="<?=$arResult["ALL_ITEMS"][$itemID]["PARAMS"]["picture_src"]?>"  alt="">
                                    </a>
                                </div>
							</div>
						<?endif?>
                    </ul>

				<?endif?>
			</li>
			<?endforeach;?>
		</ul>
	</div>
</div>


<script>
	BX.ready(function () {
		window.obj_<?=$menuBlockId?> = new BX.Main.Menu.CatalogHorizontal('<?=CUtil::JSEscape($menuBlockId)?>', <?=CUtil::PhpToJSObject($arResult["ITEMS_IMG_DESC"])?>);
	});
</script>