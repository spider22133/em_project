/*global $ */
$(document).ready(function () {


    "use strict";

    // var anchor = document.querySelectorAll('button');
    //
    // [].forEach.call(anchor, function(anchor){
    //     var open = false;
    //     anchor.onclick = function(event){
    //         event.preventDefault();
    //         if(!open){
    //             this.classList.add('close');
    //             open = true;
    //         }
    //         else{
    //             this.classList.remove('close');
    //             open = false;
    //         }
    //     }
    // });


    $('.catalog > ul > li:has( > ul)').addClass('catalog-dropdown-icon');
    //Checks if li has sub (ul) and adds class for toggle icon - just an UI


    // $('.catalog > ul > li > ul:not(:has(ul))').addClass('normal-sub');
    //Checks if drodown menu's li elements have anothere level (ul), if not the dropdown is shown as regular dropdown, not a mega menu (thanks Luka Kladaric)

    $(".catalog > ul").before("<a href=\"#\" class=\"catalog-mobile\">КАТАЛОГ ТОВАРОВ</a>");

    //Adds menu-mobile class (for mobile toggle menu) before the normal menu
    //Mobile menu is hidden if width is more then 959px, but normal menu is displayed
    //Normal menu is hidden if width is below 959px, and jquery adds mobile menu
    //Done this way so it can be used with wordpress without any trouble

    $(".catalog > ul > li").hoverIntent(function (e) {
        if ($(window).width() > 943) {
            $(this).children("ul").stop(true, false).fadeToggle(150);
            e.preventDefault();
        }
    });
    //If width is more than 943px dropdowns are displayed on hover

    $(".catalog > ul > li").click(function () {
        if ($(window).width() <= 943) {
            $(this).children("ul").fadeToggle(400);
            $(this).toggleClass('is-active');
        }

    });
    //If width is less or equal to 943px dropdowns are displayed on click (thanks Aman Jain from stackoverflow)

    $(".catalog-mobile").click(function (e) {
        $(".catalog > ul").toggleClass('show-on-mobile');
        e.preventDefault();
    });
    //when clicked on mobile-menu, normal menu is shown as a list, classic rwd menu story (thanks mwl from stackoverflow)

});