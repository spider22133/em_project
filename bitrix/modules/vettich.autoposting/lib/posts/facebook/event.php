<?
namespace Vettich\Autoposting\Posts\facebook;
use Vettich\Autoposting\PostsBase\EventBase;

class Event extends EventBase
{
	static $dbTable = Func::DBTABLE;
	static $dbOptionTable = Func::DBOPTIONTABLE;
	static $namespace = __NAMESPACE__;
}
