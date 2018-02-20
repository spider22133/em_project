<?

$MESS['VK_TAB_NAME'] = 'Vkontakte';
$MESS['VK_TAB_TITLE'] = 'Settings for Vkontakte';
$MESS['VK_PAGE_TITLE'] = 'Accounts Vkontakte';
$MESS['IS_VK_ENABLE'] = 'To enable the plugin Vkontakte';
$MESS['VK_SETTINGS'] = 'Settings Vkontakte';
$MESS['VK_ACCOUNTS'] = 'Accounts';
$MESS['VK_ACCOUNTS_NAME'] = 'Name';
$MESS['VK_IS_ENABLE'] = 'Account is enabled';
$MESS['IS_GROUP_PUBLISH'] = 'To publish on behalf of the group';
$MESS['GROUP_PUBLISH_ID'] = 'ID group/user';
$MESS['VK_ACCOUNTS_GROUP_ID'] = 'App ID';
$MESS['VK_ACCOUNTS_ACCESS_TOKEN'] = 'Access Token';
$MESS['VK_ACCOUNTS_GET_ACCESS_TOKEN'] = 'Get access token';
$MESS['GROUP_PUBLISH_GROUP'] = 'Group';
$MESS['GROUP_PUBLISH_USER'] = 'User';
$MESS['POST_VK_PUBLISH_DATE'] = 'Date publish';
$MESS['POST_VK_PHOTO'] = 'Main picture';
$MESS['POST_VK_PHOTOS'] = 'Advanced pictures';
$MESS['POST_VK_LINK_ATTACHMENT'] = 'The link in the attachment';
$MESS['POST_VK_LINK'] = 'The link to the item (in attachment)';
$MESS['POST_VK_MESSAGE'] = 'Message';
$MESS['vk_log_success'] = 'To journal a successful publication in Vkontakte';
$MESS['vk_log_error'] = 'Log errors when you publish';

$MESS['ALERT_NOT_ACCESS_TOKEN'] = 'First choose the link with access_token';
$MESS['ALERT_NOT_GROUP_ID'] = 'First fill in the "App ID"';

$MESS['VK_ACCOUNTS_NAME_DESCRIPTION'] = 'Specify a name for this account. It will be displayed in the list of available accounts.';
$MESS['GROUP_PUBLISH_ID_DESCRIPTION'] = 'The user ID can be found in the VC in My Settings -&gt; General -&gt; page Number.<br/>
	To view the group ID can:<br/>
	1) click on the link "Statistics" page located on the right side of Your group https://vk.com/stats?gid=&lt;group_id&gt; - at the end of the link will be the number in Your group<br/>
	2) go in discussions, in the address bar the address will be https://vk.com/board&lt;group_id&gt; - group_id and will be group ID<br/>
	3) look on the Members page of the community to do this, open the Members link in a new tab, and address bar will be https://vk.com/search?c[section]=people&c[group]=&lt;group_id&gt; - group_id is the ID of your group';
$MESS['VK_ACCOUNTS_GROUP_ID_DESCRIPTION'] = 'You need to register a Standalone app on the wanderer <a href="https://vk.com/editapp?act=create" target="_blank">https://vk.com/editapp?act=create</a> and to write the application ID into the field "App ID"';
$MESS['VK_ACCOUNTS_ACCESS_TOKEN_DESCRIPTION'] = 'Once You have registered a Standalone application and put his ID 
	in the field above, you must obtain an Access token, 
	to do that go to <a href="javascript:undefined" onClick="vettich_vk_get_access_token(this)">link</a>, 
	in the opened window, authorize, confirm, and the screen will display the message 
	"Please do not copy these from the address bar to third-party sites..." ("Пожалуйста, не копируйте данные из адресной строки для сторонних сайтов..."), 
	copy the address from the address bar into the field "Access Token", and click "Convert", 
	to get the Access token from the address.';
$MESS['VK_access_token_button_HTML'] = '<div class="voptions-add-button" onClick="vettich_vk_access_token_mod(this)">Convert</div>';

?> 
