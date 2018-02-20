var vettich_vk_get_access_token = function(elem)
{
	if(!elem)
		return;

	var group_id = '5139034';
	if(!$('#GROUP_ID_STD').attr('checked'))
		group_id = $('#GROUP_ID').val();
	if(!group_id)
	{
		alert(ALERT_NOT_GROUP_ID);
		return;
	}
	// url = 'http://oauth.vk.com/authorize?client_id=' + group_id + '&scope=wall,offline,photos&redirect_uri=http://' + VOptionsServerUri + '/bitrix/admin/vettich_autoposting_vk.php&response_type=token&display=mobile&state=' + cnt;
	var url = 'http://oauth.vk.com/authorize?client_id=' + group_id + '&scope=friends,wall,groups,offline,photos,video&redirect_uri=https://oauth.vk.com/blank.html&response_type=token&display=page';
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
			$('#ACCESS_TOKEN').val(access_token[1]);
		}
	}
}

var vettich_vk_access_token_mod = function(elem)
{
	if(!elem)
		return;

	access_token_id = $('#ACCESS_TOKEN');
	access_token = access_token_id.val().match(/access_token=(\w+)/i);
	if(access_token != null)
	{
		access_token_id.val(access_token[1]);
	}
	else if(access_token_id.val() == '')
	{
		alert(ALERT_NOT_ACCESS_TOKEN);
	}
}

var vch_autoposting_vk_refresh_info = function(content_id, url_params)
{
	if(!url_params)
		url_params = '';
	var url = '/bitrix/admin/vettich_autoposting_vk_method.php';
	var access_token = $('#ACCESS_TOKEN').val();
	if(!access_token)
	{
		$('#'+content_id).html(VCH_USER_INFO_ACCESS_TOKEN_EMPTY);
		return false;
	}

	var method = 'account.getInfo';
	var show = BX.showWait('adm-workarea');
	var full_url = url + '?access_token=' + access_token + '&method=' + method + '&' + url_params;
	$.getJSON(
		full_url,
		function(data)
		{
			BX.closeWait('adm-workarea', show);
			if(!!data.error)
			{
				if(data.error.error_code == 14)
				{
					window.vch_autoposting_captcha_sid = data.error.captcha_sid;
					var out = '<div>' + VCH_USER_INFO_CAPTCHA_GETTED + '</div>'
						+ '<img src="' + data.error.captcha_img + '"> '
						+ '<input id="vch_ap_captcha_code" placeholder="Enter the captcha code" style="display:inline-block"><br>'
						+ '<div class="voptions-add-button" onclick="vch_autoposting_vk_captcha_send(\'' + content_id + '\')" style="width:50%">'
							+ VCH_USER_INFO_CAPTCHA_SEND_BUTTON
						+ '</div>';
					$('#'+content_id).html(out);
				}
				else
				{
					$('#'+content_id).html('<b>Error (code: ' + data.error.error_code + '):</b> ' + data.error.error_msg);
				}
			}
			else
			{
				$('#'+content_id).html(VCH_USER_INFO_CAPTCHA_NOT_NEED);
			}
		}
	);
}

var vch_autoposting_vk_captcha_send = function(content_id)
{
	var captcha_code = $('#vch_ap_captcha_code').val();
	if(!captcha_code)
	{
		alert('Please, enter the captcha code!');
		return false;
	}
	var url_params = 'captcha_sid=' + window.vch_autoposting_captcha_sid
		+ '&captcha_key=' + captcha_code;
	return vch_autoposting_vk_refresh_info(content_id, url_params);
}