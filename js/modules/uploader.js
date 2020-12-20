import {GetDiscData} from './states.js';
import {ShowMessage} from './modals.js';
import {ReadCurrentDirectory} from './files.js';
import {UpdateDiskSpace} from './properties.js';

export function IntegrateDragDropUploader() {

    //Drag and drop file upload 
    $(document).on('dragover','#file-listing', function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
    $(document).on('dragenter','#file-listing', function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
    $(document).on('drop','#file-listing', function(e) {
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
        ShowMessage("File(s) uploaded successfully");
        ReadCurrentDirectory();
        UpdateDiskSpace();

    })
    .fail(function(resp){
        ShowMessage("Upload failed: " + resp.responseText);
    });
}

function DiscDataToBlob() {
    let JSONData = JSON.stringify(GetDiscData());

    return new Blob([JSONData], {
        type: 'application/json'
    });
}