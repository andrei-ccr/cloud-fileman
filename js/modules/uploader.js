import {GetDiscData} from './states.js';

export function IntegrateDragDropUploader() {

    //Drag and drop file upload 
    $(document).on('dragover','#file-zone', function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
    $(document).on('dragenter','#file-zone', function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
    $(document).on('drop','#file-zone', function(e) {
        var droppedFiles = e.originalEvent.dataTransfer.files;
        e.preventDefault();
        e.stopPropagation();
        
        if(droppedFiles.length <= 0) return false; //If the dropped object is not a file
        
        Upload(droppedFiles);
    });
}

export function Upload(Files) {
    var fd = new FormData();
    
    fd.append(UploadProgressTrackId, "123"); //For upload progress tracking DEPRECATED
    fd.append("discdata", DiscDataToBlob()); //Required data (Disc ID and Current Directory Id)

    $.each(Files, function(ind, val) {
        fd.append("file" + ind, val);
    });
    
    $.ajax({
        url: 'sys/api/upload', 
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: fd,                        
        type: 'post'
    })
    .done(function(JSONResp){
        $("#errors").html("");
    })
    .fail(function(resp){
        $("#errors").html(resp.responseJSON.error);
    });
}

function DiscDataToBlob() {
    let JSONData = JSON.stringify(GetDiscData());

    return new Blob([JSONData], {
        type: 'application/json'
    });
}