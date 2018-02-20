<?
IncludeModuleLangFile(__FILE__);

$arPostParams = array(
	'TABS' => array(
		'ODNOKLASSNIKI_TAB' => array(
			'NAME' => GetMessage('ODNOKLASSNIKI_TAB_NAME'),
			'TITLE' => GetMessage('ODNOKLASSNIKI_TAB_TITLE')
		)
	),
	'PARAMS' => array(
		'is_odnoklassniki_enable' => array(
			'TAB' => 'ODNOKLASSNIKI_TAB',
			'NAME' => GetMessage('IS_ODNOKLASSNIKI_ENABLE'),
			'TYPE' => 'CHECKBOX',
			// 'DEFAULT' => 'Y',
		),
		'odnoklassniki_log_success' => array(
			'TAB' => 'ODNOKLASSNIKI_TAB',
			'NAME' => GetMessage('odnoklassniki_log_success'),
			'TYPE' => 'CHECKBOX',
			// 'DEFAULT' => 'Y',
		),
		'odnoklassniki_log_error' => array(
			'TAB' => 'ODNOKLASSNIKI_TAB',
			'NAME' => GetMessage('odnoklassniki_log_error'),
			'TYPE' => 'CHECKBOX',
			// 'DEFAULT' => 'Y',
		),
	)
);
