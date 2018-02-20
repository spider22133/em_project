
function showOverlay() {  // затемнение
	var over = $('<div id="overlaycall"></div>');
	over.appendTo('body');
	over.height($(document).height()+'px').width($(document).width()+'px').fadeIn(500);	    	     
}
	
function ajustScrollTop(obj) { //позиции при скролле страницы
	var clHght = document.documentElement.clientHeight; // определяем высоту видимой части страницы
	var clformh = $(obj).height(); // определяем высоту изображения
	var posY = (clHght - clformh)/2+$(window).scrollTop(); // вычислям позицию верхнего левого угла блока с изображением
	$(obj).css('top',posY).css('margin-top','0'); // позиционируем блок
}