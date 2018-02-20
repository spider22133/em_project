<?
namespace Vettich\Autoposting\Posts\pinterest;
use Vettich\Autoposting\PostsBase\FuncBase;

IncludeModuleLangFile(__FILE__);

class Func extends FuncBase
{
	const DBTABLE = '';
	const DBOPTIONTABLE = '';
	const ACCPREFIX = 'PINTEREST';

	static function get_name()
	{
		return 'Pinterest';
	}

	static function getBaseDir()
	{
		return dirname(__DIR__);
	}
}
