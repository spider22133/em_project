<?

$MESS['FB_NAME'] = 'Facebook';
$MESS['FB_ERROR'] = 'Error with code "#CODE#" in the account "#ACC_NAME#" when posting <a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=#IBLOCK_ID#&type=#IBLOCK_TYPE#&ID=#ID#">item</a>:<br/>
	#MESSAGE# <br/>
	<a href="http://fbdevwiki.com/wiki/Error_codes" target="_blank">Description of the error codes Facebook (not official documentation)</a>';
$MESS['FB_SUCCESS'] = 'The entry of "#ACC_NAME#" has been successfully published to <a href="#URL#" target="_blank">the group</a>';
$MESS['FB_PAGE_TITLE'] = 'Facebook accounts';
$MESS['FB_TAB_NAME'] = 'Facebook';
$MESS['FB_TAB_TITLE'] = 'Settings for Facebook';
$MESS['IS_FB_ENABLE'] = 'To enable the plugin Facebook';
$MESS['FB_SETTINGS'] = 'Settings Facebook';
$MESS['FB_ACCOUNTS'] = 'Accounts';
$MESS['FB_ACCOUNTS_NAME'] = 'Name';
$MESS['FB_IS_ENABLE'] = 'Account is enabled';
$MESS['FB_GROUP_ID'] = 'Page ID';
$MESS['FB_GROUP'] = 'Token for';
$MESS['FB_GROUP_PROFILE'] = 'Profile';
$MESS['FB_GROUP_PAGE'] = 'Page';
$MESS['FB_APP_ID'] = 'App ID';
$MESS['FB_APP_SECRET'] = 'App Secret';
$MESS['FB_ACCOUNTS_ACCESS_TOKEN'] = 'Access Token';
$MESS['FB_ACCOUNTS_GET_ACCESS_TOKEN'] = 'Get access token';
$MESS['GROUP_PUBLISH_GROUP'] = 'Group';
$MESS['GROUP_PUBLISH_USER'] = 'User';
$MESS['POST_FB_PUBLISH_DATE'] = 'Date publish';
$MESS['POST_FB_PHOTO'] = 'Picture';
$MESS['POST_FB_NAME'] = 'Block title';
$MESS['POST_FB_DESCRIPTION'] = 'Block description';
$MESS['POST_FB_LINK_ATTACHMENT'] = 'Link in the attachment';
$MESS['POST_FB_LINK'] = 'Link to the item';
$MESS['POST_FB_MESSAGE'] = 'Message';
$MESS['fb_log_success'] = 'To journal a successful publication in Facebook';
$MESS['fb_log_error'] = 'Log errors when you publish';
$MESS['ALERT_NOT_APP_ID'] = 'First fill in the \'app_id\' and \'app_secret\'';
$MESS['FB_ACCOUNTS_NAME_DESCRIPTION'] = 'Specify a name for this account. It will be displayed in the list of available accounts.';
$MESS['FB_GROUP_ID_DESCRIPTION'] = 'ID page, profile or group you want to publish. To publish items on your profile, instead ID write "me", otherwise there will be errors when you publish';
$MESS['FB_HELP1_NAME'] = 'Instructions for setting the Facebook account';
$MESS['FB_HELP1_TEXT'] = '
To get started, create an app (if it is not yet created) <a href="https://developers.facebook.com/apps/">this page</a> (after creating, be sure to specify in the settings of the application domain, which will be published materials, such as http://example.ru). Next, enter in the appropriate fields application data (App ID, App Secret). Then click on the button "Get Access Token" and in the opened window, confirm that all the rights for the app (if they have not previously been podtverjdena). Then, in the "page ID" enter the ID of your page, group or profile. And don\'t forget to fill in the "Name" field, because without it, the account will not be saved. At the end of the settings, click "Save".';
$MESS['FB_access_token_button'] = '<div class="voptions-add-button" onClick="vettich_fb_access_token_get(this)">Get Access Token</div>';
$MESS['FB_WARNING_NOT_URL'] = 'The entry of "#ACC_NAME#" has been successfully published to <a href="#URL#" target="_blank"></a>, but was not attached picture, as you do not set the "Link to element"';
