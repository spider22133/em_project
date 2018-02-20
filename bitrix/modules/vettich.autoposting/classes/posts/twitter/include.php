<?
IncludeModuleLangFile(__FILE__);

if(!defined('IS_TWITTER_AUTOLOAD'))
{
	define('IS_TWITTER_AUTOLOAD', true);
	require_once __DIR__.'/../../Twitter/autoload.php';
}

CModule::AddAutoloadClasses(
	'vettich.autoposting',
	array(
		'VPostingTwitter' => 'classes/posts/twitter/VPostingTwitter.php',
		'VPostingTwitterFunc' => 'classes/posts/twitter/VPostingTwitterFunc.php',
		'VPostingTwitterOption' => 'classes/posts/twitter/VPostingTwitterOption.php',
	)
);

return array(
	'class'=> 'VPostingTwitter',
	'func' => 'VPostingTwitterFunc',
	'option' => 'VPostingTwitterOption',
);
