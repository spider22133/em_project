<?
/**
 * Company developer: ALTASIB
 * Site: http://www.altasib.ru
 * E-mail: dev@altasib.ru
 * @copyright (c) 2006-2017 ALTASIB
 */


$MESS['ALX_FEEDBACK_SERVER'] = "Server CRM:";
$MESS['ALX_FEEDBACK_PATH'] = "Path:";
$MESS['ALX_FEEDBACK_LOGIN'] = "Login:";
$MESS['ALX_FEEDBACK_PASS'] = "Password:";
$MESS['ALX_COMMON_CRM'] = "Common settings for all sites:";
$MESS['ALX_FEEDBACK_FOR_SITE'] = "Settings for site ";
$MESS['ALX_FEEDBACK_HASH'] = "Authorization hash (enter automatic):";
$MESS['BUTTON_SAVE'] = "Save";
$MESS['BUTTON_RESET'] = "Reset";
$MESS['BUTTON_DEF'] = "Defaults";

$MESS['ALX_FEEDBACK_LOGIN_PASS_ERROR'] = "For CRM configuration enter your username and password";
$MESS['ALX_FEEDBACK_ERROR_CONNECTION'] = "Connection is not established";
$MESS['ALX_FEEDBACK_SAVE_SETTINGS'] = "CRM settings saved";

$MESS['ALX_FEEDBACK_SITE_KEY'] = "ReCAPTCHA key for this site:<br/><i>To work with reCAPTCHA <a href='https://www.google.com/recaptcha/admin#list' target='_blank'>register</a> site to Google</i>";
$MESS['ALX_FEEDBACK_SECRET_KEY'] = "The secret key for reCAPTCHA site";

$MESS['ALX_FEEDBACK_LOGIN_PASS_ERROR_FOR'] = "For CRM configuration enter your username and password for the site ";
$MESS['ON_CHANGE_COMMON_SETTS_WARNING'] = "Warning! All unsaved changes made to the form will not be saved. Resume?";
$MESS['ALX_COMMON_CRM_AFTER_CHECK'] = "<br/><i>After you check this box, would display settings for all sites.</i>";
$MESS['ALX_COMMON_CRM_AFTER_UNCHECK'] = "<br/><i>Once you clear this checkbox, would display settings for each site.</i>";
$MESS['ALX_RECAPTCHA_SUB'] = "Widget reCAPTCHA settings";
$MESS['ALX_FEEDBACK_CRM_ALL'] = "CRM settings for all sites";

$MESS ['ALTASIB_IS'] = "Store ready-made solutions for Bitrix";
$MESS['ALTASIB_FEEDBACK_DESCR'] = '<b>Information for developers</b><br/><br/>
<b>Events of module</b><br/><br/>
<table class="internal altasib_events" width="100%">
	<thead>
		<tr>
			<th>Event</th>
			<th>Called</th>
			<th>Method</th>
			<th>From version</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>OnBeforeSendLead</td>
			<td>Before sending data to the lead in Bitrtix24</td>
			<td>AltasibFeedbackCRM::AddLead</td>
			<td>1.8.0</td>
		</tr>
	</tbody>
</table>
<br/>
<div id="altasib_description_open_btn">
	<span class="altasib_description_open_text">Reed more</span>
</div>
<div id="altasib_description_full">
<pre>
// Example of event handler OnBeforeSendLead:
AddEventHandler("altasib.feedback", "OnBeforeSendLead", "OnBeforeSendLeadHandler");
function OnBeforeSendLeadHandler($arFields) {
	// Installation source trail, depending on the filling of form fields
	if($_REQUEST["type_question_FID1"] == 19) // if selected information block section (category) with id = 19
		$arFields["SOURCE_ID"] = "TRADE_SHOW"; // Source of lead - Trade show
	else
		$arFields["SOURCE_ID"] = "WEB"; // otherwise - Website
}
</pre>
<div id="altasib_description_close_btn">
	<span class="altasib_description_open_text">Close</span>
</div>
</div>
';
?>