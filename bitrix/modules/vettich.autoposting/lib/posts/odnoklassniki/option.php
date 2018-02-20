<?
namespace Vettich\Autoposting\Posts\odnoklassniki;
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
			'NAME' => GetMessage('ODNOKLASSNIKI_ACCOUNTS_NAME'),
			'IS_ENABLE' => GetMessage('ODNOKLASSNIKI_IS_ENABLE'),
			'API_ID' => GetMessage('ODNOKLASSNIKI_API_ID'),
			'API_PUBLIC_KEY' => GetMessage('ODNOKLASSNIKI_API_PUBLIC_KEY'),
			'API_SECRET_KEY' => array('content'=>GetMessage('ODNOKLASSNIKI_API_SECRET_KEY'), 'default'=>false),
			'ACCESS_TOKEN' => array('content'=>GetMessage('ODNOKLASSNIKI_ACCESS_TOKEN'), 'default'=>false),
		);
	}

	static function ChangeRow(&$row, $values)
	{
		if(empty($row))
			return;
		
		$row->AddInputField("NAME", array("size"=>20));
		$row->AddInputField("API_ID", array("size"=>18));
		$row->AddInputField("API_SECRET", array("size"=>18));
		$row->AddInputField("API_SECRET_KEY", array("size"=>18));
		$row->AddInputField("ACCESS_TOKEN", array("size"=>18));

		$row->AddViewField('IS_ENABLE', $values['IS_ENABLE'] == 'Y' ? GetMessage('YES') : GetMessage('NO'));
		$row->AddCheckField('IS_ENABLE');
	}

	static function GetArModuleParamsPosts($index)
	{
		$arProps = PostingOption::getProps();
		if(!$iblock_id)
			$iblock_id = PostingOption::GetByID($index, 'IBLOCK_ID');
		$values = $arProps[$iblock_id] ?: $arProps['none'];
		$arValues = Func::GetValues($index, Func::DBOPTIONTABLE);
		if(empty($arValues))
		{
			$arValues = array(
				'ODNOKLASSNIKI_PHOTO' => 'DETAIL_PICTURE',
				'ODNOKLASSNIKI_LINK' => 'DETAIL_PAGE_URL',
				'ODNOKLASSNIKI_MESSAGE' => GetMessage('ODNOKLASSNIKI_MESSAGE_DEFAULT'),
				'ODNOKLASSNIKI_UTM_SOURCE' => 'odnoklassniki',
			);
		}
		foreach($arValues as $k=>$v)
		{
			if(isset($_POST[$k]))
				if(empty($_POST[$k]) && $k != 'ODNOKLASSNIKI_MESSAGE')
					unset($_POST[$k]);
		}
		$arPostParams = array(
			'TABS' => array(
				'ODNOKLASSNIKI_TAB' => array(
					'NAME' => GetMessage('ODNOKLASSNIKI_TAB_NAME'),
					'TITLE' => GetMessage('ODNOKLASSNIKI_TAB_TITLE')
				)
			),
			'PARAMS' => array(
				'ODNOKLASSNIKI_PUBLISH_DATE' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('POST_ODNOKLASSNIKI_PUBLISH_DATE'),
					'TYPE' => 'LIST',
					'VALUES' => $values,
					'VALUE' => $arValues['ODNOKLASSNIKI_PUBLISH_DATE'],
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1001,
					'HELP' => GetMessage('POST_ODNOKLASSNIKI_PUBLISH_DATE_HELP'),
				),
				'ODNOKLASSNIKI_PHOTO' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('POST_ODNOKLASSNIKI_PHOTO'),
					'TYPE' => 'LIST',
					'VALUES' => $values,
					'VALUE' => $arValues['ODNOKLASSNIKI_PHOTO'],
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1011,
					'HELP' => GetMessage('POST_ODNOKLASSNIKI_PHOTO_HELP'),
				),
				'ODNOKLASSNIKI_PHOTO_OTHER' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('POST_ODNOKLASSNIKI_PHOTO_OTHER'),
					'TYPE' => 'LIST',
					'VALUES' => $values,
					'VALUE' => $arValues['ODNOKLASSNIKI_PHOTO_OTHER'],
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1012,
					'HELP' => GetMessage('POST_ODNOKLASSNIKI_PHOTO_OTHER_HELP'),
				),
				'ODNOKLASSNIKI_LINK' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('POST_ODNOKLASSNIKI_LINK'),
					'TYPE' => 'LIST',
					'VALUES' => $values,
					'VALUE' => $arValues['ODNOKLASSNIKI_LINK'],
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1020,
					'HELP' => GetMessage('POST_ODNOKLASSNIKI_LINK_HELP'),
				),
				'ODNOKLASSNIKI_MESSAGE' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('POST_ODNOKLASSNIKI_MESSAGE'),
					'DESCRIPTION' => GetMessage('POST_ODNOKLASSNIKI_MESSAGE_DESCRIPTION'),
					'TYPE' => 'TEXTAREA',
					'VALUE' => $arValues['ODNOKLASSNIKI_MESSAGE'],
					'CHOISE' => 'SIMPLE',
					'VALUES' => $values,
					'SORT' => 1050,
					'HELP' => GetMessage('POST_ODNOKLASSNIKI_MESSAGE_HELP'),
				),
				'ODNOKLASSNIKI_UTM_SOURCE' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('UTM_SOURCE'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['ODNOKLASSNIKI_UTM_SOURCE'],
					'SORT' => 3000,
					'HELP' => GetMessage('UTM_SOURCE_HELP')
				),
				'ODNOKLASSNIKI_UTM_MEDIUM' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('UTM_MEDIUM'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['ODNOKLASSNIKI_UTM_MEDIUM'],
					'SORT' => 3010,
					'HELP' => GetMessage('UTM_MEDIUM_HELP')
				),
				'ODNOKLASSNIKI_UTM_CAMPAIGN' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('UTM_CAMPAIGN'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['ODNOKLASSNIKI_UTM_CAMPAIGN'],
					'SORT' => 3020,
					'HELP' => GetMessage('UTM_CAMPAIGN_HELP')
				),
				'ODNOKLASSNIKI_UTM_TERM' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('UTM_TERM'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['ODNOKLASSNIKI_UTM_TERM'],
					'SORT' => 3030,
					'HELP' => GetMessage('UTM_TERM_HELP')
				),
				'ODNOKLASSNIKI_UTM_CONTENT' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('UTM_CONTENT'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['ODNOKLASSNIKI_UTM_CONTENT'],
					'SORT' => 3040,
					'HELP' => GetMessage('UTM_CONTENT_HELP')
				),
			),
		);

		if(PostingFunc::isPlus())
		{
			$arPostParams['PARAMS']['ODNOKLASSNIKI_PUBLICATION_MODE'] = array(
				'TAB' => 'ODNOKLASSNIKI_TAB',
				'NAME' => GetMessage('ODNOKLASSNIKI_PUBLICATION_MODE'),
				'TYPE' => 'LIST',
				'VALUES' => array(
					// 'update' => GetMessage('ODNOKLASSNIKI_PUBLICATION_MODE_UPDATE'),
					// 'del_add' => GetMessage('ODNOKLASSNIKI_PUBLICATION_MODE_DEL_ADD'),
					'none' => GetMessage('ODNOKLASSNIKI_PUBLICATION_MODE_NONE'),
				),
				'VALUE' => $arValues['ODNOKLASSNIKI_PUBLICATION_MODE'],
				'HELP' => GetMessage('ODNOKLASSNIKI_PUBLICATION_MODE_HELP'),
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
				'NAME' => 'Autoposting to Odnoklassniki ['.Func::GetNextIdDB().']',
				'IS_ENABLE' => 'Y',
				'IS_GROUP_PUBLISH' => 'Y',
			);
		}
		$arModuleParams = array(
			'TAB_CONTROL_POSTFIX' => 'odnoklassniki',
			'FORM' => array(
				'PARAMS' => array(
					'ID' => $index,
				),
				'TYPE' => array('WITH_PARAMS', 'NOT_DEFAULT'),
			),
			'TABS' => array(
				'ODNOKLASSNIKI_TAB' => array(
					'NAME' => GetMessage('ODNOKLASSNIKI_TAB_NAME'),
					'TITLE' => GetMessage('ODNOKLASSNIKI_TAB_TITLE')
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
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_ACCOUNTS_NAME'),
					'DESCRIPTION' => GetMessage('ODNOKLASSNIKI_ACCOUNTS_NAME_DESCRIPTION'),
					'VALUE' => $arValues['NAME'],
					'TYPE' => 'STRING',
					'REQUIRED' => 'Y',
				),
				'IS_ENABLE' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_IS_ENABLE'),
					'VALUE' => $arValues['IS_ENABLE'],
					'TYPE' => 'CHECKBOX',
				),
				'IS_GROUP_PUBLISH' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_IS_GROUP_PUBLISH'),
					'VALUE' => $arValues['IS_GROUP_PUBLISH'],
					'TYPE' => 'CHECKBOX',
				),
				'GROUP_ID' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_GROUP_ID'),
					'VALUE' => $arValues['GROUP_ID'],
					'TYPE' => 'STRING',
				),
				'API_ID' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_API_ID'),
					'VALUE' => $arValues['API_ID'],
					'TYPE' => 'STRING',
				),
				'API_PUBLIC_KEY' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_API_PUBLIC_KEY'),
					'VALUE' => $arValues['API_PUBLIC_KEY'],
					'TYPE' => 'STRING',
				),
				'API_SECRET_KEY' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_API_SECRET_KEY'),
					'VALUE' => $arValues['API_SECRET_KEY'],
					'TYPE' => 'STRING',
				),
				'ACCESS_TOKEN' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_ACCESS_TOKEN'),
					'VALUE' => $arValues['ACCESS_TOKEN'],
					'TYPE' => 'STRING',
				),
				'note1' => array(
					'TAB' => 'ODNOKLASSNIKI_TAB',
					'NAME' => GetMessage('ODNOKLASSNIKI_NOTE'),
					'TEXT' => GetMessage('ODNOKLASSNIKI_NOTE_TEXT'),
					'VALUE' => '',
					'TYPE' => 'NOTE',
				),
			)
		);

		$hlp = PostingFunc::vettich_service('get_url', 'url=autoposting.odnoklassniki.video_help');
		if(!empty($hlp['url']))
		{
			$arModuleParams['TABS']['ODNOKLASSNIKI_TAB_VIDEO'] = array(
				'NAME' => GetMessage('ODNOKLASSNIKI_TAB_VIDEO_NAME'),
				'TITLE' => GetMessage('ODNOKLASSNIKI_TAB_VIDEO_TITLE')
			);
			$arModuleParams['PARAMS']['video_help'] = array(
				'TAB' => 'ODNOKLASSNIKI_TAB_VIDEO',
				'TYPE' => 'NOTE',
				'TEXT' => PostingFunc::get_youtube_frame($hlp['url']),
			);
		}

		return $arModuleParams;
	}
}