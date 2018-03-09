var scanner;

function startScan() {
    scanner.addListener('scan', function (code) {
        if (code.length !== 0) {
            verifyCode(code);
        }
    });
};

function startCamera() {
    scanner = new Instascan.Scanner({
        mirror: false,
        video: document.getElementById('preview'),
        refractoryPeriod: 10000,
        scanPeriod: 8
    });

    Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length != 0) {
            if (cameras.length > 1) {
                camera = cameras[1];

                scanner.start(camera);
            } else {
                camera = cameras[0];

                scanner.start(camera);
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

function verifyCode(code) {
    $('#verify').val('');

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
            var dialog = BootstrapDialog.show({
                size: BootstrapDialog.SIZE_LARGE,
                title: title,
                message: message,
                buttons: buttons,
                type: type
            });

            setTimeout(function () {
                dialog.close();
            }, 3000);
        },
        error: function(a) {
            location.reload();
        }
    });
}

$('#verify').on('input', function(e) {
    var code = $(this).val();

    e.target.blur();

    verifyCode(code);
});

$('html').bind('keydown', function(e) {
    if (e.keyCode === 0 && e.originalEvent && e.originalEvent.key == 'Unidentified') {
        $('#verify').focus();
    }
});

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

    scanner.stop().then(function () {
        scanner = null;
        $('#registrations').removeClass('hidden');
        $('.scan-menu').removeClass('hidden');
        $('.search-menu').addClass('hidden');
        $('footer').removeClass('hidden');
        $('#instascan').addClass('hidden');
    });
});
