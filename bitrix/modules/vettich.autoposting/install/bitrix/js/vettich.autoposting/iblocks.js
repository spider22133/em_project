function vettich_iblock_menu_send(qstr)
{
	width = window.innerWidth - (window.innerWidth/3);
	height = window.innerHeight - (window.innerHeight/5);
	url = '/bitrix/admin/vettich_autoposting_popup_iblocks.php?' + qstr;
	window.open(
		url, 
		'VPostingIBlocks', 
		'location=yes,resizable=yes,scrollbars=yes,width=' + width + ',height=' + height + ',left=' + (window.innerWidth/2 - width/2) + ',top=' + (window.innerHeight/2 - height/2)
	);
}

function vch_post_iblock_change(value)
{
	if(value == 'VCH_POST_IBLOCK_MENU_SEND')
		$('#VCH_POST_IBLOCK_SELECT').show();
	else
		$('#VCH_POST_IBLOCK_SELECT').hide();
}

function vch_post_go(link, isnt_push_history)
{
	if(!link || !VCH_POSTS_AJAX_ENABLE
		|| link.indexOf('javascript:') >= 0)
		return true;

	var lid = '';
	var ahref = '/bitrix/admin/vettich_autoposting_posts';
	var prefix = 'vettich_autoposting_posts_edit';
	var prefix_type = 1;
	var p = -1;
	if((p = link.indexOf(prefix)) < 0)
	{
		if((p = link.indexOf(prefix = 'vettich_autoposting_posts')) < 0)
		{
			if(link.indexOf(prefix = 'vettich_autoposting_logs') < 0)
				prefix = '';
			else
				ahref = '/bitrix/admin/vettich_autoposting_logs';
		}
		else
		{
			prefix_type = 2;
		}
	}
	if(!!prefix && link.indexOf(prefix + '_') >= 0)
	{
		p += (prefix+'_').length;
		lid = link.substr(p, link.indexOf('.', p) - p);
	}

	var err = false;
	if(!!prefix)
	{
		var show = BX.showWait('adm-workarea');
		if(link.indexOf('?') > 0)
			_link = link + '&ajax=Y';
		else
			_link = link + '?ajax=Y';
		if(!_vettich_js_files) {
			_vettich_js_files = [];
			$('head script').each(function(){
				var $this = $(this);
				var attr = $this.attr('src');
				if(attr && attr.match(/vettich/)) {
					_vettich_js_files.push(attr);
				}
			});
		}
		$.get(_link+'&ajax=Y', function(data){
			BX.closeWait('adm-workarea', show);
			window._dom = (new DOMParser()).parseFromString(data, 'text/html');

			if(!_dom || !_dom.getElementById("adm-workarea"))
				return vch_post_go_err(link);

			var head = $('head');
			$(_dom).find('head script').each(function(){
				var $this = $(this);
				var attr = $this.attr('src');
				if(attr && attr.match(/vettich/) && !$.inArray(attr, _vettich_js_files)) {
					_vettich_js_files.push(attr);
					head.append($this.get(0).outerHTML);
				}
			})

			$('title').text(_dom.getElementsByTagName("title")[0].innerHTML);
			$('#adm-workarea').html(_dom.getElementById("adm-workarea").innerHTML);
			delete _dom;

			if(!isnt_push_history)
			{
				if(window.history && history.pushState)
				{
					history.pushState   ("", "", link);
					history.replaceState("", "", link);
				}
				else
					location.hash = link;
			}

			$('.adm-submenu-item-active').removeClass('adm-submenu-item-active');
			if(!!lid)
				ahref += '_' + lid;
			ahref += '.php';
			$('a.adm-submenu-item-name-link[href="' + ahref + '"]')
				.parentsUntil('.adm-sub-submenu-block')
				.parent()
				.addClass('adm-submenu-item-active');

			$('a').unbind('click', vch_aclick);
			$('a').click(vch_aclick);
			// BX.closeWait('adm-workarea', show);
		})
		return false;
	}
	else
		return true;
}

function vch_post_go_link(link)
{
	if(vch_post_go(link))
		window.location = link;
}

function vch_post_go_err(link)
{
	window.location = link;
	return false;
}

function vch_aclick(e){
	link = e.currentTarget.referrer;
	if(!link)
		link = e.currentTarget.href;
	return vch_post_go(link);
}

function vch_history_back()
{
	vch_post_go(window.location.href, true);
}

$(document).ready(function(){
	if(typeof VCH_POSTS_AJAX_ENABLE === "undefined")
		VCH_POSTS_AJAX_ENABLE = false;
	if(VCH_POSTS_AJAX_ENABLE)
	{
		$('a').click(vch_aclick);
		$(window).on('popstate', vch_history_back);
	}
})
