/*
* проверяет переменную на существование
* string _var - переменная
*/
var is = function(_var)
{
	var ret = false;
	eval("ret = typeof "+_var+" != 'undefined'");
	return ret;
}

/*
array|object arr - массив параметров
return array - список ключей в arr в отсортированном порядке
*/
var VOptionsSortParams = function(arr)
{
	if(!arr)
		return;

	var result = [];
	$.each(arr, function(i, param){
		if(!arr[i].SORT)
			arr[i].SORT = 500;
		result.push(i);
	});

	for(i=0; i<result.length-1; i++)
	{
		min = i;
		for(j=i+1; j<result.length; j++)
		{
			if(arr[result[j]].SORT < arr[result[min]].SORT)
			{
				min = j;
			}
		}
		if(i != min)
		{
			temp = result[i];
			result[i] = result[min];
			result[min] = temp;
		}
	}

	return result;
}

var VOptionsAddGroupParamDislay = false;

var VOptionsAddGroupParams = function(id)
{
	if(!id)
		return;

	VOptionsAddGroupParamDislay = false;
	var id_orig = id;
	var params = false;
	var name = '';
	if(id instanceof Array || id instanceof Object)
	{
		var tmp_id = '';
		params = VOptionsParams;
		$.each(id, function(i, val){
			if((val instanceof Array) || (val instanceof Object))
			{
				tmp_id += val[0] + '-' + val[1] + '-';
				params = params[val[0]]['VALUES'];
				if(name == '')
				{
					name = val[0] + '[' + val[1] + ']';
				}
				else
				{
					name += '[' + val[0] + '][' + val[1] + ']';
				}
			}
			else
			{
				tmp_id += val;
				params = params[val]['VALUES'];
				if(name == '')
				{
					name = val;
				}
				else
				{
					name += '[' + val + ']';
				}
			}
		});
		id = tmp_id;
	}
	else
	{
		params = VOptionsParams['"' + id + '"']['VALUES'];
	}

	var group = $('#' + id);
	if(group == null)
		return;

	var group_cnt = group.val();
	var container = $('#' + id + '_container');

	var tmp = id_orig.pop();
	if(tmp instanceof Array || tmp instanceof Object)
		tmp = tmp[0];
	id_orig.push([tmp, group_cnt]);

	var html = '<div class="voptions-group-div" rel="' + group_cnt + '">';
	html += '<table class="voptions-group-table">';

	params_sort = VOptionsSortParams(params);
	$.each(params_sort, function(i, group_name){
		param = params[group_name];
		_name = name + '[' + group_cnt + '][' + group_name + ']'
		_id = id + '-' + group_cnt + '-' + group_name;
		html += VOptionsAddGroupParam(param, _name, _id, id_orig, group_name);
	});


	html += '</table>';
	html += '</div>';

	container.append(html);
	group.val(++group_cnt);
}

