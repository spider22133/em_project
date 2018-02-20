<?
// IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	'vettich.autoposting',
	array(
		'VPostingVk' => 'classes/posts/vk/VPostingVk.php',
		'VPostingVkFunc' => 'classes/posts/vk/VPostingVkFunc.php',
		'VPostingVkOption' => 'classes/posts/vk/VPostingVkOption.php',
	)
);

return array(
	'class'=> 'VPostingVk',
	'func' => 'VPostingVkFunc',
	'option' => 'VPostingVkOption',
);

