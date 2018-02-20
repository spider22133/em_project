<?
namespace Vettich\Autoposting\PostsBase;
use Vettich\Autoposting\PostingOption;

IncludeModuleLangFile(__FILE__);

class OptionBase extends PostBase
{
	static function get_list()
	{
		$db = static::$dbTable;
		$rs = $db::GetList();
		$arRes = array();
		while($ar = $rs->Fetch())
			$arRes[$ar['ID']] = $ar['NAME'];
		return $arRes;
	}

	static function get_default()
	{
		return array();
	}

	static function PageTitle()
	{
		return GetMessage(static::$accPrefix.'_PAGE_TITLE');
	}

	static function EditPageTitle($id=1)
	{
		if($id > 0)
			return GetMessage(static::$accPrefix.'_EDIT_PAGE_TITLE');
		return GetMessage(static::$accPrefix.'_ADD_PAGE_TITLE');
	}

	static function GetFields()
	{
		return array(
			'ID' => 'ID',
			// 'optname' => GetMessage('optname'),
		);
	}

	static function ChangeRow(&$row)
	{
		if(empty($row))
			return;
	}

	static function GetArModuleParamsPosts($index, $iblock_id=false)
	{
		return array();
	}

	static function GetArModuleParams($index)
	{
		return array();
	}

	static function SaveParams($index=0)
	{
		PostingOption::SaveParams($index, static::$dbTable);
	}

	static function GetList($sort = array('ID'))
	{
		$arResult = array();
		$db = static::$dbTable;
		$rs = $db::GetList(array(
			'order' => $sort,
		));
		while($ar = $rs->Fetch())
		{
			$arResult[$ar['ID']] = $ar;
		}
		return $arResult;
	}

	static function Save($id, $arFields)
	{
		PostingOption::Save($id, $arFields, static::$dbTable);
	}

	static function SaveFields($fields)
	{
		PostingOption::SaveFields($fields, static::$dbTable);
	}

	static function Delete($id)
	{
		PostingOption::Delete($id, static::$dbTable);
	}
}