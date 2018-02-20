<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
</div>
<?if (!($isCatalogPage || $isIndexPage || $isCartPage)):?>
	<div class="sidebar col-md-3 col-sm-4">
		<?$APPLICATION->IncludeComponent(
			"bitrix:main.include",
			"",
			Array(
				"AREA_FILE_SHOW" => "sect",
				"AREA_FILE_SUFFIX" => "sidebar",
				"AREA_FILE_RECURSIVE" => "Y",
				"EDIT_MODE" => "html",
			),
			false,
			Array('HIDE_ICONS' => 'Y')
		);?>
	</div><!--// sidebar -->
<?endif?>

</div><!--//row-->
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "sect",
		"AREA_FILE_SUFFIX" => "bottom",
		"AREA_FILE_RECURSIVE" => "N",
		"EDIT_MODE" => "html",
	),
	false,
	Array('HIDE_ICONS' => 'Y')
);?>
</div><!--//container bx-content-seection-->
</div><!--//workarea-->







<!--------------Footer-------------->

<footer id="footer">
	<!-- scroll_top_btn -->
	<a href="#" id="toTop" style="display: block;"><span id="toTopHover" style="opacity: 1;"></span></a>
	<!--end scroll_top_btn -->
	<div class="footer-top">
		<div class="container">
			<div class="row">
				<div class="col-sm-2" style="margin-left:5%;">
					<div class="single-widget">
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:main.include",
                            "",
                            Array(
                                "AREA_FILE_SHOW" => "file",
                                "AREA_FILE_SUFFIX" => "inc",
                                "COMPONENT_TEMPLATE" => ".default",
                                "EDIT_TEMPLATE" => "",
                                "PATH" => "/include/footer_top.php"
                            )
                        );?>
                        <?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"bottom", 
	array(
		"ALLOW_MULTI_SELECT" => "N",
		"CHILD_MENU_TYPE" => "",
		"COMPONENT_TEMPLATE" => "bottom",
		"DELAY" => "N",
		"MAX_LEVEL" => "1",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_TYPE" => "N",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"ROOT_MENU_TYPE" => "bottom",
		"USE_EXT" => "N"
	),
	false
);?>
						<?$APPLICATION->IncludeComponent(
							"bitrix:main.include",
							"",
							Array(
								"AREA_FILE_SHOW" => "file",
								"AREA_FILE_SUFFIX" => "inc",
								"COMPONENT_TEMPLATE" => ".default",
								"EDIT_TEMPLATE" => "",
								"PATH" => "/include/footer_socnet.php"
							)
						);?>
						<?$APPLICATION->IncludeComponent(
							"bitrix:main.include",
							"",
							Array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => SITE_DIR."include/socnet_sidebar.php",
								"AREA_FILE_RECURSIVE" => "N",
								"EDIT_MODE" => "html",
							),
							false,
							Array('HIDE_ICONS' => 'Y')
						);?>
					</div>
				</div>
				<div class="col-sm-2" style="margin-left:2%;">
					<div class="single-widget">
						<?$APPLICATION->IncludeComponent(
							"bitrix:main.include",
							"",
							Array(
								"AREA_FILE_SHOW" => "file",
								"AREA_FILE_SUFFIX" => "inc",
								"COMPONENT_TEMPLATE" => ".default",
								"EDIT_TEMPLATE" => "",
								"PATH" => "/include/footer_info.php"
							)
						);?>
						<?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"bottom", 
	array(
		"ALLOW_MULTI_SELECT" => "N",
		"CHILD_MENU_TYPE" => "",
		"COMPONENT_TEMPLATE" => "bottom",
		"DELAY" => "N",
		"MAX_LEVEL" => "1",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_TYPE" => "N",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"ROOT_MENU_TYPE" => "bottom1",
		"USE_EXT" => "N"
	),
	false
);?>

					</div>
				</div>
				<div class="col-sm-2">
					<div class="single-widget">
						<?$APPLICATION->IncludeComponent(
							"bitrix:main.include",
							"",
							Array(
								"AREA_FILE_SHOW" => "file",
								"AREA_FILE_SUFFIX" => "inc",
								"COMPONENT_TEMPLATE" => ".default",
								"EDIT_TEMPLATE" => "",
								"PATH" => "/include/footer_user.php"
							)
						);?>
						<?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"bottom", 
	array(
		"ALLOW_MULTI_SELECT" => "N",
		"CHILD_MENU_TYPE" => "",
		"COMPONENT_TEMPLATE" => "bottom",
		"DELAY" => "N",
		"MAX_LEVEL" => "1",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_TYPE" => "N",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"ROOT_MENU_TYPE" => "bottom2",
		"USE_EXT" => "N"
	),
	false
);?>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="single-widget">
						<?$APPLICATION->IncludeComponent(
							"bitrix:main.include",
							"",
							Array(
								"AREA_FILE_SHOW" => "file",
								"AREA_FILE_SUFFIX" => "inc",
								"COMPONENT_TEMPLATE" => ".default",
								"EDIT_TEMPLATE" => "",
								"PATH" => "/include/footer_services.php"
							)
						);?>
						<?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"bottom", 
	array(
		"ALLOW_MULTI_SELECT" => "N",
		"CHILD_MENU_TYPE" => "",
		"COMPONENT_TEMPLATE" => "bottom",
		"DELAY" => "N",
		"MAX_LEVEL" => "1",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_TYPE" => "N",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"ROOT_MENU_TYPE" => "bottom3",
		"USE_EXT" => "N"
	),
	false
);?>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="single-widget">
						<?$APPLICATION->IncludeComponent(
							"bitrix:main.include",
							"",
							Array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => SITE_DIR."include/sender.php",
								"AREA_FILE_RECURSIVE" => "N",
								"EDIT_MODE" => "html",
							),
							false,
							Array('HIDE_ICONS' => 'Y')
						);?>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="footer-bottom">
		<div class="container">
			<div class="row">
				<p class="pull-left">&copy; 2011 - <?php echo date("Y"); ?>, Мастерская Gem</p>
			</div>
		</div>
	</div>

