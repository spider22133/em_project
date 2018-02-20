<?
//если эфект перелистывания "fade"  и количество слайдов >1 то эффект перелистывания будет установлен 'horizontal'
if($arParams["SCROLL_EFFECT"] == "fade" && $arParams["SLIDER_COUNT"] >1)
	$effect = "horizontal";
else
	$effect = $arParams["SCROLL_EFFECT"];
//автоматическое перелистывание
if($arParams["AUTOMATIC_SLIDER"] == "Y")
	$auto = "true";
else
	$auto = "false";

// выводить ли стрелочки 
if($arParams["OUTPUT_ARROWS"] == "Y")
	$arrows = "true";
else
	$arrows = "false";

//выводить ли нижнюю навигацию
if($arParams["OUTPUT_LOWER_NAVIGATION"] == "Y")
	$navifation = "true";
else
	$navifation = "false"; 

// устанавливаем тим перехода при открытии
if(!empty($arParams["EFFECT_OPEN"]))
	$open = $arParams["EFFECT_OPEN"];
else
	$open = "fade";

// устанавливаем тим перехода при закрытии
if(!empty($arParams["EFFECT_CLOSE"]))
	$close = $arParams["EFFECT_CLOSE"];
else
	$close = "fade";

// устанавливаем скорость перехода 
if(!empty($arParams["SPEED_OPEN"]))
	$s_open = $arParams["SPEED_OPEN"];
else
	$s_open = "fade";

// устанавливаем скорость перехода 
if(!empty($arParams["SPEED_CLOSE"]))
	$s_close = $arParams["SPEED_CLOSE"];
else
	$s_close = "fade";

//скорость перелистывание
if(!empty($arParams["SCROLL_SPEED"]))
	$scroll_speed = $arParams["SCROLL_SPEED"];
else
	$scroll_speed = "0.5";

//время показа слайдера
if(!empty($arParams["SHOW_TIME_SLIDER"]))
	$show_time = $arParams["SHOW_TIME_SLIDER"];
else
	$show_time = "4";


//количество показываемых слайдов
if(!empty($arParams["SLIDER_COUNT"]))
	$slider_count = $arParams["SLIDER_COUNT"];
else
	$slider_count = "1";

//количество перелистываемых слайдов
if(!empty($arParams["TURNED_SLIDER"]))
	$turned_slider = $arParams["TURNED_SLIDER"];
else
	$turned_slider = "1";

if(!empty($arParams["SLIDER_WIDTH"]))
	$width = $arParams["SLIDER_WIDTH"];
else
	$width = "100";

// растояние между слайдами
if(!empty($arParams["SLIDE_MARGIN"]))
	$slide_margin = $arParams["SLIDE_MARGIN"];
else
	$slide_margin = "0";

if(!empty($arParams["SLIDER_WIDTH"]))
	$width = $arParams["SLIDER_WIDTH"];
else
	$width = "300";
?>

<script>
$(document).ready(function(){
	$('.bxslider').bxSlider(
	{
		pagerType:   'full',				//нижняя навигация в виде цифр
		slideMargin: <?=$slide_margin?>,		//расстояние между слайдами
		pager: <?=$navifation?>,			//выводим нижнюю навиигацию
		controls: <?=$arrows?>, 			//выводим стрелочки
		speed: <?=$scroll_speed*1000?>,			//скорость пролистывания слайда
		auto: <?=$auto?>,				//автоматическое перелистывание
		mode: '<?=$effect?>',  				//эффект перелистывания
		pause: <?=$show_time*1000?>, 			//время показа слайда
		minSlides: <?=$slider_count?>,			//максимальное количество слайдов
		maxSlides: <?=$slider_count?>,			//минимальное количество слайдов
		moveSlides: <?=$turned_slider?>,		//количество перелистываемых слайдов
		slideWidth: <?=$width?>,			//ширина слайда
});

});
</script>


<?
if($arParams["CLICKED_ACTION"] == "POPUP")
{
?>
	<script>
	$(document).ready(function(){
		$('.fancyimg').fancybox(
		{
			'transitionIn'  :   '<?=$open?>',
			'transitionOut' :   '<?=$close?>',
	       		'speedIn'       :   <?=$s_open*1000?>, 
	       		'speedOut'      :   <?=$s_close*1000?>, 
	    });
	});
	</script>
<?
}
?>

<script type="text/javascript">
$(document).ready(function(){
	$(".bx-wrapper").mouseenter(function(){
		$(".bx-next").fadeIn("slow");
		$(".bx-prev").fadeIn("slow");
		$(".bx-wrapper .bx-pager").fadeIn("slow");
	});
	$(".bx-wrapper").mouseleave(function(){
		$(".bx-next").fadeOut("slow");
		$(".bx-prev").fadeOut("slow");
		$(".bx-wrapper .bx-pager").fadeOut("slow");
	});


});

</script>

<?
if($arParams["SCROLL_EFFECT"] =="vertical")
{
?>
<script type="text/javascript">
$(document).ready(function(){
	$(".bx-next").attr("class", "bx-next-top")
	$(".bx-prev").attr("Class", "bx-prev-bottom")
});
</script>
<?
}
?>