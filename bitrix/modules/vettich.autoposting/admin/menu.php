<?
IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight("vettich.autoposting")>"D")
{
	if(!CModule::IncludeModule('vettich.autoposting'))
		return false;
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/css/vettich.autoposting/posts.css');

	$aMenu = array(
		"parent_menu"	=> "global_menu_services",
		"sort"			=> 100,
		'icon'			=> 'vettich_autoposting',
		"text"			=> GetMessage('VettichAutopostingMenu_Text').' (v. '.Vettich\Autoposting\PostingFunc::getVersion().')',
		"items_id"		=> "vettich_autoposting",
		"items"			=> array(
			array(
				"text"		=> GetMessage('VettichAutopostingMenu_Posts'),
				"title"		=> GetMessage('VettichAutopostingMenu_Posts_Title'),
				"url"		=> "/bitrix/admin/vettich_autoposting_posts.php",
				"more_url" 	=> array('/bitrix/admin/vettich_autoposting_posts_edit.php'),
			)
		),
	);

	$posts = \Vettich\Autoposting\PostingFunc::GetPosts();
	$arMenu = array(
		'text' 		=> GetMessage('VettichPostingMenu_Accounts'),
		'items_id' 	=> 'vettich_autoposting_accounts',
		'items' 	=> array(),
	);
	foreach($posts as $post)
	{
		$func = \Vettich\Autoposting\PostingFunc::module2($post, 'func');
		$arMenu['items'][] = array(
			'text' 		=> $func::get_name(),
			'url' 		=> "/bitrix/admin/vettich_autoposting_posts_".$post.".php",
			"more_url"	=> array('/bitrix/admin/vettich_autoposting_posts_edit_'.$post.'.php'),
		);
	}

	$aMenu['items'][] = $arMenu;

	$aMenu['items'][] = array(
		"text"		=> GetMessage('VettichAutopostingMenu_Logs'),
		"url"		=> "/bitrix/admin/vettich_autoposting_logs.php",
	);
	$aMenu['items'][] = array(
		"text"		=> GetMessage('VettichAutopostingMenu_Settings'),
		"url"		=> "/bitrix/admin/settings.php?lang=ru&mid=vettich.autoposting",
	);


	return $aMenu;
}

return false;
?> 
