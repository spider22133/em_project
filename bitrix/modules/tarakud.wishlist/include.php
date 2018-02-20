<?
use Bitrix\Main\Loader;

$arClasses = array(
	"Tarakud\\Wishlist\\Wishlist" => "lib/wishlist.php",
);

Loader::registerAutoLoadClasses("tarakud.wishlist", $arClasses);

?>
