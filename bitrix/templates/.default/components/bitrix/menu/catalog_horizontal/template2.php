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
<div class="bx-<?=$arParams["MENU_THEME"]?>" id="<?=$menuBlockId?>">
	<nav class="navbar navbar-gem" id="cont_<?=$menuBlockId?>">
		<div class="navbar-header" >
			<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".js-navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>

		<div class="collapse navbar-collapse js-navbar-collapse">
			<ul class="nav navbar-nav bx-nav-list-1-lvl" id="ul_<?=$menuBlockId?>">
				<?foreach($arResult["MENU_STRUCTURE"] as $itemID => $arColumns):?>     <!-- first level-->
				<?$existPictureDescColomn = ($arResult["ALL_ITEMS"][$itemID]["PARAMS"]["picture_src"] || $arResult["ALL_ITEMS"][$itemID]["PARAMS"]["description"]) ? true : false;?>
				<li class="dropdown mega-dropdown bx-nav-1-lvl bx-nav-list-<?=($existPictureDescColomn) ? count($arColumns)+1 : count($arColumns)?>-col <?if($arResult["ALL_ITEMS"][$itemID]["SELECTED"]):?>bx-active<?endif?><?if (is_array($arColumns) && count($arColumns) > 0):?> bx-nav-parent<?endif?>"
                    onmouseover="BX.CatalogMenu.itemOver(this);"
                    onmouseout="BX.CatalogMenu.itemOut(this)"
                    <?if (is_array($arColumns) && count($arColumns) > 0):?>
                        data-role="bx-menu-item"
                    <?endif?>
                    onclick="if (BX.hasClass(document.documentElement, 'bx-touch')) obj_<?=$menuBlockId?>.clickInMobile(this, event);"
                >
					<a href="<?=$arResult["ALL_ITEMS"][$itemID]["LINK"]?>"
						<?if (is_array($arColumns) && count($arColumns) > 0 && $existPictureDescColomn):?>
					   onmouseover="obj_<?=$menuBlockId?>.changeSectionPicure(this, '<?=$itemID?>');"
					   <?endif?>
					   class="dropdown-toggle" data-toggle="dropdown"><?=$arResult["ALL_ITEMS"][$itemID]["TEXT"]?>
					</a>
                    <?if (is_array($arColumns) && count($arColumns) > 0):?>
					<ul class="dropdown-menu mega-dropdown-menu">
						<div class="col-sm-8">
                            <?foreach($arColumns as $key=>$arRow):?>
									<?foreach($arRow as $itemIdLevel_2=>$arLevel_3):?>  <!-- second level-->
						<li class="col-sm-3">
							<ul>
								<li class="dropdown-header">
                                    <a href="<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["LINK"]?>"
										<?if ($existPictureDescColomn):?>
										onmouseover="obj_<?=$menuBlockId?>.changeSectionPicure(this, '<?=$itemIdLevel_2?>');"
									<?endif?>
									 data-picture="<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["PARAMS"]["picture_src"]?>"
                                     <?if($arResult["ALL_ITEMS"][$itemIdLevel_2]["SELECTED"]):?>class="bx-active"<?endif?>
                                    >
                                        <?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["TEXT"]?>

									</a></li>
								<?if (is_array($arLevel_3) && count($arLevel_3) > 0):?>
									<?foreach($arLevel_3 as $itemIdLevel_3):?>	<!-- third level-->
											<li><a href="<?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["LINK"]?>"
													<?if ($existPictureDescColomn):?>
														onmouseover="obj_<?=$menuBlockId?>.changeSectionPicure(this, '<?=$itemIdLevel_3?>');return false;"
													<?endif?>
												   data-picture="<?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["PARAMS"]["picture_src"]?>"
                                                   <?if($arResult["ALL_ITEMS"][$itemIdLevel_2]["SELECTED"]):?>class="bx-active"<?endif?>
                                                >
                                                    <?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["TEXT"]?></a>
											</li>
									<?endforeach;?>
								<?endif?>
								<!--<li class="divider"></li>-->
							</ul>
						</li>
									<?endforeach;?>
                                <?endforeach;?>

							</div>


						<?if ($existPictureDescColomn):?>
						<div class="col-sm-4" data-role="desc-img-block">
									<a href="<?=$arResult["ALL_ITEMS"][$itemID]["LINK"]?>">
									<img src="<?=$arResult["ALL_ITEMS"][$itemID]["PARAMS"]["picture_src"]?>" class="img-responsive" alt="" height="400">
									</a>
						</div>
						<?endif?>
					</ul>
                    <?endif?>
				</li>
				<?endforeach;?>
			</ul>
		</div><!-- /.nav-collapse -->
	</nav>
</div>


<script>
	BX.ready(function () {
		window.obj_<?=$menuBlockId?> = new BX.Main.Menu.CatalogHorizontal('<?=CUtil::JSEscape($menuBlockId)?>', <?=CUtil::PhpToJSObject($arResult["ITEMS_IMG_DESC"])?>);
	});


    jQuery(document).ready(function(){
        $(".dropdown").hoverIntent(
            function() { $('.dropdown-menu', this).fadeIn("fast");
            },
            function() { $('.dropdown-menu', this).fadeOut("fast");
            });
    });

</script>