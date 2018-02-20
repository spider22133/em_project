<?
namespace Vettich\Autoposting\Posts\twitter;
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
			'NAME' => GetMessage('TWITTER_ACCOUNTS_NAME'),
			'IS_ENABLE' => GetMessage('TWITTER_IS_ENABLE'),
			'API_KEY' => GetMessage('TWITTER_API_KEY'),
			'API_SECRET' => GetMessage('TWITTER_API_SECRET'),
			'ACCESS_TOKEN' => array('content'=>GetMessage('TWITTER_ACCESS_TOKEN'), 'default'=>false),
			'ACCESS_TOKEN_SECRET' => array(GetMessage('TWITTER_ACCESS_TOKEN_SECRET'), 'default'=>false),
		);
	}

	static function ChangeRow(&$row)
	{
		if(empty($row))
			return;
		
		$row->AddInputField("NAME", array("size"=>20));
		$row->AddInputField("API_KEY", array("size"=>18));
		$row->AddInputField("API_SECRET", array("size"=>18));
		$row->AddInputField("API_SECRET", array("size"=>18));
		$row->AddInputField("ACCESS_TOKEN", array("size"=>18));
		$row->AddInputField("ACCESS_TOKEN_SECRET", array("size"=>18));

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
				'TWITTER_LINK' => 'DETAIL_PAGE_URL',
				'TWITTER_PHOTO' => 'DETAIL_PICTURE',
				'TWITTER_MESSAGE_SEP' => 'Y',
				'TWITTER_MESSAGE' => GetMessage('TWITTER_MESSAGE_DEFAULT'),
				'TWITTER_UTM_SOURCE' => 'twitter',
			);
		}
		foreach($arValues as $k=>$v)
		{
			if(isset($_POST[$k]))
				if(empty($_POST[$k]) && $k != 'TWITTER_MESSAGE')
					unset($_POST[$k]);
		}
		$arPostParams = array(
			'TABS' => array(
				'TWITTER_TAB' => array(
					'NAME' => GetMessage('TWITTER_TAB_NAME'),
					'TITLE' => GetMessage('TWITTER_TAB_TITLE')
				)
			),
			'PARAMS' => array(
				'TWITTER_PHOTO' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('POST_TWITTER_PHOTO'),
					'TYPE' => 'LIST',
					'VALUES' => $values,
					'VALUE' => $arValues['TWITTER_PHOTO'],
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1011,
					'HELP' => GetMessage('POST_TWITTER_PHOTO_HELP'),
				),
				'TWITTER_PHOTOS' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('POST_TWITTER_PHOTOS'),
					'DESCRIPTION' => GetMessage('POST_TWITTER_PHOTOS_DESCRIPTION'),
					'TYPE' => 'LIST',
					'VALUES' => $values,
					'VALUE' => $arValues['TWITTER_PHOTOS'],
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1020,
					'HELP' => GetMessage('POST_TWITTER_PHOTOS_HELP'),
				),
				'TWITTER_LINK' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('POST_TWITTER_LINK'),
					'DESCRIPTION' => GetMessage('POST_TWITTER_LINK_DESCRIPTION'),
					'TYPE' => 'LIST',
					'VALUES' => $values,
					'VALUE' => $arValues['TWITTER_LINK'],
					'SIZE' => 0,
					'SORT' => 1030,
					'HELP' => GetMessage('POST_TWITTER_LINK_HELP'),
				),
				'TWITTER_MESSAGE_SEP' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('POST_TWITTER_MESSAGE_SEP'),
					'TYPE' => 'CHECKBOX',
					'VALUE' => $arValues['TWITTER_MESSAGE_SEP'],
					'SORT' => 1040,
					'HELP' => GetMessage('POST_TWITTER_MESSAGE_SEP_HELP'),
				),
				'TWITTER_MESSAGE' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('POST_TWITTER_MESSAGE'),
					'DESCRIPTION' => GetMessage('POST_TWITTER_MESSAGE_DESCRIPTION'),
					'TYPE' => 'TEXTAREA',
					'VALUE' => $arValues['TWITTER_MESSAGE'],
					'CHOISE' => 'SIMPLE',
					'VALUES' => $values,
					'SORT' => 1050,
					'HELP' => GetMessage('POST_TWITTER_MESSAGE_HELP'),
				),
				'TWITTER_UTM_SOURCE' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('UTM_SOURCE'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['TWITTER_UTM_SOURCE'],
					'SORT' => 3000,
					'HELP' => GetMessage('UTM_SOURCE_HELP')
				),
				'TWITTER_UTM_MEDIUM' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('UTM_MEDIUM'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['TWITTER_UTM_MEDIUM'],
					'SORT' => 3010,
					'HELP' => GetMessage('UTM_MEDIUM_HELP')
				),
				'TWITTER_UTM_CAMPAIGN' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('UTM_CAMPAIGN'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['TWITTER_UTM_CAMPAIGN'],
					'SORT' => 3020,
					'HELP' => GetMessage('UTM_CAMPAIGN_HELP')
				),
				'TWITTER_UTM_TERM' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('UTM_TERM'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['TWITTER_UTM_TERM'],
					'SORT' => 3030,
					'HELP' => GetMessage('UTM_TERM_HELP')
				),
				'TWITTER_UTM_CONTENT' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('UTM_CONTENT'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['TWITTER_UTM_CONTENT'],
					'SORT' => 3040,
					'HELP' => GetMessage('UTM_CONTENT_HELP')
				),
			),
		);

		if(PostingFunc::isPlus())
		{
			$arPostParams['PARAMS']['TWITTER_PUBLICATION_MODE'] = array(
				'TAB' => 'TWITTER_TAB',
				'NAME' => GetMessage('TWITTER_PUBLICATION_MODE'),
				'TYPE' => 'LIST',
				'VALUES' => array(
					'del_add' => GetMessage('TWITTER_PUBLICATION_MODE_DEL_ADD'),
					'reply' => GetMessage('TWITTER_PUBLICATION_MODE_REPLY'),
					'none' => GetMessage('TWITTER_PUBLICATION_MODE_NONE'),
				),
				'VALUE' => $arValues['TWITTER_PUBLICATION_MODE'],
				'HELP' => GetMessage('TWITTER_PUBLICATION_MODE_HELP'),
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
				'NAME' => 'Autoposting to Twitter ['.Func::GetNextIdDB().']',
				'IS_ENABLE' => 'Y',
			);
		}
		$arModuleParams = array(
			'TAB_CONTROL_POSTFIX' => 'twitter',
			'FORM' => array(
				'PARAMS' => array(
					'ID' => $index,
				),
				'TYPE' => array('WITH_PARAMS', 'NOT_DEFAULT'),
			),
			'TABS' => array(
				'TWITTER_TAB' => array(
					'NAME' => GetMessage('TWITTER_TAB_NAME'),
					'TITLE' => GetMessage('TWITTER_TAB_TITLE')
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
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('TWITTER_ACCOUNTS_NAME'),
					'DESCRIPTION' => GetMessage('TWITTER_ACCOUNTS_NAME_DESCRIPTION'),
					'VALUE' => $arValues['NAME'],
					'TYPE' => 'STRING',
					'REQUIRED' => 'Y',
					'SORT' => 100,
				),
				'IS_ENABLE' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('TWITTER_IS_ENABLE'),
					'VALUE' => $arValues['IS_ENABLE'],
					'TYPE' => 'CHECKBOX',
					'SORT' => 150,
				),
				'API_KEY' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('TWITTER_API_KEY'),
					'VALUE' => $arValues['API_KEY'],
					'TYPE' => 'STRING',
					'SORT' => 200,
				),
				'API_SECRET' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('TWITTER_API_SECRET'),
					'VALUE' => $arValues['API_SECRET'],
					'TYPE' => 'STRING',
					'SORT' => 250,
				),
				'ACCESS_TOKEN' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('TWITTER_ACCESS_TOKEN'),
					'VALUE' => $arValues['ACCESS_TOKEN'],
					'TYPE' => 'STRING',
					'SORT' => 300,
				),
				'ACCESS_TOKEN_SECRET' => array(
					'TAB' => 'TWITTER_TAB',
					'NAME' => GetMessage('TWITTER_ACCESS_TOKEN_SECRET'),
					'DESCRIPTION' => GetMessage('TWITTER_ACCESS_TOKEN_SECRET_DESCRIPTION'),
					'VALUE' => $arValues['ACCESS_TOKEN_SECRET'],
					'TYPE' => 'STRING',
				),
			)
		);

		$hlp = PostingFunc::vettich_service('get_url', 'url=autoposting.twitter.video_help');
		if(!empty($hlp['url']))
		{
			$arModuleParams['TABS']['TWITTER_TAB_VIDEO'] = array(
				'NAME' => GetMessage('TWITTER_TAB_VIDEO_NAME'),
				'TITLE' => GetMessage('TWITTER_TAB_VIDEO_TITLE')
			);
			$arModuleParams['PARAMS']['video_help'] = array(
				'TAB' => 'TWITTER_TAB_VIDEO',
				'TYPE' => 'NOTE',
				'TEXT' => PostingFunc::get_youtube_frame($hlp['url']),
			);
		}

		return $arModuleParams;
	}
}