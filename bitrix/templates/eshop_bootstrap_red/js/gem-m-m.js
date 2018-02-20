(function ($) {

    $(document).ready(function () {
        var menu_ul = $('.menu > li > ul'),
            menu_a = $('.menu > li > a');
        menu_ul.hide();
        menu_a.click(function (e) {
            e.preventDefault();
            if (!$(this).hasClass('active')) {
                menu_a.removeClass('active');
                menu_ul.filter(':visible').slideUp('normal');
                $(this).addClass('active').next().stop(true, true).slideDown('normal');
            } else {
                $(this).removeClass('active');
                $(this).next().stop(true, true).slideUp('normal');
            }
        });

        $("#ORDER_PROP_8").attr({"maxlength": 3, "placeholder": "000"});

        $(document).on('click', '#ID_DELIVERY_ID_7', function (event) {
            setTimeout(function () {
                var input = $("#ORDER_PROP_8");
                input.attr("maxlength", 3);
                input.attr("placeholder", "000");

            }, 3000);


        });

        $(function () {
            $('.board-inner a[title]').tooltip();
        });


        $(".btn-wish").on("click", function () {
            $(this).addClass("active");
        });

        // cart free delivery calculation
        $('.basket_quantity_control').on('click', function () {
            deliveryFree();
        });
        $('input[id^="QUANTITY_INPUT"]').on('change', function () {
            deliveryFree();
        });
    });

    function deliveryFree() {

        setTimeout(function () {
            var val = $('#allSum_wVAT_FORMATED').html();
            var val_int = parseInt(val.replace('грн.', ''));
            var to_delivery = $('.gem_message.free_delivery');
            var free_delivery = $('.gem_message.delivery_success');
            var to_text = $('<div class="gem_message free_delivery"><p>До бесплатной доставки необходимо еще <strong></strong></p></div>');
            var free_text = $('<div class="gem_message delivery_success"><p>Мы доставим Ваш заказ бесплатно!</p></div>');
            var cont = $('.bx_ordercart');


            if (val_int < 500) {
                free_delivery.remove();
                console.log(to_delivery.length);
                if (to_delivery.length === 0) {
                    cont.prepend(to_text);
                }

                var remain = 500 - val_int;

                $('.gem_message.free_delivery strong').html(remain + ' грн.');

                // var text = $('<div class="gem_message free_delivery"><p>До бесплатной доставки необходимо еще <strong>' + remain + ' грн.</strong></p></div>');
                // $('.bx_ordercart').prepend(text);

            } else if (500 <= val_int) {

                to_delivery.remove();

                if (free_delivery.length === 0) {
                    cont.prepend(free_text);
                }

            }
        }, 700)

    }
})(jQuery);