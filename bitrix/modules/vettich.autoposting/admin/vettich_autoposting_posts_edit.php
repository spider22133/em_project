<?
require __DIR__.'/admin_prefix.php';
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

use Vettich\Autoposting\PostingFunc;

$ID = -1;
if(isset($_GET['ID']) && ((int)$_GET['ID']) >= 0)
{
	$ID = (int)$_GET['ID'];
}

$acc = 'Posting';
if(isset($_GET['acc']) && !empty($_GET['acc']) && $_GET['acc'] != 'Posting')
{
	$_acc = trim($_GET['acc']);
	if(PostingFunc::isModule($_acc))
		$acc = $_acc;
}
$post = PostingFunc::module2($acc);
$module_id = 'vettich.autoposting';

$back_link = "vettich_autoposting_posts.php";
$edit_link = "vettich_autoposting_posts_edit.php";
if($acc != 'Posting')
{
	$back_link = "vettich_autoposting_posts_$acc.php";
	$edit_link = "vettich_autoposting_posts_edit_$acc.php";
}

$aMenu = array(
	array(
		"TEXT"=>GetMessage("VCH_POSTS_BACK_LIST"),
		"TITLE"=>GetMessage("VCH_POSTS_BACK_LIST_TITLE"),
		"LINK"=>$back_link."?lang=".LANG,
		"ICON"=>"btn_list",
	)
);
if($ID>0)
{

	$aMenu[] = array("SEPARATOR"=>"Y");
	$aMenu[] = array(
		"TEXT"=>GetMessage("VCH_POSTS_EDIT_ADD"),
		"TITLE"=>GetMessage("VCH_POSTS_EDIT_ADD_TITLE"),
		"LINK"=>$edit_link."?lang=".LANG,
		"ICON"=>"btn_new",
	);
	$aMenu[] = array(
		"TEXT"=>GetMessage("VCH_POSTS_EDIT_DEL"),
		"TITLE"=>GetMessage("VCH_POSTS_EDIT_DEL_TITLE"),
		"LINK"=>"javascript:if(confirm('".GetMessage("VCH_POSTS_EDIT_DEL_CONF")."')) window.location='".$back_link."?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."';",
		"ICON"=>"btn_delete",
	);
}
$context = new CAdminContextMenu($aMenu);
$context->Show();



if(method_exists($post['option'], 'GetArModuleParams'))
	$arModuleParams = $post['option']::GetArModuleParams($ID);
if(method_exists($post['option'], 'SaveParams'))
	$post['option']::SaveParams($ID);

if(method_exists($post['option'], 'EditPageTitle'))
	$APPLICATION->SetTitle($post['option']::EditPageTitle($ID));

$vopt = new VOptions();

$vopt->init_module_params();
$vopt->show();
?>
