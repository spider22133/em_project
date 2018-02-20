<?
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	'vettich.autoposting',
	array(
		'VOptions' => 'classes/VOptions.php',
	)
);

define('VETTICH_AUTOPOSTING_DIR', __DIR__);

/**
* Debug
*/
class CVDebug
{
	static private $filename = 'debug.html';
	static private $dir = __DIR__;

	function __construct($arg)
	{
	}

	static function log($data, $section = 'main')
	{
		
	}
}

?>