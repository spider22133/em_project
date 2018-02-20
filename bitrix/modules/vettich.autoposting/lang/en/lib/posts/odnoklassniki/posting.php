<?

$MESS['ODNOKLASSNIKI_NAME'] = 'Odnoklassniki';
$MESS['ODNOKLASSNIKI_ERROR'] = 'Error code "#CODE#" in the account "#ACC_NAME#" when you publish an <a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=#IBLOCK_ID#&type=#IBLOCK_TYPE#&ID=#ID#">item</a>:<br/>
	#MESSAGE# <br/>';
$MESS['ODNOKLASSNIKI_SUCCESS'] = '<a href="#URL#" target="_blank">Entry</a> successfully published through the account "#ACC_NAME#"';
$MESS['ODNOKLASSNIKI_TAB_NAME'] = 'Odnoklassniki';
$MESS['ODNOKLASSNIKI_TAB_TITLE'] = 'Settings Odnoklassniki';
$MESS['ODNOKLASSNIKI_PAGE_TITLE'] = 'Accounts Odnoklassniki';
$MESS['IS_ODNOKLASSNIKI_ENABLE'] = 'Is enable Odnoklassniki';
$MESS['ODNOKLASSNIKI_SETTINGS'] = 'Settings Odnoklassniki';
$MESS['ODNOKLASSNIKI_ACCOUNTS'] = 'Accounts';
$MESS['ODNOKLASSNIKI_ACCOUNTS_NAME'] = 'Name';
$MESS['ODNOKLASSNIKI_IS_ENABLE'] = 'Is enable';
$MESS['ODNOKLASSNIKI_IS_GROUP_PUBLISH'] = 'On behalf of the group';
$MESS['ODNOKLASSNIKI_GROUP_ID'] = 'ID group';
$MESS['ODNOKLASSNIKI_API_ID'] = 'Application ID';
$MESS['ODNOKLASSNIKI_API_PUBLIC_KEY'] = 'Application public key';
$MESS['ODNOKLASSNIKI_API_SECRET_KEY'] = 'Application secret key';
$MESS['ODNOKLASSNIKI_ACCESS_TOKEN'] = 'Access Token';
$MESS['ODNOKLASSNIKI_NOTE'] = 'Statement to populate fields';
$MESS['POST_ODNOKLASSNIKI_PUBLISH_DATE'] = 'Date of publication';
$MESS['POST_ODNOKLASSNIKI_PHOTO'] = 'The main picture';
$MESS['POST_ODNOKLASSNIKI_PHOTO_OTHER'] = 'Additional images';
$MESS['POST_ODNOKLASSNIKI_LINK'] = 'Link';
$MESS['POST_ODNOKLASSNIKI_MESSAGE'] = 'Message';
$MESS['odnoklassniki_log_success'] = 'To journal a successful publication in Odnoklassniki';
$MESS['odnoklassniki_log_error'] = 'Log errors when you publish';
$MESS['ODNOKLASSNIKI_ACCOUNTS_NAME_DESCRIPTION'] = 'Specify a name for this account. It will be displayed in the list of available accounts.';
$MESS['ODNOKLASSNIKI_NOTE_TEXT'] = '<div align="justify">
	<a href="http://ok.ru/devaccess" target="_blank">Sign up</a> as a developer.
	<a href="http://apiok.ru/wiki/pages/viewpage.action?pageId=42476486" targer="_blank">Build an app</a> on the
	<a href="http://ok.ru/dk?st.cmd=appEdit&st._aid=Apps_Info_MyDev_AddApp" target="_blank">link</a> 
	(if the link does not work, go to Odnoklassniki Games -> My uploads -> Add App).
	Fill in the fields "Name", "Short name", "Description" relevant information.
	Next, select the "Application type" -> "External", field "Status" -> "Public",
	set the following permissions for the app to "Required" (or "Optional"):
	<ul>
		<li>Setting status (SET_STATUS)</li>
		<li>Editing photos and photo albums (PHOTO_CONTENT)</li>
		<li>Access to the general information (VALUABLE_ACCESS)</li>
		<li>Access to groups (GROUP_CONTENT)</li>
	</ul>
	If what is right is not in the list, you need to ask the administration of the Classmates in the mail api-support@ok.ru.
	Rules the content on the page ....
	Once You have created the application, You will receive a letter with the data of the created App ID, App public key and App secret key.
	These data must be entered in the appropriate fields above.
	To obtain Access Token you must pass in the settings of your application, and at the bottom of the page near the "Unlimited access_token" a button to "Get access_token",
	when clicked displays the token that you want to copy in the appropriate field above.
	</div>';
$MESS['ODNOKLASSNIKI_WARNING_URL'] = '<a href="#URL#" target="_blank">Recording</a> successfully published through the "account#ACC_NAME#", but there was an error with the code "#ERR_CODE#" when you try to attach the link <a href="#URL_ATTACH#" target="_blank">#URL_ATTACH#</a>:<br> #ERR_MESSAGE#<br>';
$MESS['ODNOKLASSNIKI_ERROR_UNKNOWN'] = 'An unknown error occurred while publishing <a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=#IBLOCK_ID#&type=#IBLOCK_TYPE#&ID=#ID#">element</a>';
