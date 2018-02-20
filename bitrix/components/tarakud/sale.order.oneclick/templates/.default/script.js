function oneclickPopup(title, btnOrder, btnClose, urlPost, elementId, messageConfirm)
{
	formPopup = BX.PopupWindowManager.create("popup-form"+elementId, "", {
		offsetTop : -70,
		offsetLeft : -50,
		autoHide : true,
		closeByEsc : true,
		closeIcon : true,
		titleBar : true,
		draggable: {restrict:true},
		titleBar: {content: BX.create("span", {html: title, 'props': {'className': 'sale-popup-title'}})},
		content : BX("oneclick-popup"+elementId)
	});
	formPopup.setButtons([
		new BX.PopupWindowButton({
			text : btnOrder,
			className : "popup-save",
			id : "popup-save"+elementId,
			events : {
				click : function()
				{
					var send = BX("ajax_send"+elementId).value;
					if (send == "N")
					{
						BX("ajax_send"+elementId).value = 'Y';
						BX('popup-save'+elementId).className += ' disable-button';
						var formData = $('#oneclick-form'+elementId).serialize()+'&sessid='+BX.bitrix_sessid();

						if (formData.length > 0)
						{
							BX.showWait(BX('oneclick-form'+elementId));
							$.post(urlPost, formData, function(res)
							{
								BX.closeWait();
								var data = eval( '('+res+')' );

								if (data["STATUS"] == "OK")
								{
									BX('oneclick-popup'+elementId).innerHTML = "<div class='order-confirm'><div>"+messageConfirm+"</div></div>";
									BX("popup-save"+elementId).remove();
								}
								else
								{
									BX("ajax_send"+elementId).value = 'N';
									BX("popup-save"+elementId).classList.remove('disable-button');
									BX('sale-error'+elementId).innerHTML = data["DATA"];
								}
							});
						}
					}
				}
			}
		}),
		new BX.PopupWindowButton({
			text : btnClose,
			className : "popup-close",
			id : "popup-close"+elementId,
			events : {
				click : function()
				{
					formPopup.close();
				}
			}
		})
	]);

	formPopup.show();
}

function reloadCaptcha(elementId)
{
	BX.ajax.getCaptcha(function(data)
	{
		BX('captha-img'+elementId).src = '/bitrix/tools/captcha.php?captcha_sid='+data.captcha_sid;
		BX('captcha_sid'+elementId).value = data.captcha_sid;
		BX('captcha_word'+elementId).value = '';
	});

	return false;
}

function changeQuantity(type)
{
	var val = parseFloat(BX('js-popup-quantity').value);

	if (type == 'top')
		val = val + 1;

	if (type == 'down' && val > 1)
			val = val - 1;

	BX('js-popup-quantity').value = val;

	return false;
}