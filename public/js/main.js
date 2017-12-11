$(document).on('click', '.toggler', function(event) {
    event.preventDefault();
    $(this).toggleClass('active');
    $('.charts-container').toggleClass('invisible');
    /* Act on the event */
});