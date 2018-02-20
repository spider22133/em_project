<?
namespace Vettich\Autoposting\Posts\facebook;
use Vettich\Autoposting\PostsBase\FuncBase;

IncludeModuleLangFile(__FILE__);

class Func extends FuncBase
{
	const DBTABLE = '\\Vettich\\Autoposting\\Posts\\facebook\\DBTable';
	const DBOPTIONTABLE = '\\Vettich\\Autoposting\\Posts\\facebook\\DBOptionTable';
	const ACCPREFIX = 'FB';

	static function isSupport()
	{
		if(version_compare(PHP_VERSION, '5.4.0', '<'))
			return false;
		return true;
	}

	static function dontSupportMsg()
	{
		return GetMessage('vettich.autoposting_facebook_dont_support');
	}
}