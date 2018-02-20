<?
// IncludeModuleLangFile(__FILE__);

if (version_compare(PHP_VERSION, '5.4.0', '<'))
	return true;

if(!defined('IS_FACEBOOK_AUTOLOAD'))
{
	define('IS_FACEBOOK_AUTOLOAD', true);
	require_once dirname(__FILE__).'/../../Facebook/autoload.php';
}

CModule::AddAutoloadClasses(
	'vettich.autoposting',
	array(
		'VPostingFB' => 'classes/posts/facebook/VPostingFB.php',
		'VPostingFBFunc' => 'classes/posts/facebook/VPostingFBFunc.php',
		'VPostingFBOption' => 'classes/posts/facebook/VPostingFBOption.php',
	)
);

return array(
	'class'=> 'VPostingFB',
	'func' => 'VPostingFBFunc',
	'option' => 'VPostingFBOption',
);
