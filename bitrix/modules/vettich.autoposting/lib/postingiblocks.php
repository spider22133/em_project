<?
namespace Vettich\Autoposting;
IncludeModuleLangFile(__FILE__);

class PostingIBlocks
{
	public static function OnAdminListDisplayHandler(&$list)
	{
		if($GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/iblock_element_admin.php'
			or $GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/iblock_list_admin.php'
			or $GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/cat_product_list.php')
		{
			\CJSCore::Init('jquery');
			$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/js/vettich.autoposting/iblocks.js');
			$list->arActions['VCH_POST_IBLOCK_MENU_SEND'] = GetMessage('VCH_POST_IBLOCK_MENU_SEND');

			$test2html = '<span style="display:none" id="VCH_POST_IBLOCK_SELECT">'.GetMessage('VCH_POST_IBLOCK_SELECT').'<select name="vch_post_id">';
			$pid = \COption::GetOptionString(PostingFunc::module_id(), 'POST_ID_IBLOCK_SELECT', 0);
			foreach(PostingOption::GetList(array('ID'), array('ID', 'NAME')) as $opt)
				$test2html .= '<option value="'.$opt['ID'].'"'.($opt['ID']==$pid ? ' selected':'').'>'.$opt['NAME'].'</option>';
			$test2html .= '</select></span>';

			$list->arActions['VCH_POST_IBLOCK_MENU_SEND_HTML'] = array('type'=>'html', 'value'=>$test2html);
			$list->arActionsParams['select_onchange'] .= 'vch_post_iblock_change(this.value);';
			foreach($list->aRows as $id=>$v)
			{
				$arnewActions = array();
				foreach($v->aActions as $i=>$act)
				{
					if($act['ICON'] == 'delete')
					{
						$qstr = 'IBLOCK_ID='.$v->arRes["IBLOCK_ID"];
						if(intval($v->id) > 0)
							$qstr .= '&ELEM_ID='.$v->id;
						elseif(substr($v->id, 0, 1) == 'E')
							$qstr .= '&ELEM_ID='.substr($v->id, 1);
						elseif(substr($v->id, 0, 1) == 'S')
							$qstr .= '&SECTION_ID='.substr($v->id, 1);
						$arnewActions[] = array(
							'ICON' => '',
							'TEXT' => GetMessage('VCH_POST_IBLOCK_MENU_SEND'),
							'ACTION' => 'vettich_iblock_menu_send("'.$qstr.'");',
						);
						$arnewActions[] = array('SEPARATOR'=>true);
					}
					$arnewActions[] = $act;
				}
				$v->aActions = $arnewActions;
			}
		}
	}

	public static function OnBeforePrologHandler()
	{
		if(($GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/iblock_element_admin.php'
				or $GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/iblock_list_admin.php'
				or $GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/cat_product_list.php')
			&& $_REQUEST['action'] == 'VCH_POST_IBLOCK_MENU_SEND'
			&& \CModule::IncludeModule('iblock'))
		{
			if($_REQUEST['action_target'] == 'selected')
			{
				$rsList = \CIBlockElement::GetList(array(), array(
					'IBLOCK_ID' => $_REQUEST['IBLOCK_ID'],
					'ACTIVE'=>'Y'
				));
			}
			else
			{
				$ids = array();
				$sectids = array();
				foreach($_REQUEST['ID'] as $id)
				{
					if(intval($id) > 0)
						$ids[] = $id;
					elseif(substr($id, 0, 1) == 'E')
						$ids[] = substr($id, 1);
					elseif(substr($id, 0, 1) == 'S')
						$sectids[] = substr($id, 1);
				}

				$arFilter = array(
					'IBLOCK_ID' => $_REQUEST['IBLOCK_ID'],
					'ACTIVE'=>'Y',
				);
				if(!empty($ids) && !empty($sectids))
					$arFilter[] = array(
						'LOGIC' => 'OR',
						'ID' => $ids,
						'SECTION_ID' => $sectids,
					);
				elseif(!empty($ids))
					$arFilter['ID'] = $ids;
				elseif(!empty($sectids))
					$arFilter['SECTION_ID'] = $sectids;
				$rsList = \CIBlockElement::GetList(array(), $arFilter);
			}
			while($arList = $rsList->GetNext())
			{
				Posting::ElementPost($arList, 'eventIBlocksPublication', array('ids'=>array($_REQUEST['vch_post_id'])));
			}
			\COption::SetOptionString(PostingFunc::module_id(), 'POST_ID_IBLOCK_SELECT', $_REQUEST['vch_post_id']);
			if($_REQUEST['action_target'] == 'selected')
				ShowNote(GetMessage('VCH_POST_IBLOCK_SUCCESS_ALL'));
			elseif(count($ids) == 1)
				ShowNote(GetMessage('VCH_POST_IBLOCK_SUCCESS'));
			elseif(count($ids) > 1)
				ShowNote(GetMessage('VCH_POST_IBLOCK_SUCCESS_MORE'));
			elseif(count($sectids) == 1)
				ShowNote(GetMessage('VCH_POST_IBLOCK_SECTION_SUCCESS'));
			elseif(count($sectids) > 1)
				ShowNote(GetMessage('VCH_POST_IBLOCK_SECTION_SUCCESS_MORE'));
			else
				ShowNote(GetMessage('VCH_POST_IBLOCK_UNKNOWN'));
		}
	}
}