var VOptionsAddGroupParam = function(param, name, id, group_arr, group_name)
{
	if(!param)
		return '';

	param.TYPE = param.TYPE.toUpperCase();
	var value = param.DEFAULT ? param.DEFAULT : '';
	var refresh = (param.REFRESH && param.REFRESH == 'Y');
	var required = (param.REQUIRED && param.REQUIRED == 'Y');
	var multiple = (param.MULTIPLE && param.MULTIPLE == 'Y');
	var ext_params = '';

	if(param.BIND && param.BIND_VALUES)
	{
		var BIND = param.BIND;
		if(param.BIND instanceof Array || param.BIND instanceof Object)
		{
			BIND = '';
			$.each(param.BIND, function(i, v)
			{
				if(group_arr[i] && group_arr[i][0])
				{
					BIND += v + VOptionsGroupSeparator + group_arr[i][1] + VOptionsGroupSeparator;
				}
				else
				{
					BIND += v;
					return false;
				}
			});
			// param.BIND = bind;
		}

		ext_params += ' bind="' + BIND + '"';
		ext_params += ' data-bind-values=\'' + JSON.stringify(param.BIND_VALUES) + '\'';
	}

	var html = '';

	if(param.TYPE == 'GROUP')
	{
		html = '<tr><td colspan="2" >';
		html += '<input type="group" name="' + name + '[count]" id="' + id + '" value="1">';

		html += '<div class="voptions-group-title">';
		html += param.NAME;
		html += '</div>';

		if(param.DESCRIPTION)
		{
			html += '<div class="voptions-group-description">';
			html += param.DESCRIPTION;
			html += '</div>';
		}

		html += '<div id="' + id + '_container" class="voptions-group-container">';
		html += '<div class="voptions-group-div">';
		html += '<table class="voptions-group-table">';
		var last_key = -1;
		$.each(param.VALUES, function(key, v){
			var _name = name + '[0][' + key + ']';
			var _id = id + '-0-' + key;
			var _group_arr = group_arr.concat([[group_name, 0]]);
			last = key;

			html += VOptionsAddGroupParam(param.VALUES[key], _name, _id, _group_arr, key);
		});
		if(last_key >= 0 && param.VALUES[last_key].DISPLAY == 'inline')
			html += '</tr></table></td></tr>';
		html += '</table>';
		html += '</div>';
		html += '</div>';

		group_arr.push(group_name);
		_group_arr = JSON.stringify(group_arr);
		html += '<div class="voptions-add-button" onclick=\'VOptionsAddGroupParams('+ _group_arr +');\'>';
		html += (param.ADDBUTTON) ? param.ADDBUTTON : VOptionsGroupAddButtonText;
		html += '</div>';
		html += '</td></tr>';
	}
	else if(param.TYPE == 'NOTE')
	{
		html += '<tr><td colspan="2" align="center" class="VOptionNote">';
		html += param.NAME;
		html += '<br/><br/>';
		html += value;
		html += '</td></tr>';
	}
	else if(param.TYPE == 'HIDDEN')
	{
		html += '<tr><td>';
		html += '<input type="hidden" name="' + name + '" id="' + id + '" value="' + value + '">';
		html += '</td></tr>';
	}
	else
	{
		if(param.DISPLAY != VOptionsAddGroupParamDislay)
		{
			if(VOptionsAddGroupParamDislay == 'inline')
				html += '</tr></table></td></tr>';
			if(param.DISPLAY == 'block')
				html += '<tr>';
			else if(param.DISPLAY == 'inline')
				html += '<tr><td colspan="2"><table><tr>';
		}

		// html = '<tr>';
		if(param.DISPLAY != 'inline' || is('param.NAME'))
		{
			html += '<td width="20%" class="adm-detail-content-cell-l adm-detail-valign-top">';
			html += '<label for="">' + param.NAME + '</label>';
			if(is('param.HELP'))
			{
				html += ' <div class="voptions-help">';
				html += '<div class="voptions-help-btn">';
				html += '</div>';
				html += '<div class="voptions-help-text">' + param.HELP + '</div>';
				html += '</div>';
			}
			html += '</td>';
		}
		html += '<td class="adm-detail-content-cell-r">';
		switch(param.TYPE)
		{
			case 'STRING':
			case 'TEXT':
				var maxlength = param.MAXLENGTH ? param.MAXLENGTH : '255';
				var size = param.SIZE ? param.SIZE : '80';
				html += '<input type="text" size="' + size + '" maxlength="' + maxlength + '" value="' + value + '" name="' + name + '" id="' + id +'"' + ext_params + '>'
				if(refresh)
					html += '<button onclick="VOptionRefresh();">OK</button>';
				break;

			case 'TEXTAREA':
				var cols = param.COLS ? param.COLS : '80';
				var rows = param.ROWS ? param.ROWS : '10';
				html += '<textarea rows="' + rows + '" cols="' + cols + '" name="' + name + '" id="' + id + '"' + ext_params + '>' + value + '</textarea>';
				if(refresh)
					html += '<button onclick="VOptionRefresh();">OK</button>';
				if(is('param.CHOISE') && is('param.VALUES'))
				{
					html += '<input type="checkbox" class="voptions-textarea-choise-checkbox" id="' + id + '_choise_checkbox">';
					html += '<label class="voptions-textarea-choise-checkbox-label" for="' + id + '_choise_checkbox"> ' + TEXTAREA_SHOW_CHOISE + ' </label>';
					html += '<div id="' + id + '_choise" class="voptions-textarea-choise">';
					switch(param.CHOISE.toUpperCase())
					{
						case 'SIMPLE':
							$.each(param.VALUES, function(i, val){
								html += '<div class="voptions-textarea-choise-simple">';
								html += '<a href="javascript:undefined" onclick="voptions_textarea_choise(\''+ id +'\', \'#'+ i +'#\')">';
								html += val;
								html += '</a>';
								html += '</div>';
							});
					}
					html += '</div>';
				}
				break;

			case 'PASSWORD':
				var maxlength = param.MAXLENGTH ? param.MAXLENGTH : '255';
				var size = param.SIZE ? param.SIZE : '80';
				html += '<input type="password" size="' + size + '" maxlength="' + maxlength + '" value="' + value + '" name="' + name + '" id="' + id +'"' + ext_params + '>'
				if(refresh)
					html += '<button onclick="VOptionRefresh();">OK</button>';
				break;

			case 'EMAIL':
				var maxlength = param.MAXLENGTH ? param.MAXLENGTH : '255';
				var size = param.SIZE ? param.SIZE : '80';
				html += '<input type="email" size="' + size + '" maxlength="' + maxlength + '" value="' + value + '" name="' + name + '" id="' + id +'"' + ext_params + '>'
				if(refresh)
					html += '<button onclick="VOptionRefresh();">OK</button>';
				break;

			case 'COLOR':
			case 'COLORPICKER':
				var size = param.SIZE ? param.SIZE : '80';
				html += '<input type="COLOR" size="' + size + '" value="' + value + '" name="' + name + '" id="' + id +'"' + ext_params + '>'
				if(refresh)
					html += '<button onclick="VOptionRefresh();">OK</button>';
				break;

			case 'NUMBER':
				var maxlength = param.MAXLENGTH ? param.MAXLENGTH : '255';
				var size = param.SIZE ? param.SIZE : '80';
				var min = param.MIN ? param.MIN : '0';
				var max = param.MAX ? param.MAX : '';
				var step = param.STEP ? param.STEP : '';
				html += '<input type="number" size="' + size + '" maxlength="' + maxlength + '" min="' + min + '" max="' + max + '" step="' + step + '" value="' + value + '" name="' + name + '" id="' + id +'"' + ext_params + '>'
				if(refresh)
					html += '<button onclick="VOptionRefresh();">OK</button>';
				break;

			case 'DATE':
				var maxlength = param.MAXLENGTH ? param.MAXLENGTH : '255';
				var size = param.SIZE ? param.SIZE : '80';
				var min = param.MIN ? param.MIN : '0';
				var max = param.MAX ? param.MAX : '';
				html += '<input type="date" size="' + size + '" maxlength="' + maxlength + '" min="' + min + '" max="' + max + '" value="' + value + '" name="' + name + '" id="' + id +'"' + ext_params + '>'
				if(refresh)
					html += '<button onclick="VOptionRefresh();">OK</button>';
				break;

			case 'CHECKBOX':
				var _refresh = refresh ? 'onchange="VOptionRefresh();"' : '';
				var _checked = (value == 'Y');
				html += '<input type="checkbox" value="Y" name="' + name + '" id="' + id +'" ' + _checked + ' ' + _refresh + ext_params + '>'
				break;

			case 'SELECT':
			case 'LIST':
				var _refresh = refresh ? 'onchange="VOptionRefresh();"' : '';
				var _multiple = multiple ? 'multiple="multiple"' : '';
				var size = is('param.SIZE') ? param.SIZE : (multiple ? ((param.VALUES.length >= 10)? '10' : ''+param.VALUES.length) : '1');
				html += '<select ' + _multiple + ' size="' + size + '" name="' + name + '" id="' + id + '" ' + _refresh + ext_params + '>'
				$.each(param.VALUES, function(key, v){
					html += '<option value="' + key + '">' + v + '</option>';
				});
				html += '</select>';
				break;

			case 'RADIO':
				var _refresh = refresh ? 'onchange="VOptionRefresh();"' : '';
				$.each(param.VALUES, function(key, v){
					_checked = (key == value) ? ' checked="checked"' : '';
					html += '<div>';
					html += '<input type="radio" name="' + name + '" id="' + id + '' + key + '" value="' + key + '" ' + _checked + '' + _refresh + '' + ext_params + '>';
					html += '<label for="' + id + '' + key + '">' + v + '</label>';
					html += '</div>';
				});
				break;

			case 'CUSTOM':
				html += param.HTML;
				break;

			case 'NOTE':
				break;
		}
		if(BIND)
		{
			html += '<sc' + 'ript type="text/javascript">' + 
							'VOptionsBind("' + BIND + '", "' + id + '");' +
						'</sc' + 'ript>';
		}
		if(param.DESCRIPTION && param.DESCRIPTION != '')
		{
			html += '<div class="voptions-description">';
			html += param.DESCRIPTION;
			html += '</div>';
		}
		html += '</td>';
		if(param.DISPLAY == 'block')
			html += '</tr>';
	}
	VOptionsAddGroupParamDislay = param.DISPLAY;

	return html;
}

