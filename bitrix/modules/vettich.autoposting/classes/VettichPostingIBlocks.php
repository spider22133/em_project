<?
IncludeModuleLangFile(__FILE__);

class VettichPostingIBlocks
{
	public static function OnAdminListDisplayHandler(&$list)
	{
		CJSCore::Init('jquery');
		$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/js/vettich.autoposting/iblocks.js');
		if($GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/iblock_element_admin.php'
			or $GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/iblock_list_admin.php')
		{
			$list->arActions['VCH_POST_IBLOCK_MENU_SEND'] = GetMessage('VCH_POST_IBLOCK_MENU_SEND');

			$test2html = '<span style="display:none" id="VCH_POST_IBLOCK_SELECT">'.GetMessage('VCH_POST_IBLOCK_SELECT').'<select name="vch_post_id">';
			$arPostIds = VettichPostingOption::GetIDs();
			foreach($arPostIds as $id)
				$test2html .= '<option value="'.$id.'">'.VettichPostingOption::GetByID($id, 'name').'</option>';
			$test2html .= '</select></span>';

			$list->arActions['VCH_POST_IBLOCK_MENU_SEND_HTML'] = array('type'=>'html', 'value'=>$test2html);
			$list->arActionsParams['select_onchange'] .= 'vch_post_iblock_change(this.value);';
			foreach($list->aRows as $id=>$v)
			{
				if (substr($v->id, 0, 1) != 'S')
				{
					$arnewActions = array();
					foreach($v->aActions as $i=>$act)
					{
						if($act['ICON'] == 'delete')
						{
							$arnewActions[] = array(
								'ICON' => '',
								'TEXT' => GetMessage('VCH_POST_IBLOCK_MENU_SEND'),
								'ACTION' => 'vettich_iblock_menu_send("'.$v->arRes["ID"].'","'.$v->arRes["IBLOCK_ID"].'");',
							);
							$arnewActions[] = array('SEPARATOR'=>true);
						}
						$arnewActions[] = $act;
					}
					$v->aActions = $arnewActions;
				}
			}
		}
	}

	public static function OnBeforePrologHandler()
	{
		if(($GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/iblock_element_admin.php'
			or $GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/iblock_list_admin.php')
			&& $_REQUEST['action'] == 'VCH_POST_IBLOCK_MENU_SEND' && CModule::IncludeModule('iblock'))
		{
			// VOptions::debugg($_REQUEST);
			if($_REQUEST['action_target'] == 'selected')
			{
				$rsList = CIBlockElement::GetList(array(), array('IBLOCK_TYPE'=>$_REQUEST['IBLOCK_TYPE'], 'IBLOCK_ID' => $_REQUEST['IBLOCK_ID'], 'ACTIVE'=>'Y'));
			}
			else
			{
				$rsList = CIBlockElement::GetList(array(), array('IBLOCK_TYPE'=>$_REQUEST['IBLOCK_TYPE'], 'IBLOCK_ID' => $_REQUEST['IBLOCK_ID'], 'ID'=>$_REQUEST['ID'], 'ACTIVE'=>'Y'));
			}
			while($arList = $rsList->GetNext())
			{
				VettichPosting::ElementPost($arList, array('ids'=>array($_REQUEST['vch_post_id'])));
			}
			if(count($_REQUEST['ID']) == 1)
				ShowNote(GetMessage('VCH_POST_IBLOCK_SUCCESS'));
			elseif($_REQUEST['action_target'] == 'selected')
				ShowNote(GetMessage('VCH_POST_IBLOCK_SUCCESS_ALL'));
			else
				ShowNote(GetMessage('VCH_POST_IBLOCK_SUCCESS_MORE'));
		}
	}
}