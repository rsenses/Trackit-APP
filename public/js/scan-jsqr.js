var jbScanner;

function onQRCodeScanned(scannedText)
{
    console.log(scannedText);

    $.ajax({
        type: "GET",
        url: "/registration/verify/"+ scannedText,
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
                // type = BootstrapDialog.TYPE_SUCCESS;
                // title = a.message;
                // message = '<h4>'+a.user+'</h4><p>¿Va a <strong>asistir</strong> al almuerzo?</p>';
                // buttons = [{
                //     label: '<i class="fa fa-check-circle" aria-hidden="true"></i> Si',
                //     cssClass: 'btn-success btn-lg',
                //     action: function(dialog) {
                //         dialog.close();
                //     }
                // }, {
                //     label: '<i class="fa fa-ban" aria-hidden="true"></i> No',
                //     cssClass: 'btn-danger btn-lg',
                //     action: function(dialog) {
                //         dialog.close();
                //     }
                // }];
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

//this function will be called when JsQRScanner is ready to use
function JsQRScannerReady()
{
    //create a new scanner passing to it a callback function that will be invoked when
    //the scanner succesfully scan a QR code
    jbScanner = new JsQRScanner(onQRCodeScanned);
    jbScanner.stopScanning();
    //reduce the size of analyzed image to increase performance on mobile devices
    jbScanner.setSnapImageMaxSize(300);
    var scannerParentElement = document.getElementById("scanner");
    if(scannerParentElement)
    {
        //append the jbScanner to an existing DOM element
        jbScanner.appendTo(scannerParentElement);
    }
}

$('.scan-menu').click(function(event) {
    event.preventDefault();

    $('#registrations').addClass('hidden');
    $('.scan-menu').addClass('hidden');
    $('.search-menu').removeClass('hidden');
    $('footer').addClass('hidden');
    $('#scan').removeClass('hidden');

    jbScanner.resumeScanning();
});

$('.search-menu').click(function(event) {
    event.preventDefault();

    jbScanner.stopScanning();

    $('#registrations').removeClass('hidden');
    $('.scan-menu').removeClass('hidden');
    $('.search-menu').addClass('hidden');
    $('footer').removeClass('hidden');
    $('#scan').addClass('hidden');
});
