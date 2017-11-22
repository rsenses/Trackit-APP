let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
function startCameras(){
    Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length != 0) {
          if(cameras[1]){ 
            scanner.start(cameras[1]); 
            } else { 
                scanner.start(cameras[0]); 
            } 
        } else {
          console.error('No cameras found.');
        }
    }).catch(function (e) {
        console.error(e);
    }); 
}; 
function stopCameras(){
    Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length != 0) {
            if(cameras[1]){ 
                scanner.stop(cameras[1]); 
            } else { 
                scanner.stop(cameras[0]); 
            }
            scanner.stop();
        } else {
          console.error('No cameras found.');
        }
    }).catch(function (e) {
        console.error(e);
    });
};
$('.scan-menu').click(function(event) {
    event.preventDefault();
    scanner.addListener('scan', function (content) {
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
                    console.log(a);
                        type = BootstrapDialog.TYPE_SUCCESS;
                        title = a.message;
                        message = '<h4>'+a.user+'<br><small>'+a.type+'</small></h4>';
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
    startCameras();
    $('#scan').removeClass('hidden');
    $('.search-menu').removeClass('hidden');
    $('#registrations').addClass('hidden');
    $('.scan-menu').addClass('hidden');
    $('footer').addClass('hidden');
});

$('.search-menu').click(function(event) {
    event.preventDefault();
    stopCameras();
    $('#registrations').removeClass('hidden');
    $('.scan-menu').removeClass('hidden');
    $('.search-menu').addClass('hidden');
    $('footer').removeClass('hidden');
    $('#scan').addClass('hidden');
});
