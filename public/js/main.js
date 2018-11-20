$(document).on('click', '.closer', function(event) {
    event.preventDefault();
    element = $(this).parent().hide("slow");
});

// $(document).on('click', '.help', function(event) {
//     $(this).toggleClass('disable');
//     if($(this).hasClass('disable')) {
//         console.log('none');
        
//         $('.tooltipped').tooltip();
//         $(this).html('DESCACTIVAR AYUDA <i class="mdi mdi-tooltip"></i>');
//     } else {
//         console.log('yes');
        
//         $('.tooltipped').tooltip('destroy');
//         $(this).html('ACTIVAR AYUDA <i class="mdi mdi-tooltip-outline"></i>');
//     }
// });

$(document).ready(function() {
    var ww = window.screen.width;
    // var wh = window.screen.height;
    // $('body').append('<div style="color:#FFF;position:fixed;bottom:10px;right:10px;">' + ww + 'x' + wh + '</div>')
    $('select').formSelect();

    if ($('#email').length) {
        $('#email').focus();
    }
    if (ww <= 360 && ($('form.login').length)) {
        var action = $('form.login').attr('action');
        $('form.login').attr('action', action + '?mobile')
    }
    if ($('.sidenav').length) {
        $('.sidenav').sidenav();
    }
    if ($('#modalmsg').length) {
        $('#modalmsg').modal();
    }
    if ($('#modalcharts').length) {
        $('#modalcharts').modal();
    }
});