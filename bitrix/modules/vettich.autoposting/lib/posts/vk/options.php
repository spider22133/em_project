<?
IncludeModuleLangFile(__FILE__);

$arPostParams = array(
	'TABS' => array(
		'VK_TAB' => array(
			'NAME' => GetMessage('VK_TAB_NAME'),
			'TITLE' => GetMessage('VK_TAB_TITLE')
		)
	),
	'PARAMS' => array(
		'is_vk_enable' => array(
			'TAB' => 'VK_TAB',
			'NAME' => GetMessage('IS_VK_ENABLE'),
			'TYPE' => 'CHECKBOX',
			// 'DEFAULT' => 'Y',
		),
		'vk_log_success' => array(
			'TAB' => 'VK_TAB',
			'NAME' => GetMessage('vk_log_success'),
			'TYPE' => 'CHECKBOX',
			// 'DEFAULT' => 'Y',
		),
		'vk_log_error' => array(
			'TAB' => 'VK_TAB',
			'NAME' => GetMessage('vk_log_error'),
			'TYPE' => 'CHECKBOX',
			// 'DEFAULT' => 'Y',
		),
	)
);
