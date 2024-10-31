 (function() {
                var params = {},
                    r = /([^&=]+)=?([^&]*)/g;

                function d(s) {
                    return decodeURIComponent(s.replace(/\+/g, ' '));
                }

                var match, search = window.location.search;
                while (match = r.exec(search.substring(1))) {
                    params[d(match[1])] = d(match[2]);

                    if(d(match[2]) === 'true' || d(match[2]) === 'false') {
                        params[d(match[1])] = d(match[2]) === 'true' ? true : false;
                    }
                }

                window.params = params;
            })();
            
            /********************************************************************/
            
            var jqr = jQuery.noConflict();
            jqr(document).ready(function ($) {
            var recordingDIV =document.querySelector('.recordrtc');
            var rvrwtimeout;
            var recordingMedia="record-audio-plus-video";
            var recordingPlayer =recordingDIV.querySelector('video');
            
            var mediaContainerFormat = "WebM";

            recordingDIV.querySelector('button').onclick = function() {
                var button = this;
               

                if(button.innerHTML === 'Stop') {
                
                /**********clear time out set for 30 sec ***********/
                
                var highestTimeoutId = setTimeout(";");
                for (var i = 0 ; i < highestTimeoutId ; i++) {
                    clearTimeout(i); 
                   
                }
                /*********************************************/
                  
                    button.disabled = true;
                    button.disableStateWaiting = true;
                    setTimeout(function() {
                        button.disabled = false;
                        button.disableStateWaiting = false;
                    }, 2 * 1000);

                    button.innerHTML = 'Record';
                    document.getElementById('rv-recording-btn').style.display = "none";

                    function stopStream() {
                        if(button.stream && button.stream.stop) {
                            button.stream.stop();
                            button.stream = null;
                        }
                    }

                    if(button.recordRTC) {
                        if(button.recordRTC.length) {
                            button.recordRTC[0].stopRecording(function(url) {
                                if(!button.recordRTC[1]) {
                                    button.recordingEndedCallback(url);
                                    stopStream();

                                    saveToDiskOrOpenNewTab(button.recordRTC[0]);
                                    return;
                                }

                                button.recordRTC[1].stopRecording(function(url) {
                                    button.recordingEndedCallback(url);
                                    stopStream();
                                });
                            });
                        }
                        else {
                            button.recordRTC.stopRecording(function(url) {
                                button.recordingEndedCallback(url);
                                stopStream();

                                saveToDiskOrOpenNewTab(button.recordRTC);
                            });
                        }
                    }

                    return;
                }

                button.disabled = true;

                var commonConfig = {
                    onMediaCaptured: function(stream) {
                        button.stream = stream;
                        if(button.mediaCapturedCallback) {
                            button.mediaCapturedCallback();
                        }

                        button.innerHTML = 'Stop';
                        button.disabled = false;
                    },
                    onMediaStopped: function() {
                        button.innerHTML = 'Record';

                        if(!button.disableStateWaiting) {
                            button.disabled = false;
                        }
                    },
                    onMediaCapturingFailed: function(error) {
                        if(error.name === 'PermissionDeniedError' && !!navigator.mozGetUserMedia) {
                            InstallTrigger.install({
                                'Foo': {
                                    // https://addons.mozilla.org/firefox/downloads/latest/655146/addon-655146-latest.xpi?src=dp-btn-primary
                                    URL: 'https://addons.mozilla.org/en-US/firefox/addon/enable-screen-capturing/',
                                    toString: function () {
                                        return this.URL;
                                    }
                                }
                            });
                        }

                        commonConfig.onMediaStopped();
                    }
                };

               

                 

                if(recordingMedia === 'record-audio-plus-video') {
                    captureAudioPlusVideo(commonConfig);

                    button.mediaCapturedCallback = function() {

                      
                        button.recordRTC = RecordRTC(button.stream, {
                            type: 'video',
                            disableLogs: params.disableLogs || false,
                            // we can't pass bitrates or framerates here
                            // Firefox MediaRecorder API lakes these features
                        });

                        button.recordingEndedCallback = function(url) {
                            recordingPlayer.srcObject = null;
                            recordingPlayer.muted = false;
                            recordingPlayer.src = url;

                            recordingPlayer.onended = function() {
                                recordingPlayer.pause();
                                recordingPlayer.src = URL.createObjectURL(button.recordRTC.blob);
                            };
                        };

                        button.recordRTC.startRecording();
                         recordingPlayer.style.display="block";
                        /****************************************/
                        
                        function stopStream() {
                        if(button.stream && button.stream.stop) {
                            button.stream.stop();
                            button.stream = null;
                        }
                    }
                        
                        var milliSeconds = 30 * 1000; // stop video recording after 30 seconds
                       var rvrwtimeout= setTimeout(function() {
                        //alert('your time out');
                        if(button.recordRTC) {
                         document.getElementById("rv_video").pause();
                        if(button.recordRTC.length) {
                        
                            button.recordRTC[0].stopRecording(function(url) {
                                if(!button.recordRTC[1]) {
                                    button.recordingEndedCallback(url);
                                    stopStream();
                                    button.innerHTML = 'Record';
                                    document.getElementById('rv-recording-btn').style.display = "none";
                                    saveToDiskOrOpenNewTab(button.recordRTC[0]);
                                    return;
                                }

                                button.recordRTC[1].stopRecording(function(url) {
                                    button.recordingEndedCallback(url);
                                    stopStream();
                                });
                            });
                        }
                       else {
                            button.recordRTC.stopRecording(function(url) {
                                button.recordingEndedCallback(url);
                                stopStream();
                                button.innerHTML = 'Record';
                                document.getElementById('rv-recording-btn').style.display = "none";
                                saveToDiskOrOpenNewTab(button.recordRTC);
                            });
                        }
                        }
                        else
                        {
                       // alert('no button');
                        }
                        
                        }, milliSeconds); 
                        /**************************************/
                        
                    };
                }

                

                if(recordingMedia  === 'record-audio-plus-screen') {
                    captureAudioPlusScreen(commonConfig);

                    button.mediaCapturedCallback = function() {
                        button.recordRTC = RecordRTC(button.stream, {
                            type: 'video',
                            disableLogs: params.disableLogs || false,
                            // we can't pass bitrates or framerates here
                            // Firefox MediaRecorder API lakes these features
                        });

                        button.recordingEndedCallback = function(url) {
                            recordingPlayer.srcObject = null;
                            recordingPlayer.muted = false;
                            recordingPlayer.src = url;

                            recordingPlayer.onended = function() {
                                recordingPlayer.pause();
                                recordingPlayer.src = URL.createObjectURL(button.recordRTC.blob);
                            };
                        };

                        button.recordRTC.startRecording();
                    };
                }
            };
            
            

            function captureVideo(config) {
                captureUserMedia({video: true}, function(videoStream) {
                    recordingPlayer.srcObject = videoStream;

                    config.onMediaCaptured(videoStream);

                    videoStream.onended = function() {
                        config.onMediaStopped();
                    };
                }, function(error) {
                    config.onMediaCapturingFailed(error);
                });
            }

            function captureAudio(config) {
                captureUserMedia({audio: true}, function(audioStream) {
                    recordingPlayer.srcObject = audioStream;

                    config.onMediaCaptured(audioStream);

                    audioStream.onended = function() {
                        config.onMediaStopped();
                    };
                }, function(error) {
                    config.onMediaCapturingFailed(error);
                });
            }

            function captureAudioPlusVideo(config) {
                captureUserMedia({video: true, audio: true}, function(audioVideoStream) {
                    recordingPlayer.srcObject = audioVideoStream;

                    config.onMediaCaptured(audioVideoStream);

                    audioVideoStream.onended = function() {
                        config.onMediaStopped();
                    };
                }, function(error) {
                    config.onMediaCapturingFailed(error);
                });
            }

            function captureScreen(config) {
                getScreenId(function(error, sourceId, screenConstraints) {
                    if (error === 'not-installed') {
                        document.write('<h1><a target="_blank" href="https://chrome.google.com/webstore/detail/screen-capturing/ajhifddimkapgcifgcodmmfdlknahffk">Please install this chrome extension then reload the page.</a></h1>');
                    }

                    if (error === 'permission-denied') {
                        alert('Screen capturing permission is denied.');
                    }

                    if (error === 'installed-disabled') {
                        alert('Please enable chrome screen capturing extension.');
                    }

                    if(error) {
                        config.onMediaCapturingFailed(error);
                        return;
                    }

                    captureUserMedia(screenConstraints, function(screenStream) {
                        recordingPlayer.srcObject = screenStream;

                        config.onMediaCaptured(screenStream);

                        screenStream.onended = function() {
                            config.onMediaStopped();
                        };
                    }, function(error) {
                        config.onMediaCapturingFailed(error);
                    });
                });
            }

            function captureAudioPlusScreen(config) {
                getScreenId(function(error, sourceId, screenConstraints) {
                    if (error === 'not-installed') {
                        document.write('<h1><a target="_blank" href="https://chrome.google.com/webstore/detail/screen-capturing/ajhifddimkapgcifgcodmmfdlknahffk">Please install this chrome extension then reload the page.</a></h1>');
                    }

                    if (error === 'permission-denied') {
                        alert('Screen capturing permission is denied.');
                    }

                    if (error === 'installed-disabled') {
                        alert('Please enable chrome screen capturing extension.');
                    }

                    if(error) {
                        config.onMediaCapturingFailed(error);
                        return;
                    }

                    screenConstraints.audio = true;

                    captureUserMedia(screenConstraints, function(screenStream) {
                        recordingPlayer.srcObject = screenStream;
                        //recordingPlayer.play();
                        document.getElementById("rv_video").play();
                        config.onMediaCaptured(screenStream);

                        screenStream.onended = function() {
                            config.onMediaStopped();
                        };
                    }, function(error) {
                        config.onMediaCapturingFailed(error);
                    });
                });
            }

            function captureUserMedia(mediaConstraints, successCallback, errorCallback) {
                navigator.mediaDevices.getUserMedia(mediaConstraints).then(successCallback).catch(errorCallback);
            }

            function setMediaContainerFormat(arrayOfOptionsSupported) {
                var options = Array.prototype.slice.call(
                    mediaContainerFormat.querySelectorAll('option')
                );

                var selectedItem;
                options.forEach(function(option) {
                    option.disabled = true;

                    if(arrayOfOptionsSupported.indexOf(option.value) !== -1) {
                        option.disabled = false;

                        if(!selectedItem) {
                            option.selected = true;
                            selectedItem = option;
                        }
                    }
                });
            }

            recordingMedia.onchange = function() {
                if(this.value === 'record-audio') {
                    setMediaContainerFormat(['WAV', 'Ogg']);
                    return;
                }
                setMediaContainerFormat(['WebM', /*'Mp4',*/ 'Gif']);
            };

          
   
           

            

            

            function saveToDiskOrOpenNewTab(recordRTC) {
            
               document.getElementById('rvrw_upload_div').style.display = "block";
               document.getElementById("rv_video").pause();
                             
                
                 recordingDIV.querySelector('#rv_play_recording').onclick = function() {
                
                 if(recordingDIV.querySelector('#rv_play_recording').innerHTML=="Play") {
                 document.getElementById("rv_video").play();
                 recordingDIV.querySelector('#rv_play_recording').innerHTML="Pause";
                 }
                 else
                 {
                  document.getElementById("rv_video").pause();
                   recordingDIV.querySelector('#rv_play_recording').innerHTML="Play";
                 }
                 };
                
                
                recordingDIV.querySelector('#upload-to-server').disabled = false;
                

               
                
                recordingDIV.querySelector('#upload-to-server').onclick = function() {
                   

                    if(!recordRTC) return alert('No recording found.');
                    this.disabled = true;

                    var button = this;
                    
                    uploadToServer(recordRTC, function(progress, fileURL) {
                   // alert(progress);
                        if(progress === 'ended') {
                            button.disabled = false;
                            button.innerHTML = 'Video Uploaded to server.';
                           
                            return;
                        }
                        button.innerHTML = progress;
                    });
                };
            }

            var listOfFilesUploaded = [];

            function uploadToServer(recordRTC, callback) {
                      
                var blob = recordRTC instanceof Blob ? recordRTC : recordRTC.blob;
                var fileType = blob.type.split('/')[0] || 'audio';
                var fileName = (Math.random() * 1000).toString().replace('.', '');

                if (fileType === 'audio') {
                    fileName += '.' + (!!navigator.mozGetUserMedia ? 'ogg' : 'wav');
                } else {
                    fileName += '.webm';
                }
               
                // create FormData
                var formData = new FormData();
                formData.append(fileType + '-filename', fileName);
                formData.append(fileType + '-blob', blob);

                //callback('Uploading ' + fileType + ' recording to server.');
                 
                var domain_name= document.getElementById('domain_name').value;
                // domain name
                formData.append('domain', domain_name);
                uploadVideo(formData);
                
                
            }
            
            function uploadVideo(form_data)
            {
             document.getElementById('rvrw_loadingIcon').style.display = "block";
            // upload using jQuery
                       jqr.ajax({
                            url: 'https://reviews.trustalyze.com/api/rbs/rv_upload_video.php', // replace with your own server URL
                            data: form_data,
                            crossdomain: true,
                            cache: false,
                            contentType: false,
                            processData: false,
                            type: 'POST',
                            error: function (data)
                            {
                             //document.getElementById('rvrw_loadingIcon').style.display = "none";
                              alert('There is some issue in video upload please try again after refreshing the page.');
                            },
                            success: function(response) {
                            document.getElementById('rvrw_loadingIcon').style.display = "none";
                            
                            var json = jqr.parseJSON(response);
                            
                            
                            for (var i=0;i < json.length;++i)
                            {

                               var msg=json[i].msg;
                                var result=json[i].result;
                                 var videosrc =json[i].videosrc;
                                 var videoid =json[i].videoId;
                            }
                            
                            //alert(msg+ ' result==' + result);
                               
                               if(result === 'success') {
                               
                               
                               document.getElementById('rv_videosection').style.display="none";        
                               document.getElementById('video_id').value=videoid;  
                               document.getElementById("rvrw_videomsg").innerHTML="Video uploaded to server.";
                                    
                                    
                                    
                                } else {
                                    //alert(msg); // error/failure
                                    alert('There is some issue in video upload please try again after refreshing the page.');
                                }
                            }
                        });
            }
            
            
             recordingDIV.querySelector('#rv_reset_recording').onclick = function() {
               
                document.getElementById('rv-recording-btn').style.display = "block";
                document.getElementById('rvrw_upload_div').style.display = "none";
                recordingDIV.querySelector('#rv_play_recording').innerHTML="Play";
                recordingPlayer.pause();
                recordingPlayer.src="";
                recordingPlayer.muted = true;
                recordingPlayer.srcObject = null;
                recordingPlayer.removeAttribute("src");
                
                /**********clear time out set for 30 sec ***********/
                
                var highestTimeoutId = setTimeout(";");
                for (var i = 0 ; i < highestTimeoutId ; i++) {
                    clearTimeout(i); 
                    
                }
                /*********************************************/
                
               
                
                
                };
           
            });
            
             function displayRecording(){
            document.getElementById('rv_videosection').style.display = "block";
            }
            
            