function VOptionRefresh()
{
	var _link = window.location.href;
	var show = BX.showWait('adm-workarea');
	$('body').append('<div id="voptions_overlay" class="voptions-overlay"></div>');
	if(_link.indexOf('?') > 0)
		_link += '&voptions_ajax=Y';
	else
		_link += '?voptions_ajax=Y';
	$('#VOPTIONS_SUBMIT').val('');
	var _data = jQuery("#VOptionsForm").serialize();
	if(_data.indexOf('_active_tab') < 0)
		_data += '&' + tabControlName + '_active_tab=' + $('#' + tabControlName + '_active_tab').val();
	jQuery.ajax({
		url: _link,
		type: "POST",
		data: _data,
		timeout: 10000,
		success: function(data){
			jdata = $(data);
			sdata = data;
			BX.closeWait('adm-workarea', show);
			$('#voptions_overlay').remove();
			// parser = new DOMParser();
			// _dom = parser.parseFromString(data, 'text/html');

			// if(!_dom || !_dom.getElementById("tabControl_layout"))
			// 	return;

			$('#'+ tabControlName + '_layout').html(data);
			eval(tabControlName + '.PreInit()');
			// $('#tabControl_layout').html(_dom.getElementById("tabControl_layout").outerHTML);
		},
		error: function(response, status) {
			BX.closeWait('adm-workarea', show);
			$('#voptions_overlay').remove();
			new BX.CDialog({
				'title':'Error',
				'content':'<center>Error: ' + status + '</center>',
				'width':400,
				'height':150,
				'buttons':[
					BX.CDialog.prototype.btnClose,
				]
			}).Show();
		}
	});
}

