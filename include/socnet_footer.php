<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$APPLICATION->IncludeComponent(
	"bitrix:eshop.socnet.links",
	"big_squares",
	array(
		"FACEBOOK" => "https://www.facebook.com/",
		"VKONTAKTE" => "https://vk.com/",
		"GOOGLE" => "https://plus.google.com/",
	),
	false,
	array(
		"HIDE_ICONS" => "N"
	)
);?>