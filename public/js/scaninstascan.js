


$(document).ready(function(){
  
    var vid = document.getElementById("preview");
    var width, height;

    vid_w_orig = parseInt(vid.style.width);
    vid_h_orig = parseInt(vid.style.height);
    
    // re-scale image when viewport resizes
    $(window).resize(function(){
    
        // get the parent element size
        var container_w = vid.parent().width();
        var container_h = vid.parent().height();
    
        // use largest scale factor of horizontal/vertical
        var scale_w = container_w / vid_w_orig;
        var scale_h = container_h / vid_h_orig;
        var scale = scale_w > scale_h ? scale_w : scale_h;
    
        // scale the video
        vid.width(scale * vid_w_orig);
        vid.height(scale * vid_h_orig);
    
    });
  
  // trigger re-scale on pageload
    $(window).trigger('resize');
  
});

var scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
function startScan(){
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
    Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
          if(cameras[1]){ scanner.start(cameras[1]); } else { scanner.start(cameras[0]); } 
        } else {
          console.error('No cameras found.');
        }
    }).catch(function (e) {
        console.error(e);
    });
};
function stopScan(){
    scanner.removeListener('instascan', function (content){});
};
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
        scanner.stop();
    }).catch(function (e) {
        console.error(e);
    });
};

$('.scan-menu').click(function(event) {
    event.preventDefault();
    startScan();
    startCameras();
    $('#instascan').removeClass('hidden');
    $('.search-menu').removeClass('hidden');
    $('#registrations').addClass('hidden');
    $('.scan-menu').addClass('hidden');
    $('footer').addClass('hidden');
});

$('.search-menu').click(function(event) {
    event.preventDefault();
    stopCameras();
    stopScan();
    $('#registrations').removeClass('hidden');
    $('.scan-menu').removeClass('hidden');
    $('.search-menu').addClass('hidden');
    $('footer').removeClass('hidden');
    $('#instascan').addClass('hidden');
});
