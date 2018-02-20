<?
namespace Vettich\Autoposting\Posts\instagram;
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
			'NAME' => GetMessage('INSTAGRAM_ACCOUNTS_NAME'),
			'IS_ENABLE' => GetMessage('INSTAGRAM_IS_ENABLE'),
			'LOGIN' => GetMessage('INSTAGRAM_LOGIN'),
			'PASSWORD' => GetMessage('INSTAGRAM_PASSWORD'),
		);
	}

	static function ChangeRow(&$row, $values)
	{
		if(empty($row))
			return;
		
		$row->AddInputField("NAME", array("size"=>20));
		$row->AddInputField("LOGIN", array("size"=>20));
		$row->AddEditField("PASSWORD", '<input type="password" value="'.$row->arRes['password'].'" name="FIELDS['.$row->arRes['id'].'][PASSWORD]">');

		$row->AddViewField('IS_ENABLE', $row->arRes['IS_ENABLE'] == 'Y' ? GetMessage('YES') : GetMessage('NO'));
		$row->AddViewField('PASSWORD', str_repeat('*', strlen($row->arRes['PASSWORD'])));
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
				'INSTAGRAM_PHOTO' => 'DETAIL_PICTURE',
				'INSTAGRAM_MESSAGE' => GetMessage('INSTAGRAM_MESSAGE_DEFAULT'),
				'INSTAGRAM_UTM_SOURCE' => 'instagram',
			);
		}
		foreach($arValues as $k=>$v)
		{
			if(isset($_POST[$k]))
				if(empty($_POST[$k]) && $k != 'INSTAGRAM_MESSAGE')
					unset($_POST[$k]);
		}
		$arPostParams = array(
			'TABS' => array(
				'INSTAGRAM_TAB' => array(
					'NAME' => GetMessage('INSTAGRAM_TAB_NAME'),
					'TITLE' => GetMessage('INSTAGRAM_TAB_TITLE')
				)
			),
			'PARAMS' => array(
				'INSTAGRAM_PHOTO' => array(
					'TAB' => 'INSTAGRAM_TAB',
					'NAME' => GetMessage('POST_INSTAGRAM_PHOTO'),
					'TYPE' => 'LIST',
					'VALUES' => $values,
					'VALUE' => $arValues['INSTAGRAM_PHOTO'],
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1011,
					'HELP' => GetMessage('POST_INSTAGRAM_PHOTO_HELP'),
				),
				'INSTAGRAM_PHOTO_OTHER' => array(
					'TAB' => 'INSTAGRAM_TAB',
					'NAME' => GetMessage('POST_INSTAGRAM_PHOTO_OTHER'),
					'TYPE' => 'LIST',
					'VALUES' => $values,
					'VALUE' => $arValues['INSTAGRAM_PHOTO_OTHER'],
					'MULTIPLE' => 'N',
					'SIZE' => 0,
					'SORT' => 1020,
					'HELP' => GetMessage('POST_INSTAGRAM_PHOTO_OTHER_HELP'),
				),
				'INSTAGRAM_MESSAGE' => array(
					'TAB' => 'INSTAGRAM_TAB',
					'NAME' => GetMessage('POST_INSTAGRAM_MESSAGE'),
					'TYPE' => 'TEXTAREA',
					'VALUE' => $arValues['INSTAGRAM_MESSAGE'],
					'CHOISE' => 'SIMPLE',
					'VALUES' => $values,
					'SORT' => 1050,
					'HELP' => GetMessage('POST_INSTAGRAM_MESSAGE_HELP'),
				),
				'INSTAGRAM_UTM_SOURCE' => array(
					'TAB' => 'INSTAGRAM_TAB',
					'NAME' => GetMessage('UTM_SOURCE'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['INSTAGRAM_UTM_SOURCE'],
					'SORT' => 3000,
					'HELP' => GetMessage('UTM_SOURCE_HELP')
				),
				'INSTAGRAM_UTM_MEDIUM' => array(
					'TAB' => 'INSTAGRAM_TAB',
					'NAME' => GetMessage('UTM_MEDIUM'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['INSTAGRAM_UTM_MEDIUM'],
					'SORT' => 3010,
					'HELP' => GetMessage('UTM_MEDIUM_HELP')
				),
				'INSTAGRAM_UTM_CAMPAIGN' => array(
					'TAB' => 'INSTAGRAM_TAB',
					'NAME' => GetMessage('UTM_CAMPAIGN'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['INSTAGRAM_UTM_CAMPAIGN'],
					'SORT' => 3020,
					'HELP' => GetMessage('UTM_CAMPAIGN_HELP')
				),
				'INSTAGRAM_UTM_TERM' => array(
					'TAB' => 'INSTAGRAM_TAB',
					'NAME' => GetMessage('UTM_TERM'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['INSTAGRAM_UTM_TERM'],
					'SORT' => 3030,
					'HELP' => GetMessage('UTM_TERM_HELP')
				),
				'INSTAGRAM_UTM_CONTENT' => array(
					'TAB' => 'INSTAGRAM_TAB',
					'NAME' => GetMessage('UTM_CONTENT'),
					'TYPE' => 'STRING',
					'VALUE' => $arValues['INSTAGRAM_UTM_CONTENT'],
					'SORT' => 3040,
					'HELP' => GetMessage('UTM_CONTENT_HELP')
				),
			),
		);

		if(PostingFunc::isPlus())
		{
			$arPostParams['PARAMS']['INSTAGRAM_PUBLICATION_MODE'] = array(
				'TAB' => 'INSTAGRAM_TAB',
				'NAME' => GetMessage('INSTAGRAM_PUBLICATION_MODE'),
				'TYPE' => 'LIST',
				'VALUES' => array(
					'update' => GetMessage('INSTAGRAM_PUBLICATION_MODE_UPDATE'),
					'del_add' => GetMessage('INSTAGRAM_PUBLICATION_MODE_DEL_ADD'),
					'none' => GetMessage('INSTAGRAM_PUBLICATION_MODE_NONE'),
				),
				'VALUE' => $arValues['INSTAGRAM_PUBLICATION_MODE'],
				'HELP' => GetMessage('INSTAGRAM_PUBLICATION_MODE_HELP'),
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
				'NAME' => 'Autoposting to Instagram ['.Func::GetNextIdDB().']',
				'IS_ENABLE' => 'Y',
			);
		}
		$arModuleParams = array(
			'TAB_CONTROL_POSTFIX' => 'instagram',
			'FORM' => array(
				'PARAMS' => array(
					'ID' => $index,
				),
				'TYPE' => array('WITH_PARAMS', 'NOT_DEFAULT'),
			),
			'TABS' => array(
				'INSTAGRAM_TAB' => array(
					'NAME' => GetMessage('INSTAGRAM_TAB_NAME'),
					'TITLE' => GetMessage('INSTAGRAM_TAB_TITLE')
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
					'TAB' => 'INSTAGRAM_TAB',
					'NAME' => GetMessage('INSTAGRAM_ACCOUNTS_NAME'),
					'DESCRIPTION' => GetMessage('INSTAGRAM_ACCOUNTS_NAME_DESCRIPTION'),
					'VALUE' => $arValues['NAME'],
					'TYPE' => 'STRING',
					'REQUIRED' => 'Y',
					'SORT' => 100,
				),
				'IS_ENABLE' => array(
					'TAB' => 'INSTAGRAM_TAB',
					'NAME' => GetMessage('INSTAGRAM_IS_ENABLE'),
					'VALUE' => $arValues['IS_ENABLE'],
					'TYPE' => 'CHECKBOX',
					'SORT' => 150,
				),
				'LOGIN' => array(
					'TAB' => 'INSTAGRAM_TAB',
					'NAME' => GetMessage('INSTAGRAM_LOGIN'),
					'VALUE' => $arValues['LOGIN'],
					'TYPE' => 'STRING',
					'SORT' => 200,
				),
				'PASSWORD' => array(
					'TAB' => 'INSTAGRAM_TAB',
					'NAME' => GetMessage('INSTAGRAM_PASSWORD'),
					'DESCRIPTION' => GetMessage('INSTAGRAM_PASSWORD_DESCRIPTION'),
					'VALUE' => $arValues['PASSWORD'],
					'TYPE' => 'PASSWORD',
					'SORT' => 250,
				),
			)
		);

		$hlp = PostingFunc::vettich_service('get_url', 'url=autoposting.instagram.video_help');
		if(!empty($hlp['url']))
		{
			$arModuleParams['TABS']['INSTAGRAM_TAB_VIDEO'] = array(
				'NAME' => GetMessage('INSTAGRAM_TAB_VIDEO_NAME'),
				'TITLE' => GetMessage('INSTAGRAM_TAB_VIDEO_TITLE')
			);
			$arModuleParams['PARAMS']['video_help'] = array(
				'TAB' => 'INSTAGRAM_TAB_VIDEO',
				'TYPE' => 'NOTE',
				'TEXT' => PostingFunc::get_youtube_frame($hlp['url']),
			);
		}

		return $arModuleParams;
	}
}