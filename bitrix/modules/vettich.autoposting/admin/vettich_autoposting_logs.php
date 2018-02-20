<?
require __DIR__.'/admin_prefix.php';
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
IncludeModuleLangFile(__FILE__);

CAdminNotify::DeleteByTag('ERRORS_LOG');

use Vettich\Autoposting as V;
$logs_id = V\PostingFunc::__GetPosts();
$APPLICATION->SetTitle(GetMessage('LOG_PAGE_TITLE'));

if(isset($_POST['form_post']) && $_POST['form_post'] == 'Y')
{
	$log = trim($_POST['log_id']);
	if(!empty($log) && (in_array($log, $logs_id) or $log == 'all'))
	{
		V\PostingLogs::clearLogs($log);
	}
}

$arTabs = array(
	array(
		'DIV' => 'TABall',
		'TAB' => GetMessage('TAB1'),
		'TITLE' => GetMessage('TAB1_TITLE'),
		'LOG' => 'all',
	)
);

foreach($logs_id as $log_id)
{
	if($log_id == 'all')
		continue;
	if(V\PostingFunc::isModule($log_id))
	{
		$arPost = V\PostingFunc::module2($log_id);
		if(method_exists($arPost['func'], 'get_name'))
		{
			$arTabs[] = array(
				'DIV' => 'TAB'.$log_id,
				'TAB' => $arPost['func']::get_name(),
				'TITLE' => GetMessage('LOG_TITLE', array('#LOG_ID#'=>$arPost['func']::get_name())),
				'LOG' => $log_id,
			);
		}
	}
}

$tabControl = new CAdminTabControl("tabControlLogs", $arTabs, true, true);
$tabControl->Begin();

?>
<form action="" method="post">
<input type="hidden" name="form_post" value="Y">
<? 
foreach($arTabs as $arTab)
{
	$tabControl->BeginNextTab();
	$pagen = $_REQUEST['PAGEN_'.$arTab['LOG']] ?: 1;
	$pageitems = $_REQUEST['PAGEITEMS_'.$arTab['LOG']] ?: 20;
	$logs = V\PostingLogs::getLogs($arTab['LOG'], $pagen, $pageitems);

	if(empty($logs['VALUES']))
	{
		?>
		<tr>
			<td>
				<div class="log-empty">
					<?=GetMessage('LOG_EMPTY')?>
				</div>
			</td>
		</tr>
		<?
	}
	else
	{
		$navResult = new CDBResult();
		$navResult->NavPageCount = ceil($logs['TOTAL_COUNT'] / $pageitems);
		$navResult->NavPageNomer = $pagen;
		$navResult->NavNum = $arTab['LOG'];
		$navResult->NavPageSize = $pageitems;
		$navResult->NavRecordCount = $logs['TOTAL_COUNT'];

		?>
		<div style="padding:3px">
			<button class="adm-btn adm-btn-delete" onclick="ajaxClearLog('<?=$arTab['LOG']?>', '<?=$arTab['TAB']?>');return false" name="log_id" value="<?=$arTab['LOG']?>">
				<?=GetMessage('VCH_LOGS_CLEAR_BTN')?>
			</button>
		</div>
		<?
		$url = '?tabControlLogs_active_tab=TAB'.$arTab['LOG'];
		$APPLICATION->IncludeComponent(
			'bitrix:system.pagenavigation',
			'arrows_adm',
			array(
				 'NAV_RESULT' => $navResult,
				 'BASE_LINK' => $url,
			),
			false
		);

		foreach($logs['VALUES'] as $log)
		{
			?>
			<tr>
				<td>
					<div class="log_<?=strtolower($log['TYPE'])?>">
						<div class="log_date"><?=$log['DATETIME']?></div>
						<div class="log_text"><?=$log['TEXT']?></div>
					</div>
				</td>
			</tr>
			<?
		}
	}
}
?>
</form>

<style type="text/css">
	.log_error{
		padding: 10px;
		background-color: rgba(213, 84, 84, 0.9);
	}

	.log_success{
		padding: 10px;
		background-color: rgba(136, 216, 142, 0.9);
	}

	.log_warning{
		padding: 10px;
		background-color: rgba(231, 208, 82, 0.9);
	}

	.log_date{
		padding:1px;
		font-size: 10px;
	}

	.log_text{
		padding:1px;
		font-size: 14px;
	}
</style>
<script type="text/javascript">
var a_d = '';
function ajaxClearLog(log_id, log_name)
{
	if(!confirm('<?=GetMessage('CLEAR_LOG_CONFIRM')?> "' + log_name + '"?'))
		return;

	data = {
		'form_post':'Y',
		'log_id':log_id
	};
	BX.showWait('TAB'+log_id);
	$.post(
		'<?=$APPLICATION->GetCurUri('ajax=Y');?>',
		data,
		function(d){
			BX.closeWait('TAB'+log_id);
			a_d = d;
			dd = $(d).find('#TAB'+log_id);
			container = $('#TAB'+log_id);
			container.html(dd.html());
		}
	);
}
</script>
<?
$tabControl->End();
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
