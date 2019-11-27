var info = function() {
    $.ajax({
        type: 'GET',
        url: '/product/info/{{ product_id }}',
        dataType: 'json',
        cache: false,
        context: this,
        success: function(a) {
            $('#modalcharts .modal-content .row').empty();
            for (index in a['registrations']) {
                var val = $.map(a['registrations'][index], function(value) {
                    return value;
                });
                var total = val[0];
                var verified = val[1];
                var percentage = Math.floor((verified / total) * 100);
                isFinite(percentage) ? 0 : percentage;
                var valueBar = percentage >= 60 ? 'light' : '';
                $('#modalcharts .modal-content .row').append('<div class="col s6 mb-10"><p class="name-bar m-0 blue-grey-text text-darken-4"><span style="text-tansform: capitalize;">' + index + 's </span> ' + verified + ' de ' + total + '</p><div class="progress ' + valueBar + '"><span style="z-index:999; position:absolute;">' + percentage + '%</span> <div class="determinate" style="width: ' + percentage + '%"></div></div></div>');
            }
        },
        error: function(a) {
            console.log(a);
        },
        complete: function() {
            // Previous run complete, schedule the next run
            setTimeout(info, 60000);
        }
    });
};
function callModal(style, title, message) {
    $('#modalmsg').modal('open');
    $('#modalmsg, #modalmsg .modal-footer').removeClass('green orange red');
    $('#modalmsg, #modalmsg .modal-footer').addClass(style);
    $('#modalmsg .modal-content').html('<h4>' + title + '</h4>');
    $('#modalmsg .modal-content').html(message);
}
function cleanner() {
    $('#modalmsg').modal('close');
    $('input.search').val('');
    $('.btn-clear').addClass('hide');
    setTimeout(function() {
        $('.nolist').removeClass('hide');
        $('footer .modal-trigger').removeClass('hide');
        setTimeout(function() {
            $('#list').addClass('hide');
            $('input.search').focus();
        }, 1000);
    }, 1000);
}
$(document).on('click', '.verification', function(event) {
    event.preventDefault();
    var unique_id = $(this).data('uniqueid');
    var product_id = $(this).data('productid');

    $(this).removeClass('verification');
    $(this)
        .children('i')
        .removeClass(function(index, className) {
            return (className.match(/(^|\s)mdi-\S+/g) || []).join(' ');
        })
        .removeClass('grey-text')
        .addClass('mdi-loading mdi-spin blue-text');

    if (unique_id) {
        var title, message, buttons, style;
        var metadata = '';
        $.ajax({
            type: 'GET',
            url: '/registration/verify/' + unique_id + '?method=manual&product_id=' + product_id,
            dataType: 'json',
            context: this,
            cache: false,
            success: function(a) {
                for (var key in a.metadata) {
                    metadata += '<li><strong>' + key + '</strong>: ' + a.metadata[key] + '</li>';
                }
                style = 'green';
                title = 'Correcto';
                message = '<ul class="white-text"><li><h5>' + a.customer.first_name + ' ' + a.customer.last_name + '</h5></li><li style="text-transform:capitalize;">' + a.type + '</li>' + metadata + '</ul>';

                $('.results-scan').append(message);

                $(this)
                    .closest('tr')
                    .addClass('green lighten-5');
                $(this)
                    .children('i')
                    .removeClass(function(index, className) {
                        return (className.match(/(^|\s)mdi-\S+/g) || []).join(' ');
                    })
                    .removeClass('blue-text')
                    .addClass('mdi-check green-text');

                callModal(style, title, message);

                setTimeout(function() {
                    cleanner();
                }, 2000);
            },
            error: function(a, b, c) {
                a = JSON.parse(a.responseText);
                if (a.level == 'danger') {
                    style = 'red';
                    title = 'Error';
                    message = '<p>' + a.message + '</p>';
                } else if (a.level == 'warning') {
                    style = 'orange';
                    title = 'Cuidado';
                    message = '<p>' + a.message + '</p>';
                }

                $(this).addClass('verification');
                $(this)
                    .children('i')
                    .removeClass(function(index, className) {
                        return (className.match(/(^|\s)mdi-\S+/g) || []).join(' ');
                    })
                    .removeClass('blue-text')
                    .addClass('mdi-check grey-text');

                callModal(style, title, message);
                setTimeout(function() {
                    cleanner();
                }, 2000);
            }
        });
    }
});
