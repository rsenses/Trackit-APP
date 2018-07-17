$(document).on('click', '.closer', function(event){
    event.preventDefault();
    element = $(this).parent().hide("slow");
});

$(document).ready(function() {
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
