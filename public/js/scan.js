var scanner;

function startScan() {
    scanner.addListener('scan', function(code) {
        if (code.length !== 0) {
            verifyCode(code);
        }
    });
}

function startCamera() {

    scanner = new Instascan.Scanner({
        mirror: false,
        video: document.getElementById('preview'),
        refractoryPeriod: 10000,
        scanPeriod: 8
    });
    Instascan.Camera.getCameras().then(function(cameras) {
        if (cameras.length != 0) {
            if (cameras.length > 1) {
                camera = cameras[1];

                scanner.start(camera);
            } else {
                camera = cameras[0];

                scanner.start(camera);
                console.log(camera);
            }
            startScan();
        } else {
            console.log('No hemos detectado cámara');
        }
    });
}

function verifyCode(code) {
    $.ajax({
        type: 'GET',
        url: '/registration/verify/' + code + '?method=qr',
        dataType: 'json',
        cache: false,
        success: function(a) {
            $('#modalmsg').modal('open');
            var title, message, buttons, type;
            var metadata = '';
            if (a.status == 'error') {
                style = 'red';
                title = 'Error';
                message = a.message;
            } else if (a.status == 'warning') {
                style = 'orange';
                title = 'Cuidado';
                message = a.message;
            } else if (a.status == 'success') {
                for (var key in a.metadata) {
                    metadata += '<strong>'+key+'</strong>: '+a.metadata[key]+'<br>';
                }
                style = 'green';
                title = a.message;
                message = a.user+'<br /><span class="type white-text">'+a.type+'</span><br><small>'+metadata+'</small>';
            }
            $('#modalmsg, #modalmsg .modal-footer').removeClass('green orange red');
            $('#modalmsg, #modalmsg .modal-footer').addClass(style);
            $('#modalmsg .modal-content h4').html(title);
            $('#modalmsg .modal-content p').html(message);
        },
        error: function(a) {
            location.reload();
        }
    });
}