</footer>

<?php //global $APPLICATION;
//
//$canonical = $_SERVER["SERVER_NAME"].''.$APPLICATION->GetCurPage(false);
//$APPLICATION->AddHeadString('<link href="http://'.$canonical.'" rel="canonical" />',true);
//
//if(!empty($_REQUEST["PAGEN_1"])) {
//    $is_title = $APPLICATION->GetProperty("title");
//    if(empty($is_title)){
//        $is_title = $APPLICATION->GetTitle();
//    }
//    $APPLICATION->SetPageProperty('title', $is_title.' - страница '.$_GET["PAGEN_1"]);
//}
//?>


<!-- <div class="col-xs-12 hidden-lg hidden-md hidden-sm">
			<?/*$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket.line",
	".default",
	array(
		"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
		"PATH_TO_PERSONAL" => SITE_DIR."personal/",
		"SHOW_PERSONAL_LINK" => "N",
		"SHOW_NUM_PRODUCTS" => "Y",
		"SHOW_TOTAL_PRICE" => "N",
		"SHOW_PRODUCTS" => "N",
		"POSITION_FIXED" => "N",
		"POSITION_HORIZONTAL" => "center",
		"POSITION_VERTICAL" => "bottom",
		"SHOW_AUTHOR" => "N",
		"PATH_TO_REGISTER" => SITE_DIR."login/",
		"PATH_TO_PROFILE" => SITE_DIR."personal/",
		"COMPONENT_TEMPLATE" => ".default",
		"SHOW_EMPTY_VALUES" => "Y"
	),
	false
);*/?>
		</div> -->




</div> <!-- //bx-wrapper -->

<?/*$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	".default",
	array(
		"AREA_FILE_SHOW" => "file",
		"AREA_FILE_SUFFIX" => "inc",
		"AREA_FILE_RECURSIVE" => "Y",
		"EDIT_TEMPLATE" => "standard.php",
		"COMPONENT_TEMPLATE" => ".default",
		"PATH" => "/include/include_ajax.php"
	),
	false
);*/?>
<script>
	var lastWait = [];
	/* non-xhr loadings */
	BX.showWait = function (node, msg)
	{
		node = BX(node) || document.body || document.documentElement;
		msg = msg || BX.message('JS_CORE_LOADING');

		var container_id = node.id || Math.random();

		var obMsg = node.bxmsg = document.body.appendChild(BX.create('DIV', {
			props: {
				id: 'wait_' + container_id,
				className: 'bx-core-waitwindow'
			},
			text: msg
		}));

		setTimeout(BX.delegate(_adjustWait, node), 10);

		$('#win8_wrapper').show();
		lastWait[lastWait.length] = obMsg;
		return obMsg;
	};

	BX.closeWait = function (node, obMsg)
	{
		$('#win8_wrapper').hide();
		if (node && !obMsg)
			obMsg = node.bxmsg;
		if (node && !obMsg && BX.hasClass(node, 'bx-core-waitwindow'))
			obMsg = node;
		if (node && !obMsg)
			obMsg = BX('wait_' + node.id);
		if (!obMsg)
			obMsg = lastWait.pop();

		if (obMsg && obMsg.parentNode)
		{
			for (var i = 0, len = lastWait.length; i < len; i++)
			{
				if (obMsg == lastWait[i])
				{
					lastWait = BX.util.deleteFromArray(lastWait, i);
					break;
				}
			}

			obMsg.parentNode.removeChild(obMsg);
			if (node)
				node.bxmsg = null;
			BX.cleanNode(obMsg, true);
		}
	};

	function _adjustWait()
	{
		if (!this.bxmsg)
			return;

		var arContainerPos = BX.pos(this),
			div_top = arContainerPos.top;

		if (div_top < BX.GetDocElement().scrollTop)
			div_top = BX.GetDocElement().scrollTop + 5;

		this.bxmsg.style.top = (div_top + 5) + 'px';

		if (this == BX.GetDocElement())
		{
			this.bxmsg.style.right = '5px';
		}
		else
		{
			this.bxmsg.style.left = (arContainerPos.right - this.bxmsg.offsetWidth - 5) + 'px';
		}
	}</script>
<script type="text/javascript" data-skip-moving="true">
/* <![CDATA[ */
var google_conversion_id = 833840251;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script data-skip-moving="true" type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript data-skip-moving="true">
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="https://www.googleads.g.doubleclick.net/pagead/viewthroughconversion/833840251/?guid=ON&amp;script=0"/>
</div>
</noscript>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=926016264224162&ev=PageView&noscript=1"/></noscript>
</body>
</html>