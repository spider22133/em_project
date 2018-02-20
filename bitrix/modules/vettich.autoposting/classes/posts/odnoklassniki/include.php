<?

CModule::AddAutoloadClasses(
	'vettich.autoposting',
	array(
		'VPostingOK' => 'classes/posts/odnoklassniki/VPostingOK.php',
		'VPostingOKFunc' => 'classes/posts/odnoklassniki/VPostingOKFunc.php',
		'VPostingOKOption' => 'classes/posts/odnoklassniki/VPostingOKOption.php',
	)
);

return array(
	'class'=> 'VPostingOK',
	'func' => 'VPostingOKFunc',
	'option' => 'VPostingOKOption',
);
