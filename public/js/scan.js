var decode = true;

var video = document.getElementById("video");
var canvas = document.getElementById("canvas");
var context = canvas.getContext("2d");
var width, height, localStream;
var start = null;

// var requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame ||
// window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
//
// window.requestAnimationFrame = requestAnimationFrame;

width = parseInt(canvas.style.width);
height = parseInt(canvas.style.height);

function decoderPlay() {
    var constraints;
    var ua = navigator.userAgent;

    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile|mobile|CriOS/i.test(ua)) {
        if (/Chrome/i.test(ua)) {
            navigator.mediaDevices.enumerateDevices().then(devices => {
                var sourceId = null;

                // enumerate all devices
                for (var device of devices) {
                    // if there is still no video input, or if this is the rear camera
                    if (device.kind == 'videoinput' &&
                    (!sourceId || device.label.indexOf('back') !== -1)) {
                        sourceId = device.deviceId;
                    }
                }

                // we didn't find any video input
                if (!sourceId) {
                    throw 'no video input';
                }

                constraints = {
                    video: {
                        sourceId: sourceId
                    }
                };

                startCamera(constraints);
            });
        } else {
            constraints = {
                video: {
                    facingMode: {exact: 'environment'}
                }
            };
        }
    } else {
        constraints = {
            video: true
        };

        startCamera(constraints);
    }
}

function startCamera(constraints) {
    navigator.mediaDevices.getUserMedia(constraints)
        .then(stream => successCallback(stream))
        .catch(e => console.log(e.name + ": "+ e.message));
}

function successCallback(stream) {
    if (video.srcObject !== undefined) {
        video.srcObject = stream;
    } else if (window.URL) {
        video.src = window.URL.createObjectURL(stream);
    } else {
        video.src = stream;
    }

    video.onloadedmetadata = function(e) {
        width = parseInt(this.videoWidth);
        height = parseInt(this.videoHeight);

        canvas.width = width;
        canvas.height = height;
    };

    localStream = stream;

    window.requestAnimationFrame(frame);

}

function errorCallback() {}

function frame() {
    window.requestAnimationFrame(frame);

    scan();
}

function scan() {
    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        // Load the video onto the canvas
        context.drawImage(video, 0, 0, width, height);
        // Load the image data from the canvas

        var imageData = context.getImageData(0, 0, width, height);
        var decoded = jsQR.decodeQRFromImage(imageData.data, imageData.width, imageData.height);

        if (decoded) {
            var type, buttons, message, title;

            if (decode === true) {
                decode = false;
                $.ajax({
                    type: "GET",
                    url: "/registration/verify/"+ decoded,
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
        }
    }
}
$('.scan-menu').click(function(event) {
    event.preventDefault();

    $('#registrations').addClass('hidden');
    $('.scan-menu').addClass('hidden');
    $('.search-menu').removeClass('hidden');
    $('footer').addClass('hidden');
    $('#scan').removeClass('hidden');

    decode = true;
    decoderPlay();
});

$('.search-menu').click(function(event) {
    event.preventDefault();

    decode = false;

    let tracks = localStream.getTracks();

    tracks.forEach(function(track) {
        track.stop();
    });

    video.srcObject = null;

    context.clearRect(0, 0, canvas.width, canvas.height);

    $('#registrations').removeClass('hidden');
    $('.scan-menu').removeClass('hidden');
    $('.search-menu').addClass('hidden');
    $('footer').removeClass('hidden');
    $('#scan').addClass('hidden');
});
