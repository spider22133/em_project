<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage('VCH_PLUS_TITLE'));

function _curl_get($url)
{
	$result = null;
	if( $curl = curl_init() ) {
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		$result = curl_exec($curl);
		curl_close($curl);
	} else {
		$result = file_get_contents($url);
	}
	return $result;
}

$module_id = 'vettich.autopostingplus';
$error = [];
if(!empty($_POST['license_key'])) {
	$lkey = trim($_POST['license_key']);
	$result = _curl_get('http://web.vettich.dev/pay/license-key-json?license_key='.$lkey);
	if($result) {
		$result = json_decode($result, true);
		if($result['error'] && $result['error'] == 'license key not found') {
			$error[] = GetMessage('VCH_PLUS_ERROR_LK_NOT_FOUND');
		} elseif(!$result['is_pay']) {
			$error[] = GetMessage('VCH_PLUS_ERROR_LK_NOT_PAYED');
		} elseif($result['activated']) {
			$error[] = GetMessage('VCH_PLUS_ERROR_LK_ACTIVED');
		} else {
			$res = _curl_get('http://web.vettich.dev/pay/activate-license-key-json?license_key='.$lkey);
			if($res) {
				$res = json_decode($res, true);
				if($res['result'] == 'ok') {
					COption::SetOptionString('vettich.autoposting', 'plus_license_key', $lkey);
					$showMessageOk = true;
				} elseif($res['error'])
					$error[] = $res['error'];
			}
		}
	}
}

if(!empty($_POST['install'])) {
	$lkey = COption::GetOptionString('vettich.autoposting', 'plus_license_key', '');
	if(empty($lkey))
		$error[] = GetMessage('VCH_PLUS_ERROR_LK_EMPTY');
	else {
		$data = _curl_get('http://web.vettich.dev/product/download?id=4&license_key='.$lkey);
		$file = __DIR__.'/data.zip';
		$udir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module_id;
		if(!is_dir($udir))
			mkdir($udir);
		file_put_contents($file, $data);

		$res = CBXArchive::GetArchive($file);
		$res->SetOptions(array());
		$uRes = $res->Unpack($udir);
		unlink($file);

		if($uRes) {
			LocalRedirect('/bitrix/admin/partner_modules.php?id='.$module_id.'&lang=ru&install=Y&sessid='.bitrix_sessid());
			exit;
		} else {
			$error[] = GetMessage('VCH_PLUS_ERROR_UNZIP');
			$error[] = print_r($res->GetErrors(), true);
		}
	}
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$serr = implode("\n", $error);
if(!empty($serr)) {
	ShowError($serr);
}
if($showMessageOk)
	ShowMessage(array('TYPE'=>'OK', 'MESSAGE'=> GetMessage('VCH_PLUS_LK_ACTIVATED')));
?>
<div class="adm-detail-content-item-block">
<?=GetMessage('VCH_PLUS_TEXT_1');?>
<br><br>
<div style="display:inline-block; float:left; padding:20px">
	<?
	$optLKey = COption::GetOptionString('vettich.autoposting', 'plus_license_key', '');
	if(empty($optLKey)):
		?>
		<form action="" method="post">
			<b><?=GetMessage('VCH_PLUS_LK_ENTER')?></b>
			<br>
			<input type="text" name="license_key" placeholder="<?=GetMessage('VCH_PLUS_INPUT_LK_PH')?>" size="42" value="<?=@$_POST['license_key']?>">
			<br><br>
			<input type="submit" value="<?=GetMessage('VCH_PLUS_INPUT_LK_ACTIVATED')?>">
			<br>
		</form>
		<?
	else:
		?>
		<b><?=GetMessage('VCH_PLUS_LK')?></b><br>
		<input type="text" name="license_key" size="42" disabled="disabled" value="<?=$optLKey?>"><br><br>
		<form action="" method="post">
			<input type="hidden" name="install" value="Y">
			<?$params = IsModuleInstalled($module_id) ? ' disabled="disabled"' : '';?>
			<input type="submit" value="<?=GetMessage('VCH_PLUS_INPUT_LK_INSTALL')?>" class="adm-btn-save" <?=$params?>><br>
		</form>
		<?
	endif;
	?>
</div>
	<?
	$params = '';
	if($p = $USER->GetFullName())
		$params .= '&customer_name='.$p;
	if($p = $USER->GetEmail())
		$params .= '&customer_email='.$p;
	?>
	<?=GetMessage('VCH_PLUS_TEXT_2', array('#PARAMS#' => $params))?>
<div style="clear:both"></div>
</div>
<?
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
