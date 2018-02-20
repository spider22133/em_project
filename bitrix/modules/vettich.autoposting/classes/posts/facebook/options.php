<?
IncludeModuleLangFile(__FILE__);

$arPostParams = array(
	'TABS' => array(
		'FB_TAB' => array(
			'NAME' => GetMessage('FB_TAB_NAME'),
			'TITLE' => GetMessage('FB_TAB_TITLE')
		)
	),
	'PARAMS' => array(
		'is_fb_enable' => array(
			'TAB' => 'FB_TAB',
			'NAME' => GetMessage('IS_FB_ENABLE'),
			'TYPE' => 'CHECKBOX',
			// 'DEFAULT' => 'Y',
		),
		'fb_log_success' => array(
			'TAB' => 'FB_TAB',
			'NAME' => GetMessage('fb_log_success'),
			'TYPE' => 'CHECKBOX',
			// 'DEFAULT' => 'Y',
		),
		'fb_log_error' => array(
			'TAB' => 'FB_TAB',
			'NAME' => GetMessage('fb_log_error'),
			'TYPE' => 'CHECKBOX',
			// 'DEFAULT' => 'Y',
		),
	)
);

/*$arIncludeJS[] = dirname(__FILE__).'/options.js';
?>
<script type="text/javascript">
	var ALERT_NOT_APP_ID = '<?=GetMessage('ALERT_NOT_APP_ID')?>';
</script>
*/