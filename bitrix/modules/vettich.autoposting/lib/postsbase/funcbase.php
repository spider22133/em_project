<?
namespace Vettich\Autoposting\PostsBase;
use Vettich\Autoposting\PostingFunc;

IncludeModuleLangFile(__FILE__);

class FuncBase extends PostBase
{
	const DBTABLE = '';
	const DBOPTIONTABLE = '';
	const ACCPREFIX = '';

	static function get_name()
	{
		return GetMessage(static::ACCPREFIX.'_NAME');
	}

	static function GetValues($ID, $dbtable = null)
	{
		if($dbtable == null)
			$dbtable = static::DBTABLE;
		return PostingFunc::GetValues($ID, $dbtable);
	}

	static function GetAccountValues($ID, $IDOPT, $db=null, $dbopt=null)
	{
		if($db == null)
			$db = static::DBTABLE;
		if($dbopt == null)
			$dbopt = static::DBOPTIONTABLE;
		$arResult = self::GetValues($ID, $db);
		$arResult = array_merge_recursive($arResult, self::GetValues($IDOPT, $dbopt));
		return $arResult;
	}

	static function GetNextIdDB($dbtable=null)
	{
		if($dbtable == null)
			$dbtable = static::DBTABLE;
		return PostingFunc::GetNextIdDB($dbtable);
	}

	static function isSupport()
	{
		return true;
	}

	static function dontSupportMsg()
	{
		return 'This social network is supported.';
	}

	static function getBaseDir()
	{
		return dirname(__DIR__).'/posts';
	}
}