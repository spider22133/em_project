<?php
require __DIR__.'/admin_prefix.php';

IncludeModuleLangFile(__FILE__);

use Vettich\Autoposting\PostingFunc;

$acc = 'Posting';
if(!empty($_GET['acc']) && $_GET['acc'] != $acc)
{
	$_acc = trim($_GET['acc']);
	$_post = PostingFunc::module2($_acc);
	if(method_exists($_post['func'], 'isSupport') && !$_post['func']::isSupport()) {
		require_once ($DOCUMENT_ROOT.BX_ROOT."/modules/main/include/prolog_admin_after.php");

		echo '<span style="color:black">';
		if(method_exists($_post['func'], 'dontSupportMsg'))
			echo $_post['func']::dontSupportMsg();
		else
			echo GetMessage('vettich.autoposting_soc_network_dont_support');
		echo '</span>';
		$APPLICATION->SetTitle(GetMessage('VettichAutopostingMenu_acc_'.$_acc));

		require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
		return;
	} elseif(empty($_post) or empty($_post['option'])) {
		require_once ($DOCUMENT_ROOT.BX_ROOT."/modules/main/include/prolog_admin_after.php");

		$social = json_decode(PostingFunc::_curl_post('http://service.vettich.ru/social/all/get-info-bitrix?social='.$_acc, array(), '', false, 10), true);

		echo '<span style="color:black">';
		if(!!$social) {
			echo GetMessage('vettich.autoposting_soc_network_on_paid', array(
				'#ACC#' => GetMessage('VettichAutopostingMenu_acc_'.$_acc),
				'#PRICE#' => $social['price'],
				'#LINK_PAY#' => $social['link_pay'],
			));
		} else {
			echo GetMessage('vettich.autoposting_soc_network_on_paid_2', array(
				'#ACC#' => GetMessage('VettichAutopostingMenu_acc_'.$_acc),
			));
		}
		echo '</span>';
		$APPLICATION->SetTitle(GetMessage('VettichAutopostingMenu_acc_'.$_acc));

		require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
		return;
	}

	if(PostingFunc::isModule($_acc))
	{
		$acc = $_acc;
	}
}
$post = PostingFunc::module2($acc);


$arFields = array();
if(method_exists($post['option'], 'GetFields'))
	$arFields = $post['option']::GetFields();

$sTableID = "tbl_posts_".$acc;
$oSort = new CAdminSorting($sTableID, "ID", "DESC");
$lAdmin = new CAdminList($sTableID, $oSort);

if($lAdmin->EditAction())
{
	if(method_exists($post['option'], 'SaveFields'))
		$post['option']::SaveFields($FIELDS);
}

if(($arID = $lAdmin->GroupAction()))
{
	if($_REQUEST['action_target']=='selected')
	{
		if(method_exists($post['option'], 'GetIDs'))
			$arID = $post['option']::GetIDs();
	}

	foreach($arID as $ID)
	{
		$ID = IntVal($ID);
		if($ID <= 0)
			continue;
		switch($_REQUEST['action'])
		{
			case "delete":
				if(method_exists($post['option'], 'Delete'))
					$post['option']::Delete($ID);
				break;
		}
	}
}

$aHeaders = array();
$arList = array();

foreach ($arFields as $key => $value)
{
	if(is_array($value))
		$aHeaders[] = array('id'=>$key, 'content'=>$value['content'], 'sort'=>$key, 'default'=>$value['default']);
	else
		$aHeaders[] = array('id' => $key, 'content' => $value, 'sort' => $key, 'default' => true);
}
if(method_exists($post['option'], 'GetList'))
	$arList = $post['option']::GetList(array($by=>$order));

$lAdmin->AddHeaders($aHeaders);

$edit_action_url = "vettich_autoposting_posts_edit.php";
if($acc != 'Posting')
	$edit_action_url = "vettich_autoposting_posts_edit_".$acc.".php";

foreach($arList as $list)
{
	$row =& $lAdmin->AddRow($list['ID'], $list);
	if(method_exists($post['option'], 'ChangeRow'))
		$post['option']::ChangeRow($row);
	$edit_action_url_id = $edit_action_url."?ID=".$list['ID'];

	$row->AddViewField('NAME', '<a href="'.$edit_action_url_id.'">'.$list['NAME'].'</a>');

	$arActions = Array(
		array(
			"ICON"=>"edit",
			"DEFAULT"=>true,
			"TEXT"=>GetMessage("VCH_POSTS_LIST_EDIT"),
			"ACTION"=>'vch_post_go_link("'.$edit_action_url_id.'")',
			// "ACTION"=>$lAdmin->ActionRedirect($edit_action_url_id),
		),
		array(
			"ICON"=>"delete",
			"TEXT"=>GetMessage("VCH_POSTS_LIST_DELETE"),
			"ACTION"=>"if(confirm('".GetMessage("VCH_POSTS_LIST_DELETE_CONF")."')) ".$lAdmin->ActionDoGroup($list['ID'], "delete")
		),
	);
	$row->AddActions($arActions);
}

$lAdmin->AddGroupActionTable(Array(
	"delete"=>true,
));

$link_add = "vettich_autoposting_posts_edit.php";
// $link_add = "vettich_autoposting_posts_edit.php?lang=".LANG;
if($acc != 'Posting')
	$link_add = "vettich_autoposting_posts_edit_".$acc.".php?lang=".LANG;
$aContext = array(
	array(
		"TEXT"=>GetMessage("VCH_POSTS_LIST_ADD"),
		"LINK"=> $link_add,
		"TITLE"=>GetMessage("VCH_POSTS_LIST_ADD_TITLE"),
		"ICON"=>"btn_new",
	),
);
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->CheckListMode();

$pageTitle = '';
if(method_exists($post['option'], 'PageTitle'))
	$pageTitle = $post['option']::PageTitle();
$APPLICATION->SetTitle($pageTitle);

require_once ($DOCUMENT_ROOT.BX_ROOT."/modules/main/include/prolog_admin_after.php");

$lAdmin->DisplayList();

require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>
