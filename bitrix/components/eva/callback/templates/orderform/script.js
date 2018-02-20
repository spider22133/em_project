$(document).ready(function(){
	$('body').append('<div id="overlay"></div>');
});

$(document).ready(function(){
	$('a.call_btn').click(function(event){
		 $('#overlay').fadeIn(500);
		 $('#form_wrapper_call').show();
		 //ajustScrollTop('#form_wrapper_call');
		 event.preventDefault();
	 });
	 
	// закрываем блоки с формами
	$('span.wr_close').click(function(event){
		$('#overlay').fadeOut(500);
		$(this).parents('#form_wrapper_call').hide();
		event.preventDefault();
	});
	
	$('#overlay').click(function(event){
		$('#overlay').fadeOut(500);
		$('#form_wrapper_call').hide();
		event.preventDefault();
	});
});

$(function(){
	
	//alert('it works!');
	// поля формы фокус 
	$('#v_name').on('focus', function(){
		if($(this).val() == '' || $(this).val() == 'Введите ФИО' || $(this).val() == 'Поле ФИО не заполнено!') $(this).val('').css({'color':'#b4b4b4', 'border-color':'#b4b4b4'});
	});
	$('#v_phone').on('focus', function(){
		if($(this).val() == '' || $(this).val() == 'Введите телефон' || $(this).val() == 'Поле Телефон не заполнено!') $(this).val('').css({'color':'#b4b4b4', 'border-color':'#b4b4b4'});
	});
	$('#v_time').on('focus', function(){
		if($(this).val() == '' || $(this).val() == 'Удобное время для звонка' || $(this).val() == 'Поле не заполнено!') $(this).val('').css({'color':'#b4b4b4', 'border-color':'#b4b4b4'});
	});
	$('#v_mess').on('focus', function(){
		if($(this).val() == '' || $(this).val() == 'Сообщение' || $(this).val() == 'Поле Сообщение не заполнено!') $(this).val('').css({'color':'#b4b4b4', 'border-color':'#b4b4b4'});
	});
	
	$('#call_ord').on('submit', function(event){
		var canSend = 1;
		
		if($('#v_name').val() == ''){$('#v_name').css({'border':'1px solid #f00', 'color':'#f00'}); canSend = 0;	}
		if($('#v_phone').val() == ''){$('#v_phone').css({'border':'1px solid #f00', 'color':'#f00'}); canSend = 0;}
		//if($('#v_time').val() == 'Удобное время для звонка'){$('#v_time').css({'border':'1px solid #f00', 'color':'#f00'}).val('Поле не заполнено!'); canSend = 0;}
		if($('#v_mess').val() == ''){$('#v_mess').css({'border':'1px solid #f00', 'color':'#f00'}); canSend = 0;}
		
		if(canSend != 0){		
			//подготавливаем и отправляем данные…
			
			var form = $(this);
			var url = form.attr('action');
			
			var type = $(this).attr('id');
			var sessid = $('#sessid').val();
			var name = $('#v_name').val();
			var phone = $('#v_phone').val();
			var time = $('#v_time').val();
			var usemess = $('#use_mess').val();
			var message = $('#v_mess').val();
			var captcha_sid = $('#captcha_sid').val();
			var captcha_word = $('#captcha_word').val();
			var PARAMS_HASH = $('#PARAMS_HASH').val();
			
			// если поле Сообщение активно
			if(typeof message !== 'undefined'){
				$.post(url, 
				  {
					  form_type: type,
					  sessid: sessid,					  
					  v_name: name,
					  v_phone: phone,
					  v_time: time,
					  v_mess: message,
					  captcha_sid: captcha_sid,
					  captcha_word: captcha_word,
					  PARAMS_HASH: PARAMS_HASH
				  }, 
					 function(data){
						form.parent('.frm_place').empty().html(data);
					}													
				);
			}
			else{
				$.post(url, 
				  {
					  form_type: type,
					  sessid: sessid,					  
					  v_name: name,
					  v_phone: phone,
					  v_time: time,
					  captcha_sid: captcha_sid,
					  captcha_word: captcha_word,
					  PARAMS_HASH: PARAMS_HASH
				  }, 
					 function(data){
						form.parent('.frm_place').empty().html(data);
					}													
				);
			}				
			event.preventDefault();
		}
		else{
			return false;
		}
	
	});
});