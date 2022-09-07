
var workerHandle = new Worker('/media/scripts/upload-video.js');
var workerDataPacket = {
    percent: 0,
    process_state: 0,
    file_name: "",
    file_data: [],
    file_type: 0,
    file_size: 0,
    expect_data_from: 0,
    expect_data_to:  0
};
var FileArrayBuffer;

function loadFile(elem)
{
    var hFile = elem.files[0];
    if(hFile === undefined)
        return;
    
    workerDataPacket.file_name = hFile.name;
    workerDataPacket.file_type = hFile.type;
    workerDataPacket.file_size = hFile.size;
    workerDataPacket.file_data = new Blob();
        
    var fileReader = new FileReader();
    fileReader.onload = function(){
        FileArrayBuffer = fileReader.result;
        workerHandle.postMessage([workerDataPacket]);
    }
    fileReader.readAsArrayBuffer(hFile);
}

workerHandle.onmessage = function(e){
    var progresBar = document.getElementById('progress_bar');
    var percentageVal = document.getElementById('percentage_value');
    var partVidName = document.getElementById('part_vid_name');
    var partVidSize = document.getElementById('part_vid_size');
    var partVidType = document.getElementById('part_vid_type');

    workerDataPacket.percent = e.data.percent;
    workerDataPacket.process_state = e.data.process_state;
    workerDataPacket.expect_data_from = e.data.expect_data_from;
    workerDataPacket.expect_data_to = e.data.expect_data_to;
    workerDataPacket.file_type = e.data.file_type;
    workerDataPacket.file_name = e.data.file_name;
    workerDataPacket.file_size = e.data.file_size;

    var percentValue = workerDataPacket.percent + '%';
    progresBar.style.width = percentValue;
    percentageVal.innerHTML = workerDataPacket.percent + " %";

    if(workerDataPacket.process_state == 1)
    {
        partVidName.value = workerDataPacket.file_name;
        partVidSize.value = workerDataPacket.file_size;
        partVidType.value = workerDataPacket.file_type;
        
        return;
    }

    bytes = new Uint8Array(FileArrayBuffer, workerDataPacket.expect_data_from, workerDataPacket.expect_data_to);
    workerDataPacket.file_data = new Blob([bytes], {type: workerDataPacket.file_type});
    workerHandle.postMessage([workerDataPacket]);
}