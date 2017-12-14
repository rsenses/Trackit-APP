var scanner = new Instascan.Scanner({ video: document.getElementById('preview') });

function startScan() {
    scanner.addListener('scan', function (code) {
        console.log(code);
        if (code.length !== 0) {
            $.ajax({
                type: "GET",
                url: "/registration/verify/"+ code,
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
                        message = '<h4>'+a.user+'<br /><small class="text-danger">'+a.type+'</small></h4>';
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
};

function startCamera() {
    Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length != 0) {
            if (cameras.length > 1) {
                scanner.start(cameras[1]);
            } else {
                scanner.start(cameras[0]);
            }

            startScan();
        } else {
            BootstrapDialog.show({
                size: BootstrapDialog.SIZE_LARGE,
                title: 'Error',
                message: 'No hemos detectado ninguna cámara en el dispositivo.',
                buttons: buttons = [{
                    label: '<i class="fa fa-times-circle" aria-hidden="true"></i> Cerrar',
                    cssClass: 'btn-primary btn-lg',
                    action: function(dialog) {
                        dialog.close();
                    }
                }],
                type: BootstrapDialog.TYPE_DANGER,
                onhide: function(dialogRef) {
                    decode = true;
                }
            });
        }
    });
};

$('.scan-menu').click(function(event) {
    event.preventDefault();

    startCamera();

    $('#instascan').removeClass('hidden');
    $('.search-menu').removeClass('hidden');
    $('#registrations').addClass('hidden');
    $('.scan-menu').addClass('hidden');
    $('footer').addClass('hidden');
});

$('.search-menu').click(function(event) {
    event.preventDefault();

    scanner.stop();

    $('#registrations').removeClass('hidden');
    $('.scan-menu').removeClass('hidden');
    $('.search-menu').addClass('hidden');
    $('footer').removeClass('hidden');
    $('#instascan').addClass('hidden');
});
