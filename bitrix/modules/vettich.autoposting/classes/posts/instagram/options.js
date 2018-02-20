var vettich_vk_get_access_token = function(elem)
{
	if(!elem)
		return;

	// var ff = $(elem).parents('.voptions-group-div');
	// cnt = ff.attr('rel');
	group_id = $('#group_id').val();
	if(!group_id)
	{
		alert(ALERT_NOT_GROUP_ID);
		return;
	}
	// url = 'http://oauth.vk.com/authorize?client_id=' + group_id + '&scope=wall,offline,photos&redirect_uri=http://' + VOptionsServerUri + '/bitrix/admin/vettich_autoposting_vk.php&response_type=token&display=mobile&state=' + cnt;
	url = 'http://oauth.vk.com/authorize?client_id=' + group_id + '&scope=friends,wall,groups,offline,photos,video&redirect_uri=https://oauth.vk.com/blank.html&response_type=token&display=page';
	window.open(url, 'VOptionsVk');
} 

var vettich_vk_access_token = function(url)
{
	if(!url)
		return;

	mch = url.match(/#.*/i);
	access_token = '';
	if(mch != null)
	{
		access_token = mch[0].match(/access_token=(\w+)/i);
		// state = mch[0].match(/state=(\w+)/i)
		if(access_token)
		{
			group_cnt = state ? state[1] : 0;
			$('#access_token').val(access_token[1]);
		}
	}
}

var vettich_vk_access_token_mod = function(elem)
{
	if(!elem)
		return;

	// var ff = $(elem).parents('.voptions-group-div');
	// cnt = ff.attr('rel');
	access_token_id = $('#access_token');
	access_token = access_token_id.val().match(/access_token=(\w+)/i);
	if(access_token != null)
	{
		access_token_id.val(access_token[1]);
	}
	else
	{
		alert(ALERT_NOT_ACCESS_TOKEN);
	}
}