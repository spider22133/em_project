<?                                                
/**
 * Module settings.
 */

/*
 * Include some standard language constants.
 */
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$module_id = "justdevelop.morder";
$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);

if($POST_RIGHT >= "R"):
	if(CModule::IncludeModule("sale")) {
		$db_props = CSaleOrderProps::GetList();
		$arSaleProps = array();
		$arReplaces = array();
		while($arProps = $db_props->Fetch()) {
			if((!isset($arSaleProps[$arProps['CODE']]) || !in_array($arProps['NAME'],$arSaleProps[$arProps['CODE']])) && strlen($arProps['CODE'])>0) $arSaleProps[$arProps['CODE']][] = $arProps['NAME'];
			if((!isset($arReplaces['PROP_'.$arProps['CODE']]) || !in_array($arProps['NAME'],$arReplaces['PROP_'.$arProps['CODE']])) && strlen($arProps['CODE'])>0) $arReplaces['PROP_'.$arProps['CODE']][] = $arProps['NAME'];
		}
		
		$obStatus = CSaleStatus::GetList();
		$arStatus = array();
		while($arStat = $obStatus->Fetch()) {
			$arStatus[$arStat['ID']][$arStat['LID']] = array(
				'NAME'		=> $arStat['NAME'],
				'DESCRIPTION'	=> $arStat['DESCRIPTION']
			);
		}
		$obSite = CSite::GetList($by="sort", $order="desc");
		$arSites = array();
		while($arResult = $obSite->Fetch()) {
			$arSites[$arResult['ID']] = $arResult['NAME'];
		}
		$arFields = Array(
			"ENTITY_ID" => "USER",
			"USER_TYPE_ID" => "string",
			'LANG' => LANGUAGE_ID
		);
		
		$obUserFields = CUserTypeEntity::GetList( array($by=>$order), $arFields );
		$arUserFields = array();
		while($arRes = $obUserFields->Fetch())
		{
			$arUserFields[] = $arRes;
		}
		$arGroups = array();
		$obGroups = CGroup::GetList(($by="c_sort"), ($order="desc") );
		while($arGr = $obGroups->Fetch()) {
			$arGroups[$arGr['ID']] = $arGr['NAME'];
		}
		
	}
	require_once dirname(__FILE__).'/classes/general/JUSTDEVELOP_Send.php';
	
	$tabs = array(
			array(
				"DIV"   => 'shop',
				"TAB"   => GetMessage("JUSTDEVELOP_SMS_INTERNET_MAGAZIN"),
				"ICON"  => '',
				"TITLE" => GetMessage("JUSTDEVELOP_SMS_NASTROYKI_INTERNET_M")
			),
			array(
				"DIV"   => 'gate',
				"TAB"   => GetMessage("JUSTDEVELOP_SMS_GATE_OPTIONS"),
				"ICON"  => '',
				"TITLE" => GetMessage("JUSTDEVELOP_SMS_GATE_OPTIONS")
			),
			array(
				"DIV"   => 'rights',
				"TAB"   => GetMessage("JUSTDEVELOP_SMS_DOSTUP"),
				"ICON"  => '',
				"TITLE" => GetMessage("JUSTDEVELOP_SMS_NASTROYKI_DOSTUPA")
			),
	);
	$tabControl = new CAdminTabControl("JUSTDEVELOPSettings", $tabs);
	
	if($REQUEST_METHOD == "POST" && strlen($Update.$Apply.$RestoreDefaults) > 0 && $POST_RIGHT == "W" && check_bitrix_sessid()){
		
		$enc_value = \Bitrix\Main\Web\Json::encode($_POST["sender"], $options = null);
		COption::SetOptionString('justdevelop.morder', "sender", $enc_value);
		
		if(strlen($RestoreDefaults) > 0){
			COption::RemoveOption("justdevelop.morder");
			$z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
			while($zr = $z->Fetch()){
				$APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
			}	
		}

		$Update = $Update.$Apply;
		ob_start();
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
		ob_end_clean();
		
		foreach ($_POST['settings'] as $settingName => $settingValue) {
			if(substr($settingName,0,strlen('user_groups_iskl')) == 'user_groups_iskl') continue;
			COption::SetOptionString('justdevelop.morder', $settingName, $settingValue);
		}
		
		foreach($arGroups as $id => $name) {
			if(isset($_POST['settings']['user_groups_iskl'.$id])) {
				COption::SetOptionString('justdevelop.morder', 'user_groups_iskl'.$id, '1');
			} else {
				COption::SetOptionString('justdevelop.morder', 'user_groups_iskl'.$id, '0');
			}
		}
		
		if(!isset($_POST['settings']['tf'])) {
			COption::SetOptionString('justdevelop.morder', 'tf', '0');
		} else {
			COption::SetOptionString('justdevelop.morder', 'tf', '1');
		}
	    
		if(strlen($_REQUEST["back_url_settings"]) > 0){
            if(strlen($Apply) > 0 || strlen($RestoreDefaults) > 0){
				 LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
            }
            else{
				LocalRedirect($_REQUEST["back_url_settings"]);
            }   
        }
        else{
            LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&".$tabControl->ActiveTabParam());
        }
	}
	
	$tabControl->Begin();?>
	<form
		name="JUSTDEVELOPSettingsForm"
		method="post"
		action="<?=$GLOBALS['APPLICATION']->GetCurPage() ?>?mid=<?=urlencode($mid) ?>&amp;lang=<?=LANGUAGE_ID ?>">
		
		<style>
			.JUSTDEVELOP-description p {
				font-size: 100% !important;
				padding: 0 0 0 30px;
			}
			
			.JUSTDEVELOP-description-2 p, .JUSTDEVELOP-description-2 ul {
				font-size: 120% !important;
				/*padding: 0 0 0 30px;*/
			}
			.code {
				color: blue;
			}
			.font-sz100 code {
				font-size: 160% !important;
			}
			.JUSTDEVELOP-description .heading {
				font-size: 120%;
				font-weight: bold;
				padding: 10px 0;
			}
			.JUSTDEVELOP-description li {font-size: 100% !important;}
			.toggle-link {
				text-decoration:none;
				border-bottom:2px dotted #000;
			}
			.tal-right {
				text-align: right !important;
			}
			.JUSTDEVELOP-medium {
				font-size: 100%;
			}
		</style>
		<? $jsdir = '/bitrix/js/justdevelop.morder/js';?>
	
		<script src="<?=$jsdir?>/jquery-1.6.4.min.js"></script>
		
		<script>	
				function getstartbot(){
				$.ajax({
				    url: "/bitrix/admin/justdevelop.morder_startbot_.php",
				    method: "GET",
				    data: {"start":"Y"},
				    success: function(data){}
				    });
				
				}

				$(document).ready(function(){
					 setInterval('getstartbot()',10000);
				})

			function changeSite(val) {
				$('.sites').hide();
				document.getElementById('site'+val).style.display='';
			}

			window.onload = function(){changeSite($('#JUSTDEVELOPsite').val())}

		</script>
		
	<script type="text/javascript">

