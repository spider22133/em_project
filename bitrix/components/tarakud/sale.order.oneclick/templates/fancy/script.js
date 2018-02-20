function reloadCaptcha(elementId)
{
	BX.ajax.getCaptcha(function(data) {
		BX('captha-img'+elementId).src = '/bitrix/tools/captcha.php?captcha_sid='+data.captcha_sid;
		BX('captcha_sid'+elementId).value = data.captcha_sid;
		BX('captcha_word'+elementId).value = '';
	});

	return false;
}

function changeQuantity(type)
{
	var val = parseFloat($('.js-popup-quantity').val());

	if (type == 'top')
		val++;

	if (type == 'down' && val > 1)
		val--;

	$('.js-popup-quantity').val(val);

	return false;
}

$(document).ready(function() {
	$(".js-popup-btn-form").fancybox({
		'padding': 15,
		//'width': 550,
		'scrolling': 'hidden',
		//'height': 300
	});
	
	$(".js-popup-order-confirm").click(function(e) {
		e.preventDefault();

		var id = $(this).data('id');
		var formData = $('#popup-form'+id).serialize()+'&sessid='+BX.bitrix_sessid();
		var urlPost = $('#js-url').val();
		var send = $('#send-ajax'+id).val();

		if (formData.length > 0 && send == "N")
		{
			$('#send-ajax'+id).val('Y');
			$(this).attr('disabled', 'disabled');
			BX.showWait(BX('popup-form'+id));

			$.post(urlPost, formData, function(res)
			{
				BX.closeWait();
				var data = eval( '('+res+')' );

				if (data["STATUS"] == "OK")
					$('#popup-window'+id).html("<div class='popup-order-confirm'><div>"+BX.message('SALE_ORDER')+"</div></div>");
				else
				{
					$('#send-ajax'+id).val('N');
					$('#popup-error'+id).html(data["DATA"]);
					$(".js-popup-order-confirm").removeAttr("disabled");
				}
			});
		}
		else
		{
			$(".js-popup-order-confirm").removeAttr("disabled");
		}

		return false;
	});

	$(".js-element_offers").change(function() {
		$(".js-popup-price-val").html( $(this).find(':selected').data('price') );
	});
});