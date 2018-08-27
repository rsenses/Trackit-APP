$(document).on('click', '.closer', function(event){
    event.preventDefault();
    element = $(this).parent().hide("slow");
});


$(document).ready(function() {
    var ww = window.screen.width;

    $('select').formSelect();

    if (ww <= 320 && ($('form.login').length)) {
        var action = $('form.login').attr('action');
        $('form.login').attr('action', action+'?mobile')
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
