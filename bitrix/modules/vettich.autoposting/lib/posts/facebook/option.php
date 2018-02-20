<?
namespace Vettich\Autoposting\Posts\facebook;
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
			'NAME' => GetMessage('FB_ACCOUNTS_NAME'),
			'IS_ENABLE' => GetMessage('FB_IS_ENABLE'),
			'GROUP_ID' => GetMessage('FB_GROUP_ID'),
			'APP_ID' => GetMessage('FB_APP_ID'),
			'APP_SECRET' => GetMessage('FB_APP_SECRET'),
		);
	}

	static function ChangeRow(&$row)
	{
		if(empty($row))
			return;
		
		$row->AddInputField("NAME", array("size"=>20));
		$row->AddInputField("GROUP_ID", array("size"=>18));
		$row->AddInputField("APP_ID", array("size"=>18));
		$row->AddInputField("APP_SECRET", array("size"=>18));

		$row->AddViewField('IS_ENABLE', $row->arRes['IS_ENABLE'] == 'Y' ? GetMessage('YES') : GetMessage('NO'));
		$row->AddCheckField('IS_ENABLE');
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
				'FB_LINK' => 'DETAIL_PAGE_URL',
				'FB_PHOTO' => 'DETAIL_PICTURE',
				'FB_MESSAGE' => GetMessage('FB_MESSAGE_DEFAULT'),
				'FB_UTM_SOURCE' => 'facebook',
			);
		}
		foreach($arValues as $k=>$v)
		{
			if(isset($_POST[$k]))
				if(empty($_POST[$k]) && $k != 'FB_MESSAGE')
					unset($_POST[$k]);
		}
		$arPostParams = array(
			'TABS' => array(
				'FB_TAB' => array(
					'NAME' => GetMessage('FB_TAB_NAME'),
					'TITLE' => GetMessage('FB_TAB_TITLE')
				)
			),
			'PARAMS' => array(
				'FB_PUBLISH_DATE' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('POST_FB_PUBLISH_DATE'),
					'TYPE' => 'LIST',
					'VALUES' => $values,
					'VALUE' => $arValues['FB_PUBLISH_DATE'],
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 2010,
					'HELP' => GetMessage('POSTS_FB_PUBLISH_DATE_HELP'),
				),
				'FB_LINK' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('POST_FB_LINK'),
					'TYPE' => 'LIST',
					'VALUES' => $values,
					'VALUE' => $arValues['FB_LINK'],
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 2020,
					'HELP' => GetMessage('POSTS_FB_LINK_HELP'),
				),
				'FB_PHOTO' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('POST_FB_PHOTO'),
					'TYPE' => 'LIST',
					'VALUES' => $values,
					'VALUE' => $arValues['FB_PHOTO'],
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 2030,
					'HELP' => GetMessage('POST_FB_PHOTO_HELP'),
				),
				'FB_NAME' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('POST_FB_NAME'),
					'TYPE' => 'LIST',
					'VALUES' => $values,
					'VALUE' => $arValues['FB_NAME'],
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 2040,
					'HELP' => GetMessage('POSTS_FB_NAME_HELP'),
				),
				'FB_DESCRIPTION' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('POST_FB_DESCRIPTION'),
					'TYPE' => 'LIST',
					'VALUES' => $values,
					'VALUE' => $arValues['FB_DESCRIPTION'],
					'SIZE' => 0,
					'SORT' => 2050,
					'HELP' => GetMessage('POST_FB_DESCRIPTION_HELP'),
				),
				'FB_MESSAGE' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('POST_FB_MESSAGE'),
					'TYPE' => 'TEXTAREA',
					'CHOISE' => 'SIMPLE',
					'VALUE' => $arValues['FB_MESSAGE'],
					'VALUES' => $values,
					'SORT' => 2060,
					'HELP' => GetMessage('POST_FB_MESSAGE_HELP'),
				),
				'FB_UTM_SOURCE' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('UTM_SOURCE'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['FB_UTM_SOURCE'],
					'SORT' => 3000,
					'HELP' => GetMessage('UTM_SOURCE_HELP')
				),
				'FB_UTM_MEDIUM' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('UTM_MEDIUM'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['FB_UTM_MEDIUM'],
					'SORT' => 3010,
					'HELP' => GetMessage('UTM_MEDIUM_HELP')
				),
				'FB_UTM_CAMPAIGN' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('UTM_CAMPAIGN'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['FB_UTM_CAMPAIGN'],
					'SORT' => 3020,
					'HELP' => GetMessage('UTM_CAMPAIGN_HELP')
				),
				'FB_UTM_TERM' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('UTM_TERM'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['FB_UTM_TERM'],
					'SORT' => 3030,
					'HELP' => GetMessage('UTM_TERM_HELP')
				),
				'FB_UTM_CONTENT' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('UTM_CONTENT'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['FB_UTM_CONTENT'],
					'SORT' => 3040,
					'HELP' => GetMessage('UTM_CONTENT_HELP')
				),
			)
		);

		if(PostingFunc::isPlus())
		{
			$arPostParams['PARAMS']['FB_PUBLICATION_MODE'] = array(
				'TAB' => 'FB_TAB',
				'NAME' => GetMessage('FB_PUBLICATION_MODE'),
				'TYPE' => 'LIST',
				'VALUES' => array(
					'update' => GetMessage('FB_PUBLICATION_MODE_UPDATE'),
					'del_add' => GetMessage('FB_PUBLICATION_MODE_DEL_ADD'),
					'none' => GetMessage('FB_PUBLICATION_MODE_NONE'),
				),
				'VALUE' => $arValues['FB_PUBLICATION_MODE'],
				'HELP' => GetMessage('FB_PUBLICATION_MODE_HELP'),
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
				'NAME' => 'Autoposting to Facebook ['.Func::GetNextIdDB().']',
				'IS_ENABLE' => 'Y',
			);
		}
		$arModuleParams = array(
			'TAB_CONTROL_POSTFIX' => 'facebook',
			'FORM' => array(
				'PARAMS' => array(
					'ID' => $index,
				),
				'TYPE' => array('WITH_PARAMS', 'NOT_DEFAULT'),
			),
			'TABS' => array(
				'FB_TAB' => array(
					'NAME' => GetMessage('FB_TAB_NAME'),
					'TITLE' => GetMessage('FB_TAB_TITLE')
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
					'TAB' 			=> 'FB_TAB',
					'NAME' 			=> GetMessage('FB_ACCOUNTS_NAME'),
					'DESCRIPTION' 	=> GetMessage('FB_ACCOUNTS_NAME_DESCRIPTION'),
					'VALUE' 		=> $arValues['NAME'],
					'TYPE' 			=> 'STRING',
					'REQUIRED' 		=> 'Y',
					'SORT' => 100,
				),
				'IS_ENABLE' => array(
					'TAB' 		=> 'FB_TAB',
					'NAME' 		=> GetMessage('FB_IS_ENABLE'),
					'TYPE' 		=> 'CHECKBOX',
					'VALUE' 	=> $arValues['IS_ENABLE'],
					'SORT' => 150,
				),
				'GROUP_ID' => array(
					'TAB' 			=> 'FB_TAB',
					'NAME' 			=> GetMessage('FB_GROUP_ID'),
					'DESCRIPTION' 	=> GetMessage('FB_GROUP_ID_DESCRIPTION'),
					'VALUE' 		=> $arValues['GROUP_ID'],
					'TYPE' 			=> 'STRING',
					'HELP'			=> $arValues['GROUP_ID'],
					'SORT' => 200,
				),
				'APP_ID' => array(
					'TAB' 		=> 'FB_TAB',
					'NAME' 		=> GetMessage('FB_APP_ID'),
					'VALUE' 	=> $arValues['APP_ID'],
					'TYPE' 		=> 'STRING',
					'SORT' => 250,
				),
				'APP_SECRET' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('FB_APP_SECRET'),
					'DESCRIPTION' => GetMessage(''),
					'VALUE' => $arValues['APP_SECRET'],
					'TYPE' => 'STRING',
				),
				'ACCESS_TOKEN' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('FB_ACCOUNTS_ACCESS_TOKEN'),
					'VALUE' 	=> $arValues['ACCESS_TOKEN'],
					'TYPE' => 'STRING',
				),
				'access_token_button' => array(
					'TAB' => 'FB_TAB',
					'NAME' => '',
					'TYPE' => 'CUSTOM',
					'HTML' => GetMessage('FB_access_token_button').
						'<script>ALERT_NOT_APP_ID="'.GetMessage('ALERT_NOT_APP_ID').'"</script>',
				),
				'fb_help1' => array(
					'TAB' => 'FB_TAB',
					'NAME' => GetMessage('FB_HELP1_NAME'),
					'TYPE' => 'NOTE',
					'TEXT' => GetMessage('FB_HELP1_TEXT'),
				),
			)
		);

		$hlp = PostingFunc::vettich_service('get_url', 'url=autoposting.facebook.video_help');
		if(!empty($hlp['url']))
		{
			$arModuleParams['TABS']['FB_TAB_VIDEO'] = array(
				'NAME' => GetMessage('FB_TAB_VIDEO_NAME'),
				'TITLE' => GetMessage('FB_TAB_VIDEO_TITLE')
			);
			$arModuleParams['PARAMS']['video_help'] = array(
				'TAB' => 'FB_TAB_VIDEO',
				'TYPE' => 'NOTE',
				'TEXT' => PostingFunc::get_youtube_frame($hlp['url']),
			);
		}

		global $arIncludeJS;
		$arIncludeJS[] = '/bitrix/js/vettich.autoposting/fb_options.js';

		return $arModuleParams;
	}

}