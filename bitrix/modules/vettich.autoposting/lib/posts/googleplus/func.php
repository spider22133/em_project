<?
namespace Vettich\Autoposting\Posts\googleplus;
use Vettich\Autoposting\PostsBase\FuncBase;

IncludeModuleLangFile(__FILE__);

class Func extends FuncBase
{
	const DBTABLE = '';
	const DBOPTIONTABLE = '';
	const ACCPREFIX = 'GOOGLEPLUS';

	static function get_name()
	{
		return 'Google+';
	}

	static function getBaseDir()
	{
		return dirname(__DIR__);
	}
}
