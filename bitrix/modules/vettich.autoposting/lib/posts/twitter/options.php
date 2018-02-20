<?
IncludeModuleLangFile(__FILE__);

$arPostParams = array(
	'TABS' => array(
		'TWITTER_TAB' => array(
			'NAME' => GetMessage('TWITTER_TAB_NAME'),
			'TITLE' => GetMessage('TWITTER_TAB_TITLE')
		)
	),
	'PARAMS' => array(
		'is_twitter_enable' => array(
			'TAB' => 'TWITTER_TAB',
			'NAME' => GetMessage('IS_TWITTER_ENABLE'),
			'TYPE' => 'CHECKBOX',
			// 'DEFAULT' => 'Y',
		),
		'twitter_log_success' => array(
			'TAB' => 'TWITTER_TAB',
			'NAME' => GetMessage('twitter_log_success'),
			'TYPE' => 'CHECKBOX',
			// 'DEFAULT' => 'Y',
		),
		'twitter_log_error' => array(
			'TAB' => 'TWITTER_TAB',
			'NAME' => GetMessage('twitter_log_error'),
			'TYPE' => 'CHECKBOX',
			// 'DEFAULT' => 'Y',
		),
	)
);
