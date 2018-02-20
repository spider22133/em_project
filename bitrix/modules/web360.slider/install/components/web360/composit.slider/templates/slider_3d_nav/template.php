<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$frame = $this->createFrame()->begin();?>

<ul class="bxslider">
<?
if(!empty($arResult["ITEMS"]))
{
	foreach($arResult["ITEMS"] as $key=>$arItems)
	{
		if($arParams["DATA_SOURCE"] == "MEDIA_LIBRARY" ||($arParams["DATA_SOURCE"] == "IBLOCK" && $arParams["CLICKED_ACTION"] == "EMPTY"))
		{
			?>
			<li><div><img src="<?=$arItems["CONTENT"]["RESIZE_IMG"]?>"/></div></li>
			<?
		}
		elseif($arParams["DATA_SOURCE"] == "IBLOCK" && $arParams["CLICKED_ACTION"] == "POPUP")
		{
			?>
			<li><div><a class="fancyimg" rel="group" href="<?=$arItems["CONTENT"]["STANDART_IMG"]?>"><img src="<?=$arItems["CONTENT"]["RESIZE_IMG"]?>"/></a></div></li>
			<?
		}
		elseif($arParams["DATA_SOURCE"] == "BANNER" && $arItems["CONTENT"]["TYPE"] == "html" && !empty($arItems["CONTENT"]["CODE"]))
		{
			?>
			<li><div><?echo $arItems["CONTENT"]["CODE"]?></div></li>
			<?
		}
		else
		{
			if(!empty($arItems["URL"]))
			{
				?>
				<li><div><a href="<?=$arItems["URL"]?>"> <img src="<?=$arItems["CONTENT"]["RESIZE_IMG"]?>"/></a></div></li>
				<?
			}
			else
			{
				?>
				<li><div><img src="<?=$arItems["CONTENT"]["RESIZE_IMG"]?>"/></div></li>
				<?
			}
		}
		
	}
}
?>
</ul>

<?$frame->end();?>

























