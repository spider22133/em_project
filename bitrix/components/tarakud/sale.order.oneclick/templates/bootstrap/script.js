$(function () {
	$('.js-popup-control button').on('click', function(e) {
		e.preventDefault();
		
		var type = $(this).data('type');
		var val = parseFloat($('.js-popup-quantity').val());
		
		if (type == 'up')
			val++;

		if (type == 'down' && val > 1)
			val--;

		$('.js-popup-quantity').val(val);
	});
	
	$('.js-popup-recaptcha').on('click', function(e) {
		e.preventDefault();
		
		var elementId = $(this).data('id');
		BX.ajax.getCaptcha(function(data) {
			BX('captha-img'+elementId).src = '/bitrix/tools/captcha.php?captcha_sid='+data.captcha_sid;
			BX('captcha_sid'+elementId).value = data.captcha_sid;
			BX('captcha_word'+elementId).value = '';
		});
	});
	
	$('.js-offer').on('change', function(e) {
		e.preventDefault();
		
		var id = $(this).val();
		if ($('.js-price').length)
			$('.js-price').html(arPriceOffer[id]);
	});
	
	$('.js-offer-all').on('click', function(e) {	
		var id = $('.js-offer-all input[type=radio]:checked').val();
		
		if ($('.js-price').length)
			$('.js-price').html(arPriceOffer[id]);
	});
	
	var ajaxSend = "N";
	$(".js-popup-order").click(function(e) {
		e.preventDefault();
		
		var el = $(this);
		var id = el.data('id');
		var err = $('.js-popup-error'+id);
		var formData = $('#popup-form'+id).serialize()+'&sessid='+BX.bitrix_sessid();
		var urlPost = "/bitrix/components/tarakud/sale.order.oneclick/ajax.php";

		if (formData.length > 0 && ajaxSend === "N")
		{
			ajaxSend = "Y";
			el.attr('disabled', 'disabled');
			BX.showWait(BX('popup-form'+id));

			$.post(urlPost, formData, function(res)
			{
				BX.closeWait();
				var data = eval( '('+res+')' );

				if (data["STATUS"] == "OK")
				{
					err.html('');
					err.hide();
					$('.js-modal-body'+id).html("<div class='popup-order-confirm'><div>"+BX.message('SALE_ORDER')+"</div></div>");
					$('.js-modal-footer').remove();
				}
				else
				{
					ajaxSend = "N";
					err.html(data["DATA"]);
					err.show();
					el.removeAttr("disabled");
				}
			});
		}
		else
		{
			el.removeAttr("disabled");
		}
	});
	
});