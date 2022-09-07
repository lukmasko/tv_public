
var workerResult = 0;

onmessage = function(e) {
    var formData = new FormData();
    var workerData = e.data[0];

    formData.append('file_data', workerData.file_data, workerData.file_name);
    formData.append('file_name', workerData.file_name);
    formData.append('file_size', workerData.file_size);
    formData.append('file_type', workerData.file_type);

    sendXHR(formData, "/upload-video", "POST", false);
    postMessage(workerResult);
}

function sendXHR(formData, path, method, mode=true){
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            var response = JSON.parse(this.responseText);
            workerResult = response;
        }
    }
    xhr.open(method, path, mode);
    xhr.send(formData);
}