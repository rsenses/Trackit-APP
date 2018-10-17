$(document).on('click', '.closer', function(event) {
    event.preventDefault();
    element = $(this).parent().hide("slow");
});


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