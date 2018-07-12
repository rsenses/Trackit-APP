$(document).ready(function() {
    var trigger = $('.hamburger'),
        logo = $('.logo'),
        main = $('main'),
        logoInside = $('.sidebar-brand a img'),
        isClosed = false;

    trigger.click(function() {
        hamburger_cross();
    });

    function hamburger_cross() {
        if (isClosed === true) {
            main.removeClass('is-open');
            logo.removeClass('is-open');
            logoInside.addClass('is-open');
            trigger.removeClass('is-open');
            trigger.addClass('is-closed');
            isClosed = false;
        } else {
            main.addClass('is-open');
            logo.addClass('is-open');
            logoInside.removeClass('is-open');
            trigger.removeClass('is-closed');
            trigger.addClass('is-open');
            isClosed = true;
        }
    }
    $('.dropdown').on('show.bs.dropdown', function() {
        $(this).find('.dropdown-menu').first().stop(true, true).slideDown();
    });

    $('.dropdown').on('hide.bs.dropdown', function() {
      $(this).find('.dropdown-menu').first().stop(true, true).slideUp();
    });

    $('[data-toggle="offcanvas"]').click(function() {
        $('#wrapper').toggleClass('toggled');
    });

    // var ww = $(window).width();
    // var hw = $(window).height();
    // $('body').append('<p style="color:#FFF;position:fixed;bottom:0;right:15px;">H '+hw+'<br />W '+ww+'</p>')
});