function settingsAddChat(a)
{
	var row = BX.findParent(a, { 'tag': 'tr'});
	var tbl = row.parentNode;

	var tableRow = tbl.rows[row.rowIndex-1].cloneNode(true);
	tbl.insertBefore(tableRow, row);

	var selRights = BX.findChild(tableRow.cells[1], { 'tag': 'select'}, true);
	selRights.selectedIndex = 0;

	selGroups = BX.findChild(tableRow.cells[0], { 'tag': 'select'}, true);
	selGroups.selectedIndex = 0;

	selSites = BX.findChild(tableRow.cells[2], { 'tag': 'select'}, true);
	selSites.selectedIndex = 0;
	
	selSiteSpan = BX.findChild(tableRow.cells[2], { 'tag': 'span'}, true);
	selSiteSpan.style.display = "none";	
	
	RightsRowNew = new BX.CRightsRowNew({'row': tableRow});

	BX.bind(selRights, "change", BX.delegate(RightsRowNew.ChangeSite, RightsRowNew));
}


</script>

	<? $tabControl->BeginNextTab() ?>
		<tr>
			<td colspan="2">     	
			<div class="tab-text"><?=GetMessage("JUSTDEVELOP_SMS_VOZMOJNOSTI")?></div>
			</td>
		</tr>
		<tr class="heading">
			<td colspan="2"><?=GetMessage("JUSTDEVELOP_SMS_OBSIE_NASTROYKI")?></td>
		</tr>
		<tr>
			<td width="50%"><?=GetMessage("JUSTDEVELOP_SMS_SAYT")?></td>
			<td width="50%">
				<select name="settings[site]" id='JUSTDEVELOPsite' onchange='javascript:changeSite(this.value);'>
				<? foreach($arSites as $id => $value):?>
					<option value="<?=$id?>" <?=((COption::GetOptionString('justdevelop.morder', 'site') == $id))?' selected="selected"':''?>><?=$value?></option>
				<? endforeach;?>
				</select>
			</td>
		</tr>
		<? if(CModule::IncludeModule("sale")) :?>

			<tr id="add-field"  class="heading">
				
				<td width="50%" >
					<div class="tal-right">
						<?=GetMessage("JUSTDEVELOP_SMS_TEKSTA_SOOBSENIY")?><br/><br/>
						<strong>#ORDER_NUMBER#</strong> - <?=GetMessage("JUSTDEVELOP_SMS_ORDER_NUMBER")?><br/>
						<strong>#ORDER_SUMM#</strong> - <?=GetMessage("JUSTDEVELOP_SMS_ORDER_SUMM")?><br/>
						<strong>#PRICE_DELIVERY#</strong> - <?=GetMessage("JUSTDEVELOP_SMS_PRICE_DELIVERY")?><br/>
						<strong>#PRICE#</strong> - <?=GetMessage("JUSTDEVELOP_SMS_PRICE")?><br/>
						<strong>#ITEMS#</strong> - <?=GetMessage("JUSTDEVELOP_SMS_ITEMS")?><br/>
						<strong>#STATUS_NAME#</strong> - <?=GetMessage("JUSTDEVELOP_SMS_STATUS_ZAKAZA")?><br/>
						<strong>#DELIVERY_NAME#</strong> - <?=GetMessage("JUSTDEVELOP_SMS_NAZVANIE_SLUJBY_DOST")?><br/>
						<strong>#PAY_SYSTEM#</strong> - <?=GetMessage("JUSTDEVELOP_SMS_NAZVANIE_PAY_SYSTEM")?><br/>
						<strong>#DELIVERY_DOC_NUM#</strong> - <?=GetMessage("JUSTDEVELOP_SMS_NOMER_DOKUMENTA_OTGR")?><br/>
						<strong>#DELIVERY_DOC_DATE#</strong> - <?=GetMessage("JUSTDEVELOP_SMS_DATA_DOKUMENTA_OTGRU")?><br/>
						<strong>#USER_ID#</strong> - <?=GetMessage("JUSTDEVELOP_SMS_USER_ID")?><br/>
						<strong>#DATE_INSERT#</strong> - <?=GetMessage("JUSTDEVELOP_SMS_DATE_INSERT")?><br/>
						<? foreach($arReplaces as $code => $name):?>
							<strong>#<?=$code?>#</strong> - <?=implode(' | ',$name)?><br/>
						<? endforeach;?><br/><br/>
						<?=GetMessage("JUSTDEVELOP_SMS_UBEDITESQ_CTO_U_VSE")?><a href="/bitrix/admin/sale_order_props.php?lang=ru"><?=GetMessage("JUSTDEVELOP_SMS_SVOYSTV_ZAKAZA")?></a> <?=GetMessage("JUSTDEVELOP_SMS_ESTQ_MNEMONICESKIY")?>".
					</div>
				</td>
				<td width="50%">&nbsp</td>
			</tr>

			<tr class="heading">
				<td colspan="2"><?=GetMessage("JUSTDEVELOP_SMS_DOPOLNITELQNYE_SABLO")?></td>
			</tr>
		<? endif?>

	<?
		$strId = COption::GetOptionString('justdevelop.morder', 'sender');
		if(strlen($strId) > 0)
			$arIDchat = \Bitrix\Main\Web\Json::decode($strId);
		if(strlen($arIDchat["id"][0]) > 3):
			foreach($arIDchat["id"] as $key => $item):
				if(strlen($item) <= 0)
					continue;
		?>
			<tr>
				<td class="adm-detail-content-cell-l">
					<?=GetMessage("JUSTDEVELOP_SMS_CHAT_ID")?>
					<input name="sender[id][]" value="<?=$item?>">
				</td>
	
				<td class="adm-detail-content-cell-r">
					<?=GetMessage("JUSTDEVELOP_SMS_CHAT_MSG")?>
					<input name="sender[msg][]" value="<?=$arIDchat["msg"][$key]?>">
				</td>
				<td width="0%"></td>
			</tr>
			<?endforeach;
		else:?>
		<tr>
				<td class="adm-detail-content-cell-l">
					<?=GetMessage("JUSTDEVELOP_SMS_CHAT_ID")?>
					<input name="sender[id][]" value="">
				</td>
	
				<td class="adm-detail-content-cell-r">
					<?=GetMessage("JUSTDEVELOP_SMS_CHAT_MSG")?>
					<input name="sender[msg][]" value="">
				</td>
				<td width="0%"></td>
			</tr>
		<?endif;?>
		<tr>
			<td class="adm-detail-content-cell-l"></td>
			<td style="padding-bottom:10px;" class="adm-detail-content-cell-r">
				<a href="javascript:void(0)" onclick="settingsAddChat(this)" hidefocus="true" class="adm-btn"><?=GetMessage("JUSTDEVELOP_SMS_CHAT_NEW")?></a>
			</td>
			<td></td>
			<td></td>
		</tr>
	
		<? foreach($arSites as $id => $value):?>
		<tr id="site<?=$id?>" <?=(COption::GetOptionString('justdevelop.morder', 'site')==$id)?'':(count($arsites)==1 ? 'style="display:none"': '')?> class="sites">
			<td colspan="2">
			<? if(CModule::IncludeModule("sale")) :?>
				<table class="edit-table">
					<tr class="heading">
						<td colspan="2"><?=GetMessage("JUSTDEVELOP_SMS_DOPOLNITELQNOE_SOOBS")?></td>
					</tr>
					<tr id="add_phone_new<?=$id?>" >
						<td width="50%" class="field-name"><?=GetMessage("JUSTDEVELOP_SMS_TELEFON")?></td>
						<td width="50%">
						    <input
							    type="text"
							    size="50"
							    name="settings[add_phone_new<?=$id?>]"
							    value="<?=COption::GetOptionString('justdevelop.morder', 'add_phone_new'.$id) ?>"/>
						</td>
					</tr>
					<tr>
						<td width="50%" class="field-name"><strong><?=GetMessage("JUSTDEVELOP_SMS_SOOBSENIE")?></strong></td>
						<td width="50%">
							<?if(strlen(COption::GetOptionString('justdevelop.morder', 'new_order'.$id)) <= 0)
								$strNewOrder = GetMessage("JUSTDEVELOP_NEW_ORDER");
							else
								$strNewOrder = COption::GetOptionString('justdevelop.morder', 'new_order'.$id)
							?>
							<textarea name="settings[new_order<?=$id?>]" cols="90" rows="9" wrap="SOFT"><?=$strNewOrder?></textarea>
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><?=GetMessage("JUSTDEVELOP_SMS_DOPOLNITELQNOE_SOOBS1")?></td>
					</tr>
					<tr id="add_phone_pay<?=$id?>" >
						<td width="50%" class="field-name"><?=GetMessage("JUSTDEVELOP_SMS_TELEFON")?></td>
						<td width="50%">
							<input
								type="text"
								size="50"
								name="settings[add_phone_pay<?=$id?>]"
								value="<?=COption::GetOptionString('justdevelop.morder', 'add_phone_pay'.$id)?>"/>
						</td>
					</tr>
					<tr>
						<td width="50%" class="field-name"><strong><?=GetMessage("JUSTDEVELOP_SMS_SOOBSENIE")?></strong></td>
						<td width="50%">
							<?if(strlen(COption::GetOptionString('justdevelop.morder', 'on_pay_order'.$id)) <= 0)
								$strPAYOrder = GetMessage("JUSTDEVELOP_PAY_ORDER");
							else
								$strPAYOrder = COption::GetOptionString('justdevelop.morder', 'on_pay_order'.$id)
							?>
						    <textarea name="settings[on_pay_order<?=$id?>]" cols="90" rows="9" wrap="SOFT"><?=$strPAYOrder?></textarea>
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><?=GetMessage("JUSTDEVELOP_SMS_DOPOLNITELQNOE_SOOBS2")?></td>
					</tr>
					<tr id="add_phone_cancel<?=$id?>" >
						<td width="50%" class="field-name"><?=GetMessage("JUSTDEVELOP_SMS_TELEFON")?></td>
						<td width="50%">
						    <input
							    type="text"
							    size="50"
							    name="settings[add_phone_cancel<?=$id?>]"
							    value="<?=COption::GetOptionString('justdevelop.morder', 'add_phone_cancel'.$id) ?>"/>
						</td>
					</tr>
					<tr>
						<td width="50%" class="field-name"><strong><?=GetMessage("JUSTDEVELOP_SMS_SOOBSENIE")?></strong></td>
						<td width="50%">
						    <textarea name="settings[order_cancel<?=$id?>]" cols="90" rows="9" wrap="SOFT"><?=COption::GetOptionString('justdevelop.morder', 'order_cancel'.$id) ?></textarea>
						</td>
					</tr>
					<tr class="heading">
						<td colspan="2"><?=GetMessage("JUSTDEVELOP_SMS_DOPOLNITELQNOE_SOOBS3")?></td>
					</tr>
					<tr id="add_phone_delivery<?=$id?>" >
						<td width="50%" class="field-name"><?=GetMessage("JUSTDEVELOP_SMS_TELEFON")?></td>
						<td width="50%">
						    <input
							    type="text"
							    size="50"
							    name="settings[add_phone_delivery<?=$id?>]"
							    value="<?=COption::GetOptionString('justdevelop.morder', 'add_phone_delivery'.$id) ?>"/>
						</td>
					</tr>
					<tr>
						<td width="50%" class="field-name"><strong><?=GetMessage("JUSTDEVELOP_SMS_SOOBSENIE")?></strong></td>
						<td width="50%">
						    <textarea name="settings[order_delivery<?=$id?>]" cols="90" rows="9" wrap="SOFT"><?=COption::GetOptionString('justdevelop.morder', 'order_delivery'.$id) ?></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<!--<tr class="heading" id="statuses<?=$id?>" >
						<td colspan="2"><?=GetMessage("JUSTDEVELOP_SMS_DOPOLNITELQNOE_SOOBS4")?></td>
					</tr>
					<? foreach($arStatus as $statId => $val):?>
					<?
						$desc = $val[LANGUAGE_ID];
					?>
						<tr class="heading"  id="main_status_dop_<?=$statId?><?=$id?>" >
							<td colspan="2"><?=GetMessage("SMCS_DOPOLNITELQNOE_SOOBS5")?><?=$desc['NAME']?></td>
						</tr>
						<tr id="add_phone_status_<?=$statId?><?=$id?>" >
							<td width="50%" class="field-name"><?=GetMessage("JUSTDEVELOP_SMS_TELEFON")?></td>
							<td width="50%">
							    <input
								    type="text"
								    size="50"
								    name="settings[add_phone_status_<?=$statId?><?=$id?>]"
								    value="<?=COption::GetOptionString('justdevelop.morder', 'add_phone_status_'.$statId.$id) ?>"/>
							</td>
						</tr>
						<tr id="main_status_<?=$statId?><?=$id?>" >
							<td width="50%"><strong><?=$desc['NAME']?></strong></td>
							<td width="50%">
								<textarea name="settings[status_<?=$statId?><?=$id?>]" cols="90" rows="9" wrap="SOFT"><?=COption::GetOptionString('justdevelop.morder', 'status_'.$statId.$id)?></textarea>
							</td>
						</tr>
					<? endforeach;?>
				-->
				</td>
				</table>
				<? endif?>
			</tr>
		<? endforeach;?>
		<tr>
				<td colspan="2">
					<span style="color: red;"><?=GetMessage("JUSTDEVELOP_SMS_PRIMECHANIE")?></span>
				</td>
			</tr>
		<? if(!CModule::IncludeModule("sale")):?>
			<tr>
				<td colspan="2" style="font-size: 14px; color: red;"><?=GetMessage("JUSTDEVELOP_SMS_NE_USTANOVLEN_MODULQ")?></td>
			</tr>
		<? endif;?>
	<? $tabControl->BeginNextTab() ?>
    	<tr>
    		<td width="50%"><?=GetMessage("JUSTDEVELOP_SMS_LOGIN")?></td>
    		<td width="50%">
    		    <input
    			    type="text"
    			    size="50"
    			    value="<?=COption::GetOptionString('justdevelop.morder', 'login') ?>"
    			    name="settings[login]" />
    		</td>
    	</tr>
    	<tr>
    		<td width="50%"><?=GetMessage("JUSTDEVELOP_SMS_PASSWORD")?></td>
    		<td width="50%">
    		    <input
    			    type="text"
    			    size="50"
    			    value="<?=COption::GetOptionString('justdevelop.morder', 'password')?>"
    			    name="settings[password]" />
    		</td>
    	</tr>
    	
	<? $tabControl->BeginNextTab();?>
		<? require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
		<?
		if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)> 0 && check_bitrix_sessid()) 
		{
			if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
				LocalRedirect($_REQUEST["back_url_settings"]);
			else
				LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
		}
		?>
	
	<?$tabControl->Buttons();?>
	    <input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE") ?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE") ?>" />
	    <input type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY") ?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE") ?>" />
	    <?if(strlen($_REQUEST["back_url_settings"])):?>
		<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL") ?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE") ?>" onclick="window.location='<?=htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"])) ?>'" />
		<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"]) ?>" />
	    <?endif;?>
	    <?=bitrix_sessid_post();?>
	<?$tabControl->End();?>
	</form>
<? endif;?>