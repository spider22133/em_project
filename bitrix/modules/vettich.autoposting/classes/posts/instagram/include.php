<?
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	'vettich.autoposting',
	array(
		'VPostingInst' => 'classes/posts/instagram/VPostingInst.php',
		'VPostingInstFunc' => 'classes/posts/instagram/VPostingInstFunc.php',
		'VPostingInstOption' => 'classes/posts/instagram/VPostingInstOption.php',
	)
);

return array(
	'class'=> 'VPostingInst',
	'func' => 'VPostingInstFunc',
	'option' => 'VPostingInstOption',
);
