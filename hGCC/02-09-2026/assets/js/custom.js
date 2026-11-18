jQuery(document).ready(function ($) {

    $(window).on('scroll', function () {

        if ($(window).scrollTop() > 0) {

            $('.header-menu').addClass('fixed');
            $('body').addClass('fixed');

        } else {

            $('.header-menu').removeClass('fixed');
            $('body').removeClass('fixed');

        }

    });

});