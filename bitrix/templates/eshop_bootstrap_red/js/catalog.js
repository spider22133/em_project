jQuery(document).ready(function($){
//if you change this breakpoint in the style.css file (or _layout.scss if you use SASS), don't forget to update this value as well
	var MqL = 1170;
	var catalogLi = $('.catalog-content li');

	//move nav element position according to window width




    moveSearch();
   $(window).resize(moveSearch);
	moveNavigation();
	$(window).resize(moveNavigation);
	moveSubnav();
	$(window).resize(moveSubnav);

	
	


	//open/close mega-navigation
	$('.catalog-trigger').on('click', function(event){
		event.preventDefault();
		toggleNav();
	});
	
	
	//close meganavigation
	$('.catalog .close-button').on('click', function(event){
		event.preventDefault();
		toggleNav();
	});
	
	
	
	 //on mobile - open submenu
	$('.has-children').children('a').on('click', function(event){
		//prevent default clicking on direct children of .has-children 
		event.preventDefault();
		var selected = $(this);
		selected.next('ul').removeClass('is-hidden').end()
		.parent('.has-children').parent('ul').addClass('move-out');
	}); 

	
	//open submenu
	 if (!Modernizr.touch) {
	catalogLi.hoverIntent({
			over: mouseEnter,
			out: mouseOut,
			timeout: 400
		});
		
		};

	
	function mouseEnter(){$(this).children('a').addClass('is-active').next('ul').addClass('is-active');}
     function mouseOut(){$(this).children('a').removeClass('is-active').next('ul').removeClass('is-active');}
	
	
 /* //open submenu
	$('.has-children').children('a').on('click', function(event){
		if( !checkWindowWidth() ) event.preventDefault();
		var selected = $(this);
		if( selected.next('ul').hasClass('is-hidden') ) {
			//desktop version only
			selected.addClass('is-active').next('ul').removeClass('is-hidden').end().parent('.has-children').parent('ul').addClass('move-out');
			selected.parent('.has-children').siblings('.has-children').children('ul').addClass('is-hidden').end().children('a').removeClass('is-active');
		} else {
			selected.removeClass('is-active').next('ul').addClass('is-hidden').end().parent('.has-children').parent('ul').removeClass('move-out');
		}
	}); */
	
	
	
		

	//submenu items - go back link
	$('.go-back').on('click', function(){
		var selected = $(this),
			visibleNav = $(this).parent('ul').parent('.has-children').parent('ul');
		selected.parent('ul').addClass('is-hidden').parent('.has-children').parent('ul').removeClass('move-out');
	}); 

	function toggleNav(){
		var navIsVisible = ( !$('.catalog').hasClass('dropdown-is-active') ) ? true : false;
		$('.catalog').toggleClass('dropdown-is-active', navIsVisible);
		$('.catalog-trigger').toggleClass('dropdown-is-active', navIsVisible);
		if( !navIsVisible ) {
			$('.catalog').one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend',function(){
				$('.has-children ul').addClass('is-hidden');
				$('.move-out').removeClass('move-out');
				$('.is-active').removeClass('is-active');
			});	
		}
	}
	
	function checkWindowWidth() {
		//check window width (scrollbar included)
		var e = window, 
            a = 'inner';
        if (!('innerWidth' in window )) {
            a = 'client';
            e = document.documentElement || document.body;
        }
        if ( e[ a+'Width' ] >= MqL ) {
			return true;
		} else {
			return false;
		}
	}

	function moveNavigation(){
		var navigation = $('.catalog');
  		var desktop = checkWindowWidth();
        if ( desktop ) {
			navigation.detach();
			navigation.insertAfter('div.catalog-wrapper');
		} else {
			navigation.detach();
			navigation.insertAfter('div.catalog-wrapper');
		}
	}
/*
	function moveSearch(){
		var search = $('.catalog-search');
  		var desktop = checkWindowWidth();
        if ( desktop ) {
			search.detach();
			search.appendTo('div#search');
		} else {
		}
	}*/
	
	function moveSearch(){
		var search = $('.catalog-search');
  		var desktop = checkWindowWidth();
        if ( desktop ) {
        search.detach();
		search.appendTo('#search');
		} else {
		search.detach();
		search.appendTo('.tosearch');
		}
	}
	
	function moveSubnav(){
	    var catalogSecond = $('.catalog-secondary');
		var subnav = catalogSecond.children('div.col-md-8');
  		var desktop = checkWindowWidth();
		var find = $(".xwrap");
        if ( desktop ) {
        // subnav.contents().unwrap();
		catalogSecond.find('.level-2').removeClass('level-2');
		
		find.addClass('level-2');
		} else {
			//subnav.contents().unwrap();
		catalogSecond.find('.level-2').removeClass('level-2');
		}
	}

	window.onload = (function(){
		toggleDrop();
	});

	function toggleDrop()
	{
		var width = $(window).width();
		var navCatalog = $("nav.catalog")
		if (width >= MqL) {
			navCatalog.addClass('dropdown-is-active');
		}
		else {
			navCatalog.removeClass('dropdown-is-active');
		}
	}


});