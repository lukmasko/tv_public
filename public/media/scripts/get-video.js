var videoSource;
var videoElement;
var mediaSource;
var isLastPart;
var countPart=1;

window.addEventListener('load', function(){
    isLastPart = false;
});

var buffering = false;
window.addEventListener('focus', function(){
    if( buffering )
        return;
    
    buffering = true;
    getMediaPresentationData();
});

function setMediaPresentationData(data){

    mediaSource = new window.MediaSource();
    var objUrl = URL.createObjectURL(mediaSource);
    videoElement = document.getElementById('video');

    videoElement.src = objUrl;
    videoElement.width = data.width;
    videoElement.height = data.height;

    mediaSource.addEventListener('sourceopen', function (e){
        videoSource = mediaSource.addSourceBuffer('video/mp4; codecs="' + data.codecs + '"');

        videoSource.addEventListener('updateend', function(){
            if(isLastPart){
                mediaSource.endOfStream();
                isLastPart = false;
            }
            else{
                getVideo(countPart);
                countPart++;
            }
        });
    });

    videoElement.ondurationchange = (event) => {
        //console.log('Not sure why, but the duration of the video has changed.');
    };
}

function getMediaPresentationData()
{
    var url = '/get-part?v=1';
    var xhr = new XMLHttpRequest();
    xhr.open('get', url, true);
    xhr.responseType = 'text';
    xhr.send();

    xhr.onreadystatechange = function()
    {
        if(this.readyState == 4 && this.status == 200){
            var xmlData = JSON.parse(this.responseText);
            setMediaPresentationData(xmlData);
            getVideo(0);
        }
    }
}

function getVideo(part){
    vid = getVideoId();
    var url = '/get-part?v=' +vid+ '&p=' + part;
    var xhr = new XMLHttpRequest();
    xhr.open('get', url, true);
    xhr.responseType = 'arraybuffer';
    xhr.send();

    xhr.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            if(xhr.response.byteLength <= 0){
                isLastPart = true;
            }
            videoSource.appendBuffer(new Uint8Array(xhr.response));
        }
    }
}

function getVideoId(){
    var cUrl = window.location.href;
    var url = new URL(cUrl);
    return url.searchParams.get('v');
}