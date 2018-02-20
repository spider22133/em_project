$(document).ready(function(){
	//$('body').append('<div id="overlaycall"></div>');
	
	$('a.call_btn').click(function(event){
		 $('#overlaycall').height($(document).height()+'px').fadeIn(500);
		 var ob = $('#overlaycall').offset();
		 if(ob.top>0) $('#overlaycall').css("top",'-'+ob.top+'px');
		 if(ob.left>0) $('#overlaycall').css("left",'-'+ob.left+'px');
		 if($(document).width() > $('#overlaycall').width())
			$('#overlaycall').css("width",$(document).width()+'px');
		 
		 $('#form_wrapper_call').show();
		 ajustScrollTop('#form_wrapper_call');
		 event.preventDefault();
	 });
	 
	$('span.wr_close').click(function(event){
		$('#overlaycall').fadeOut(500);
		$(this).parents('#form_wrapper_call').hide();
		event.preventDefault();
	});
	
});

$(function(){
	
	$('#call_ord').on('submit', function(event){
		var canSend = 1;
		
		if($('#v_name').val() == '' && $('#v_name').prop("required")){canSend = 0;}
		if($('#v_phone').val() == '' && $('#v_phone').prop("required")){canSend = 0;}
		if($('#v_time').val() == '' && $('#v_time').prop("required")){canSend = 0;}
		if(typeof $('#v_mess').val() !== 'undefined' && $('#v_mess').val() == '' && $('#v_mess').prop("required")){canSend = 0;}
		
		if(canSend != 0){		
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