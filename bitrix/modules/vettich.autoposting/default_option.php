<?
$vettich_autoposting_default_option = array(
	'is_enable' => 'Y',
	'is_ajax_enable' => 'Y',
	'is_enable_logs' => 'Y',
);

$_dir_name = dirname(__FILE__).'/classes/posts/';
$_dir = scandir($_dir_name);
if($_dir !== false)
	foreach($_dir as $v)
	{
		if($v != '.' && $v != '..' && is_dir($_dir_name.$v))
		{
			if(file_exists($_dir_name."$v/default_options.php"))
			{
				include_once($_dir_name."$v/default_options.php");
				$vettich_autoposting_default_option = array_merge_recursive($vettich_autoposting_default_option, $default_option);
			}
		}
	}

?>