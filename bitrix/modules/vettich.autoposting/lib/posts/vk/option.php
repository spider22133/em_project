<?
namespace Vettich\Autoposting\Posts\vk;
use Vettich\Autoposting\PostingOption;
use Vettich\Autoposting\PostingFunc;

IncludeModuleLangFile(__FILE__);

class Option extends \Vettich\Autoposting\PostsBase\OptionBase
{
	static $dbTable = Func::DBTABLE;
	static $dbOptionTable = Func::DBOPTIONTABLE;
	static $accPrefix = Func::ACCPREFIX;

	static function GetFields()
	{
		return array(
			'ID' => 'ID',
			'NAME' => GetMessage('VK_ACCOUNTS_NAME'),
			'IS_ENABLE' => GetMessage('VK_IS_ENABLE'),
			'IS_GROUP_PUBLISH' => GetMessage('IS_GROUP_PUBLISH'),
			'GROUP_PUBLISH_ID' => GetMessage('GROUP_PUBLISH_ID'),
			'GROUP_ID' => GetMessage('VK_ACCOUNTS_GROUP_ID'),
		);
	}

	static function ChangeRow(&$row)
	{
		if(empty($row))
			return;
		
		$row->AddInputField("NAME", array("size"=>20));
		$row->AddInputField("GROUP_PUBLISH_ID", array("size"=>18));
		$row->AddInputField("GROUP_ID", array("size"=>18));

		$row->AddViewField('IS_ENABLE', $row->arRes['IS_ENABLE'] == 'Y' ? GetMessage('YES') : GetMessage('NO'));
		$row->AddViewField('IS_GROUP_PUBLISH', $row->arRes['IS_ENABLE'] == 'Y' ? GetMessage('YES') : GetMessage('NO'));
		$row->AddCheckField('IS_ENABLE');
		$row->AddCheckField('IS_GROUP_PUBLISH');
	}

