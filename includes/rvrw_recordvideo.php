<!DOCTYPE html>
<?php include_once '../../../../wp-config.php';?>
<html>
    <head>
        <script src="<?php echo plugins_url('rebusify-verified-reviews-woocommerce-extension/js/RecordRTC.js');?>"></script>
        <!--<script src="https://cdn.WebRTC-Experiment.com/RecordRTC.js"></script>-->
        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <style>
            video {
              max-width: 100%;
              border: 5px solid yellow;
              border-radius: 9px;
            }
            body {
              background: black;
            }
            h1 {
              color: yellow;
            }
        </style>
    </head>

    <body>
        <h1 id="header">RecordRTC Upload to PHP</h1>
        <input type="button" onClick="startRecording()" name="btn" value="Start Video Recording" />
        <br>
        <video id="your-video-id" controls="" autoplay=""></video>

        <script type="text/javascript">
            // capture camera and/or microphone
            function startRecording() {
            navigator.mediaDevices.getUserMedia({ video: true, audio: true }).then(function(camera) {

                // preview camera during recording
                document.getElementById('your-video-id').muted = true;
                document.getElementById('your-video-id').srcObject = camera;

                // recording configuration/hints/parameters
                var recordingHints = {
                    type: 'video'
                };

                // initiating the recorder
                var recorder = RecordRTC(camera, recordingHints);

                // starting recording here
                recorder.startRecording();
                // change this later for 30 second
                // auto stop recording after 5 seconds
                //var milliSeconds = 5 * 1000;
                var milliSeconds = 30 * 1000;
                setTimeout(function() {

                    // stop recording
                    recorder.stopRecording(function() {
                        
                        // get recorded blob
                        var blob = recorder.getBlob();

                        // generating a random file name
                        var fileName = getFileName('webm');

                        // we need to upload "File" --- not "Blob"
                        var fileObject = new File([blob], fileName, {
                            type: 'video/webm'
                        });

                        var formData = new FormData();

                        // recorded data
                        formData.append('video-blob', fileObject);

                        // file name
                        formData.append('video-filename', fileObject.name);

                        document.getElementById('header').innerHTML = 'Uploading to PHP using jQuery.... file size: (' +  bytesToSize(fileObject.size) + ')';

                        // upload using jQuery
                        $.ajax({
                            url: 'https://reviews.trustalyze.com/api/rbs/save.php', // replace with your own server URL
                            data: formData,
                            crossdomain: true,
                            cache: false,
                            contentType: false,
                            processData: false,
                            type: 'POST',
                            success: function(response) {
                                if (response === 'success') {
                                    alert('successfully uploaded recorded blob');

                                    // file path on server
                                    var fileDownloadURL = 'https://reviews.trustalyze.com/api/rbs/uploads/' + fileObject.name;

                                    // preview the uploaded file URL
                                    document.getElementById('header').innerHTML = '<a href="' + fileDownloadURL + '" target="_blank">' + fileDownloadURL + '</a>';

                                    // preview uploaded file in a VIDEO element
                                    document.getElementById('your-video-id').src = fileDownloadURL;

                                    // open uploaded file in a new tab
                                    window.open(fileDownloadURL);
                                } else {
                                    alert(response); // error/failure
                                }
                            }
                        });

                        // release camera
                        document.getElementById('your-video-id').srcObject = null;
                        camera.getTracks().forEach(function(track) {
                            track.stop();
                        });

                    });

                }, milliSeconds);
            });
            }

            // this function is used to generate random file name
            function getFileName(fileExtension) {
                var d = new Date();
                var year = d.getUTCFullYear();
                var month = d.getUTCMonth();
                var date = d.getUTCDate();
                return 'RecordRTC-' + year + month + date + '-' + getRandomString() + '.' + fileExtension;
            }

            function getRandomString() {
                if (window.crypto && window.crypto.getRandomValues && navigator.userAgent.indexOf('Safari') === -1) {
                    var a = window.crypto.getRandomValues(new Uint32Array(3)),
                        token = '';
                    for (var i = 0, l = a.length; i < l; i++) {
                        token += a[i].toString(36);
                    }
                    return token;
                } else {
                    return (Math.random() * new Date().getTime()).toString(36).replace(/\./g, '');
                }
            }
        </script>
    </body>
</html>