/*
string bind_to - опция, на значения которой биндимся
string param - опция, зависящая от опции bind_to
*/
var VOptionsBind = function(bind_to, param)
{
	var item = $('#' + bind_to); 
	if(item.length == 0)
		return;

	item.on('change', function(){
		var elem = $('#' + param);
		if(elem.length == 0)
			return;

		var elem_tag = elem[0].tagName;

		var val = $(this).val();
		var values = [];
		if(elem.attr('data-bind-values'))
		{
			eval('values=' + elem.attr('data-bind-values'));
		}
		var is_found = false;
		$.each(values, function(index, value){
			if(index == val)
			{
				is_found = true;
				switch(elem_tag)
				{
					case 'INPUT':
						elem.val(value);
						break;

					case 'TEXTAREA':
						var choise = $('#' + param + '_choise');
						choise.empty();
						if((value instanceof Object) || (value instanceof Array))
						{
							$.each(value, function(opt_value, opt_text){
								choise.append($('<div class="voptions-textarea-choise-simple">'));
								choise.append($('<a href="javascript:undefined" onclick="voptions_textarea_choise(\''+ param + '\', \'#' + opt_value + '#\')">' + opt_text + '</a>'));
								choise.append($('</div>'));
							});
						}
						else
						{
							choise.append($('<div class="voptions-textarea-choise-simple">'));
							choise.append($('<a href="javascript:undefined" onclick="voptions_textarea_choise(\''+ param + '\', \'#' + value + '#\')">' + value + '</a>'));
							choise.append($('</div>'));
						}
						break;

					case 'SELECT':
						list_selected = elem.find(':selected');
						if(list_selected.length)
						{
							elem.attr('prev_select', '');
							temp = [];
							$.each(list_selected, function(_i, _v){
								temp.push($(_v).val());
							});
							elem.attr('prev_select', temp.join(','));
						}
						elem.empty();
						list_selected = elem.attr('prev_select');
						if(list_selected)
							list_selected = list_selected.split(',');
						else
							list_selected = [];
						if((value instanceof Object) || (value instanceof Array))
						{
							cnt = 0;
							$.each(value, function(opt_value, opt_text){
								_selected = list_selected.indexOf(opt_value) == -1 ? '' : ' selected';
								elem.append($('<option value="' + opt_value + '"' + _selected + '>' + opt_text + '</option>'));
								cnt++;
							});
							if(elem.attr('size') > 0)
							{
								if(cnt > 10)
								{
									cnt = 10;
								}
								elem.attr('size', cnt ? cnt : 1);
							}
						}
						else
						{
							_selected = list_selected.indexOf(value) == -1 ? '' : ' selected';
							elem.append($('<option value="' + index + '"' + _selected + '>' + value + '</option>'));
							if(elem.attr('size') > 0)
							{
								elem.attr('size', 1);
							}
						}
						break;
				}
				elem.trigger('change');
				return false;
			}
		});
		if(!is_found)
		{
			if(values['none'])
			{
				switch(elem_tag)
				{
					case 'SELECT':
					case 'LIST':
						elem.empty();
						elem.append($('<option value="none">' + values['none'] + '</option>'));
						break;
				}
			}
		}
	});
}

/**
* вставляет в textarea с идентификатором elem_id строку str_paste на место с курсором
*
* string elem_id - идентификатор textarea
* string str_paste - строка которую нужно вставить
*/
var voptions_textarea_choise = function(elem_id, str_paste)
{
	var elem = document.getElementById(elem_id);
	if(!elem)
		return;

	var istart = elem.selectionStart;
	var iend = elem.selectionEnd;
	var itxt = elem.value;
	elem.value = itxt.substr(0, istart) + str_paste + itxt.substr(iend);
	elem.focus();
}
