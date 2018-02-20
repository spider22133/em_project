<?
IncludeModuleLangFile(__FILE__);

$arPostParams = array(
	'TABS' => array(
		'INSTAGRAM_TAB' => array(
			'NAME' => GetMessage('INSTAGRAM_TAB_NAME'),
			'TITLE' => GetMessage('INSTAGRAM_TAB_TITLE')
		)
	),
	'PARAMS' => array(
		'is_instagram_enable' => array(
			'TAB' => 'INSTAGRAM_TAB',
			'NAME' => GetMessage('IS_INSTAGRAM_ENABLE'),
			'TYPE' => 'CHECKBOX',
			// 'DEFAULT' => 'Y',
		),
		'instagram_log_success' => array(
			'TAB' => 'INSTAGRAM_TAB',
			'NAME' => GetMessage('instagram_log_success'),
			'TYPE' => 'CHECKBOX',
			// 'DEFAULT' => 'Y',
		),
		'instagram_log_error' => array(
			'TAB' => 'INSTAGRAM_TAB',
			'NAME' => GetMessage('instagram_log_error'),
			'TYPE' => 'CHECKBOX',
			// 'DEFAULT' => 'Y',
		),
	)
);
