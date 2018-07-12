$('.closer').click(function(event){
    event.preventDefault();
    element = $(this).parent().hide("slow");
});

$(window).load(function() {
    $('.loader-box').removeClass('active').delay(1200).queue(function(next) {
        $(this).addClass('hide');
        next();
    });
});

$(document).ready(function() {
    $('.sidenav').sidenav();
});