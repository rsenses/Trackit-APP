$('.scan-menu').click(function(event) {
    event.preventDefault();
    // scanner = null;
    let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
    scanner.addListener('scan', function (content) {
        console.log(content);
        if (content.length !== 0) {
        $.ajax({
            type: "GET",
            url: "/registration/verify/"+ content,
            dataType: "json",
            cache: false,
            success: function(a) {
                if (a.status == 'error') {
                    type = BootstrapDialog.TYPE_DANGER;
                    title = 'Error';
                    message = '<h4>'+a.message+'</h4>';
                    buttons = [{
                        label: '<i class="fa fa-times-circle" aria-hidden="true"></i> Cerrar',
                        cssClass: 'btn-primary btn-lg',
                        action: function(dialog) {
                            dialog.close();
                        }
                    }];
                } else if (a.status == 'success') {
                    type = BootstrapDialog.TYPE_SUCCESS;
                    title = a.message;
                    message = '<h4>'+a.user+'</h4>';
                    buttons = [{
                        label: '<i class="fa fa-times-circle" aria-hidden="true"></i> Cerrar',
                        cssClass: 'btn-primary btn-lg',
                        action: function(dialog) {
                            dialog.close();
                        }
                    }];
                    $(this).data('uniqueid', null)
                        .removeClass('text-danger')
                        .addClass('text-success')
                        .children('i')
                        .removeClass('fa-times-circle')
                        .addClass('fa-check-circle');
                }
                BootstrapDialog.show({
                    size: BootstrapDialog.SIZE_LARGE,
                    title: title,
                    message: message,
                    buttons: buttons,
                    type: type,
                    onhide: function(dialogRef) {
                        decode = true;
                    }
                });
            },
            error: function(a) {
                location.reload();
            }
        });
        }
    });
    Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
          scanner.start(cameras[0]);
        } else {
          console.error('No cameras found.');
        }
    }).catch(function (e) {
        console.error(e);
    });
    $('#scan').removeClass('hidden');
});

$('.search-menu').click(function(event) {
    // scanner.stop()
    event.preventDefault();
    $('#registrations').removeClass('hidden');
    $('.scan-menu').removeClass('hidden');
    $('.search-menu').addClass('hidden');
    $('footer').removeClass('hidden');
    $('#scan').addClass('hidden');
    // localStream.getTracks()[0].stop();
    // decode = false;
});
