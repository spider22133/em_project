<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$APPLICATION->IncludeComponent(
	"bitrix:eshop.socnet.links", 
	"social", 
	array(
		"COMPONENT_TEMPLATE" => "social",
		"FACEBOOK" => "https://www.facebook.com/www.gem.style/",
		"TWITTER" => "",
		"INSTAGRAM" => "https://www.instagram.com/gem.ua/",
		"VKONTAKTE" => "",
		"GOOGLE" => ""
	),
	false,
	array(
		"HIDE_ICONS" => "N"
	)
);?>