	static function GetArModuleParamsPosts($index, $iblock_id=false)
	{
		$arProps = PostingOption::getProps();
		if(!$iblock_id)
			$iblock_id = PostingOption::GetByID($index, 'IBLOCK_ID');
		$values = $arProps[$iblock_id] ?: $arProps['none'];
		$arValues = Func::GetValues($index, Func::DBOPTIONTABLE);
		if(empty($arValues))
		{
			$arValues = array(
				'VK_PUBLISH_DATE' => '',
				'VK_PHOTOS' => '',
				'VK_PHOTO' => 'DETAIL_PICTURE',
				'VK_LINK' => 'DETAIL_PAGE_URL',
				'VK_MESSAGE' => GetMessage('VK_MESSAGE_DEFAULT'),
				'VK_UTM_SOURCE' => 'vk',
			);
		}
		foreach($arValues as $k=>$v)
		{
			if(isset($_POST[$k]))
				if(empty($_POST[$k]) && $k != 'VK_MESSAGE')
					unset($_POST[$k]);
		}
		$arPostParams = array(
			'TABS' => array(
				'VK_TAB' => array(
					'NAME' => GetMessage('VK_TAB_NAME'),
					'TITLE' => GetMessage('VK_TAB_TITLE')
				)
			),
			'PARAMS' => array(
				'VK_PUBLISH_DATE' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('POST_VK_PUBLISH_DATE'),
					'TYPE' => 'LIST',
					'VALUES' => $values,
					'VALUE' => $arValues['VK_PUBLISH_DATE'],
					'MULTIPLE' => 'N',
					'REQUEST_VALUE_NOT_USE_IS_EMPTY' => 'Y',
					'SIZE' => 0,
					'SORT' => 1010,
					'HELP' => GetMessage('POST_VK_PUBLISH_DATE_HELP'),
				),
				'VK_PHOTO' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('POST_VK_PHOTO'),
					'TYPE' => 'LIST',
					'VALUES' => $values,
					'VALUE' => $arValues['VK_PHOTO'],
					'MULTIPLE' => 'N',
					'REQUEST_VALUE_NOT_USE_IS_EMPTY' => 'Y',
					'SIZE' => 0,
					'SORT' => 1011,
					'HELP' => GetMessage('POST_VK_PHOTO_HELP'),
				),
				'VK_PHOTOS' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('POST_VK_PHOTOS'),
					'TYPE' => 'LIST',
					'VALUES' => $values,
					'VALUE' => $arValues['VK_PHOTOS'],
					'MULTIPLE' => 'N',
					'REQUEST_VALUE_NOT_USE_IS_EMPTY' => 'Y',
					'SIZE' => 0,
					'SORT' => 1020,
					'HELP' => GetMessage('POST_VK_PHOTOS_HELP'),
				),
				'VK_LINK' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('POST_VK_LINK'),
					'TYPE' => 'LIST',
					'VALUES' => $values,
					'VALUE' => $arValues['VK_LINK'],
					'MULTIPLE' => 'N',
					'REQUEST_VALUE_NOT_USE_IS_EMPTY' => 'Y',
					'SIZE' => 0,
					'SORT' => 1030,
					'HELP' => GetMessage('POST_VK_LINK_HELP'),
				),
				'VK_MESSAGE' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('POST_VK_MESSAGE'),
					'TYPE' => 'TEXTAREA',
					'VALUE' => $arValues['VK_MESSAGE'],
					'CHOISE' => 'SIMPLE',
					'VALUES' => $values,
					'SORT' => 1050,
					'HELP' => GetMessage('POST_VK_MESSAGE_HELP'),
				),
				'VK_UTM_SOURCE' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('UTM_SOURCE'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['VK_UTM_SOURCE'],
					'SORT' => 3000,
					'HELP' => GetMessage('UTM_SOURCE_HELP')
				),
				'VK_UTM_MEDIUM' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('UTM_MEDIUM'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['VK_UTM_MEDIUM'],
					'SORT' => 3010,
					'HELP' => GetMessage('UTM_MEDIUM_HELP')
				),
				'VK_UTM_CAMPAIGN' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('UTM_CAMPAIGN'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['VK_UTM_CAMPAIGN'],
					'SORT' => 3020,
					'HELP' => GetMessage('UTM_CAMPAIGN_HELP')
				),
				'VK_UTM_TERM' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('UTM_TERM'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['VK_UTM_TERM'],
					'SORT' => 3030,
					'HELP' => GetMessage('UTM_TERM_HELP')
				),
				'VK_UTM_CONTENT' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('UTM_CONTENT'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['VK_UTM_CONTENT'],
					'SORT' => 3040,
					'HELP' => GetMessage('UTM_CONTENT_HELP')
				),
			),
		);


		if(PostingFunc::isPlus())
		{
			$arPostParams['PARAMS']['VK_PUBLICATION_MODE'] = array(
				'TAB' => 'VK_TAB',
				'NAME' => GetMessage('VK_PUBLICATION_MODE'),
				'TYPE' => 'LIST',
				'VALUES' => array(
					'update' => GetMessage('VK_PUBLICATION_MODE_UPDATE'),
					'del_add' => GetMessage('VK_PUBLICATION_MODE_DEL_ADD'),
					'none' => GetMessage('VK_PUBLICATION_MODE_NONE'),
				),
				'VALUE' => $arValues['VK_PUBLICATION_MODE'],
				'HELP' => GetMessage('VK_PUBLICATION_MODE_HELP'),
				'SORT' => 160,
			);
		}
		return $arPostParams;
	}

	static function GetArModuleParams($index)
	{
		$arValues = Func::GetValues($index);
		if(empty($arValues))
		{
			$arValues = array(
				'NAME' => 'Autoposting to VK ['.Func::GetNextIdDB().']',
				'IS_ENABLE' => 'Y',
				'IS_GROUP_PUBLISH' => 'N',
				'GROUP_PUBLISH' => 'Y',
				'GROUP_ID_STD' => 'Y',
			);
		}
		$arModuleParams = array(
			'TAB_CONTROL_POSTFIX' => 'vk',
			'FORM' => array(
				'PARAMS' => array(
					'ID' => $index,
				),
				'TYPE' => array('WITH_PARAMS', 'NOT_DEFAULT'),
			),
			'TABS' => array(
				'VK_TAB' => array(
					'NAME' => GetMessage('VK_TAB_NAME'),
					'TITLE' => GetMessage('VK_TAB_TITLE')
				)
			),
			'BUTTONS' => array(
				'SAVE' => array(
					'NAME' => GetMessage('SAVE_BUTTON'),
				),
				'APPLY' => array(
					'NAME' => GetMessage('APPLY_BUTTON'),
				),
				'RESTORE_DEFAULTS' => array(
					'ENABLE' => 'N',
				)
			),
			'PARAMS' => array(
				'NAME' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('VK_ACCOUNTS_NAME'),
					'DESCRIPTION' => GetMessage('VK_ACCOUNTS_NAME_DESCRIPTION'),
					'VALUE' => $arValues['NAME'],
					'TYPE' => 'STRING',
					'REQUIRED' => 'Y',
					'SORT' => 100,
				),
				'IS_ENABLE' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('VK_IS_ENABLE'),
					'VALUE' => $arValues['IS_ENABLE'],
					'TYPE' => 'CHECKBOX',
					'SORT' => 150,
				),
				'IS_GROUP_PUBLISH' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('IS_GROUP_PUBLISH'),
					'VALUE' => $arValues['IS_GROUP_PUBLISH'],
					'TYPE' => 'CHECKBOX',
					'SORT' => 200,
				),
				'GROUP_PUBLISH_ID' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('GROUP_PUBLISH_ID'),
					'VALUE' => $arValues['GROUP_PUBLISH_ID'],
					'TYPE' => 'STRING',
					'SORT' => 250,
				),
				'GROUP_PUBLISH' => array(
					'TAB' => 'VK_TAB',
					'NAME' => '',
					'TYPE' => 'RADIO',
					'DESCRIPTION' => GetMessage('GROUP_PUBLISH_ID_DESCRIPTION'),
					'VALUE' => $arValues['GROUP_PUBLISH'],
					'VALUES' => array(
						'Y' => GetMessage('GROUP_PUBLISH_GROUP'),
						'N' => GetMessage('GROUP_PUBLISH_USER'),
					),
					'SORT' => 300,
				),
				'GROUP_ID_STD' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('VK_ACCOUNTS_GROUP_ID_STD'),
					'VALUE' => $arValues['GROUP_ID_STD'],
					'TYPE' => 'CHECKBOX',
					'REFRESH' => 'Y',
					'SORT' => 350,
				),
				'GROUP_ID' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('VK_ACCOUNTS_GROUP_ID'),
					'PLACEHOLDER' => GetMessage('VK_ACCOUNTS_GROUP_ID_PLACEHOLDER'),
					'DESCRIPTION' => GetMessage('VK_ACCOUNTS_GROUP_ID_DESCRIPTION'),
					'VALUE' => $arValues['GROUP_ID'],
					'TYPE' => 'STRING',
					'TYPE' => (empty($_POST['GROUP_ID_STD']) ?
						($arValues['GROUP_ID_STD'] == 'Y' ? 'HIDDEN' : 'STRING') :
						($_POST['GROUP_ID_STD'] == 'Y' ? 'HIDDEN' : 'STRING')),
					'SORT' => 400,
				),
				'ACCESS_TOKEN' => array(
					'TAB' => 'VK_TAB',
					'NAME' => GetMessage('VK_ACCOUNTS_ACCESS_TOKEN'),
					'VALUE' => $arValues['ACCESS_TOKEN'],
					'TYPE' => 'STRING',
				),
				'ACCESS_TOKEN_BUTTON' => array(
					'TAB' => 'VK_TAB',
					'NAME' => '',
					'DESCRIPTION' => GetMessage('VK_ACCOUNTS_ACCESS_TOKEN_DESCRIPTION'),
					'TYPE' => 'CUSTOM',
					'HTML' => GetMessage('VK_access_token_button_HTML').
						"<script>ALERT_NOT_GROUP_ID='".GetMessage('ALERT_NOT_GROUP_ID')."';".
						"ALERT_NOT_ACCESS_TOKEN='".GetMessage('ALERT_NOT_ACCESS_TOKEN')."'</script>",
				),
			)
		);

		$vk_info = '<script>'
			.'VCH_USER_INFO_CAPTCHA_GETTED='.\VOptions::_json_encode(GetMessage('VCH_USER_INFO_CAPTCHA_GETTED')).';'
			.'VCH_USER_INFO_CAPTCHA_SEND_BUTTON='.\VOptions::_json_encode(GetMessage('VCH_USER_INFO_CAPTCHA_SEND_BUTTON')).';'
			.'VCH_USER_INFO_CAPTCHA_NOT_NEED='.\VOptions::_json_encode(GetMessage('VCH_USER_INFO_CAPTCHA_NOT_NEED')).';'
			.'VCH_USER_INFO_ACCESS_TOKEN_EMPTY='.\VOptions::_json_encode(GetMessage('VCH_USER_INFO_ACCESS_TOKEN_EMPTY')).';'
			.'</script>'
			.'<div onclick="vch_autoposting_vk_refresh_info(\'vch_user_info_content\')" class="voptions-add-button">'
				.GetMessage('VCH_USER_INFO_BUTTON')
			.'</div>'
			.'<div id="vch_user_info_content" style="margin-top:10px;padding-left:20px"><div>';

		$arModuleParams['PARAMS']['USER_INFO_AREA'] = array(
			'TAB' => 'VK_TAB',
			'NAME' => '',
			'TYPE' => 'CUSTOM',
			'HTML' => $vk_info,
		);

		$hlp = PostingFunc::vettich_service('get_url', 'url=autoposting.vk.video_help');
		if(!empty($hlp['url']))
		{
			$arModuleParams['TABS']['VK_TAB_VIDEO'] = array(
				'NAME' => GetMessage('VK_TAB_VIDEO_NAME'),
				'TITLE' => GetMessage('VK_TAB_VIDEO_TITLE')
			);
			$arModuleParams['PARAMS']['video_help'] = array(
				'TAB' => 'VK_TAB_VIDEO',
				'TYPE' => 'NOTE',
				'TEXT' => PostingFunc::get_youtube_frame($hlp['url']),
			);
		}

		global $arIncludeJS;
		$arIncludeJS[] = '/bitrix/js/vettich.autoposting/vk_options.js';

		return $arModuleParams;
	}
}
