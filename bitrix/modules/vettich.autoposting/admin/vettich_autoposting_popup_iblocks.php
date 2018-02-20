<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);

CModule::IncludeModule('vettich.autoposting');
use Vettich\Autoposting as V;
$arModuleParams = V\PostingOption::GetArModuleParamsPopup(intval($_GET['IBLOCK_ID']));

?><script type="text/javascript">
function cloze(){window.close();}
</script><?

if(isset($_POST['Save']) && trim($_POST['Save']) != '') // "Send" submit
{
	if(!empty($_POST['popup_a']) && $_POST['popup_a'] == 'Y')
	{
		$arPosts = array();
		if(!empty($_POST['viblock_posts']))
			$arPosts = $_POST['viblock_posts'];
		COption::SetOptionString('vettich.autoposting', 'viblock_posts', serialize($arPosts));
		$param = array('ids' => $arPosts);
	}
	else
	{
		V\PostingOption::SaveParamsPopup();
		$arPosts = V\PostingFunc::GetValues(V\PostingOption::GetPopupID());
		$param = array('post' => $arPosts);
	}
	if(!empty($arPosts))
	{
		$arFilter = array(
			'IBLOCK_ID' => $_REQUEST['IBLOCK_ID'],
			'ACTIVE'=>'Y',
		);
		if(!empty($_GET['ELEM_ID']))
			$arFilter['ID'] = $_GET['ELEM_ID'];
		elseif(!empty($_GET['SECTION_ID']))
			$arFilter['SECTION_ID'] = $_GET['SECTION_ID'];
		else
			die(GetMessage('VCH_POST_IBLOCK_ERROR_EMPTY_IDS'));
		$rsElems = CIBlockElement::GetList(array(), $arFilter);
		while($arElem = $rsElems->GetNext())
		{
			V\Posting::ElementPost($arElem, 'eventPopupIBlocksPublication', $param);
		}
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");
		?><div class="adm-detail-content-item-block"><?
		echo GetMessage('VCH_POST_IBLOCK_SUCCESS');
		?></div>
		<button onclick="cloze()" class="adm-btn"><?=GetMessage('VCH_POST_IBLOCK_BUTTON_CLOSE')?></button>
		<?
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");
	}
	else
	{
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");
		?><div class="adm-detail-content-item-block"><?
		echo GetMessage('VCH_POST_IBLOCK_NOT_CHOSE');
		?></div>
		<button onclick="cloze()" class="adm-btn"><?=GetMessage('VCH_POST_IBLOCK_BUTTON_CLOSE')?></button>
		<?
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");
	}
	exit;
}
if(isset($_POST['Apply']) && trim($_POST['Apply']) != '') // "Cancel" submit
{
	?>
	<script type="text/javascript">cloze()</script>
	<?
	exit;
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");
$vopt_b = new VOptions();
$vopt_b->init_module_params();

if(!empty($_GET['ELEM_ID']))
{
	$rsElem = CIBlockElement::GetByID($_GET['ELEM_ID']);
	$arElem = $rsElem->GetNext();
	echo '<h1 class="adm-title">';
	echo GetMessage('VCH_POST_IBLOCK_TITLE', array('#NAME#'=>$arElem['NAME']));
	echo '</h1>';
}
elseif(!empty($_GET['SECTION_ID']))
{
	$rsElem = CIBlockSection::GetByID($_GET['SECTION_ID']);
	$arElem = $rsElem->GetNext();
	echo '<h1 class="adm-title">';
	echo GetMessage('VCH_POST_SECTION_IBLOCK_TITLE', array('#NAME#'=>$arElem['NAME']));
	echo '</h1>';
}
?>
<label for="input_ab" style="cursor:pointer">
	<div class="adm-detail-content-item-block">
		<?=GetMessage('VCH_POST_IBLOCK_CHECK')?>
		<input type="checkbox" id="input_ab" checked="checked" onchange="f_input_ab()">
	</div>
</label>
<div id="div_a">
	<?
	$arPosts = array();
	foreach(V\PostingOption::GetList(array('ID'), array('ID', 'NAME')) as $post)
		$arPosts[$post['ID']] = $post['NAME'];

	if(empty($arPosts))
	{
		?><div class="adm-detail-content-item-block"><?
		echo GetMessage('VCH_POST_IBLOCK_NOT_POSTS');
		?></div><?
	}
	else
	{
		$arModuleParams['TABS'] = array(
			'TAB2' => array(
				'NAME' => GetMessage('VCH_POST_IBLOCK_PARAM_NAME'),
				'TITLE' => GetMessage('VCH_POST_IBLOCK_PARAM_TITLE'),
			)
		);
		$arModuleParams['TAB_CONTROL_POSTFIX'] = 'popup_a';
		$prefix = 'viblock_post_';
		$arModuleParams['PARAMS'] = array();
		$arModuleParams['PARAMS']['popup_a'] = array(
			'TAB' => 'TAB2',
			'TYPE' => 'HIDDEN',
			'VALUE' => 'Y'
		);
		$arValue = unserialize(COption::GetOptionString('vettich.autoposting', 'viblock_posts', false));
		if(!$arValue)
			$arValue = array();
		$arModuleParams['PARAMS']['viblock_posts'] = array(
			'TAB' => 'TAB2',
			'TYPE' => 'CHECKBOXLIST',
			'NAME' => $name,
			'VALUES' => $arPosts,
			'VALUE' => $arValue,
		);
		$vopt_a = new VOptions();
		$vopt_a->init_module_params();
		$vopt_a->show();
	}
	?>
</div>
<div id="div_b" style="display:none">
	<?
	$vopt_b->show();
	?>
</div>
<script type="text/javascript">
function f_input_ab()
{
	$input_ab = $('#input_ab:checked');
	if($input_ab.length)
	{
		$('#div_a').show();
		$('#div_b').hide();
	}
	else
	{
		$('#div_b').show();
		$('#div_a').hide();
	}
}
</script>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");

