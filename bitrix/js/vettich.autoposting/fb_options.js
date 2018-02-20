var vettich_fb_access_token_get = function(elem)
{
	if(!elem)
		return;

	// var ff = $(elem).parents('.voptions-group-div');
	// cnt = ff.attr('rel');
	// group_id = $('#fb_accounts-' + cnt + '-group_id').val();
	// app_id = $('#fb_accounts-' + cnt + '-app_id').val();
	// app_secret = $('#fb_accounts-' + cnt + '-app_secret').val();
	group_id = $('#GROUP_ID').val();
	app_id = $('#APP_ID').val();
	app_secret = $('#APP_SECRET').val();
	scope = 'publish_actions,manage_pages,publish_pages,public_profile';
	if(!app_id || !app_secret)
	{
		alert(ALERT_NOT_APP_ID);
		return;
	}
	callback = window.location.origin + '/bitrix/admin/vettich_autoposting_fb_callback.php';
	width = 800;
	height = 600;
	url = '/bitrix/admin/vettich_autoposting_fb_login.php?app_id=' + app_id + '&app_secret=' + app_secret + '&callback=' + callback + '&scope=' + scope;
	window.open(url, 'VOptionsFB', 'location=yes,resizable=yes,scrollbars=yes,width=' + width + ',height=' + height + ',left=' + ((window.innerWidth - width)/2) + ',top=' + ((window.innerHeight - height)/2));
} 

var vettich_fb_access_token = function(access_token, expiresAt)
{
	if(!access_token)
		return;
	$('#ACCESS_TOKEN').val(access_token);
	$('#EXPIRES_AT').val(expiresAt);
	return true;
}
