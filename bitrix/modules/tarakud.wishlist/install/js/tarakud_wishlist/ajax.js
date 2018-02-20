$(function () {
	var sendWishlist = "N";
	
	$('.js-wishlist').on('click', function (e) {
		e.preventDefault();
		
		var elementId = parseInt($(this).data('wishid'));
		var iblockId = parseInt($(this).data('wishiblock'));
		var el = $(this);
		
		if (sendWishlist == "N" && elementId > 0 && iblockId > 0)
		{
			sendWishlist = "Y";
			var postData = 'id='+elementId+'&iblock='+iblockId+'&ajax=wishlist&sessid='+BX.bitrix_sessid();

			$.ajax({
				url: wishlistUrl,
				type: 'post',
				data: postData,
				dataType: "json",
				success: function (data) 
				{
					sendWishlist = "N";
					if (data.status == "add")
					{
						$(el).html(BX.message('T_DEL_TEXT'));
						$(el).off('click');
						$(el).attr('href', BX.message('T_PAGE'));
					}
				}
			});
		}
	});
});