<?
namespace Vettich\Autoposting\Posts\mymailru;
use Vettich\Autoposting\PostsBase\FuncBase;

IncludeModuleLangFile(__FILE__);

class Func extends FuncBase
{
	const DBTABLE = '';
	const DBOPTIONTABLE = '';
	const ACCPREFIX = 'MYMAILRU';

	// static function get_name()
	// {
	// 	return 'MyMailRu';
	// }

	static function getBaseDir()
	{
		return dirname(__DIR__);
	}
}
