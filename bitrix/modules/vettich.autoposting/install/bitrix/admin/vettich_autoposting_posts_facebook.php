<?
if (version_compare(PHP_VERSION, '5.4.0', '<'))
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
	require_once ($DOCUMENT_ROOT.BX_ROOT."/modules/main/include/prolog_admin_after.php");

	echo '<h3>The facebook module does not support php versions below 5.4.0. Current php version '.PHP_VERSION.'</h3>';
	require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
	return;
}

$_GET['acc'] = 'facebook';
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/vettich_autoposting_prefix.php');
require($vettich_autoposting_prefix_dir."/admin/vettich_autoposting_posts.php");
?>
