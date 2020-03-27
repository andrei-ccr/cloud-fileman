import { Files } from './files-api.js';

export function Uploader() {
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
        
        Files.Upload(droppedFiles);
    });

    $(document).on('change', "#file-up", function() {
        var droppedFiles = document.getElementById('file-up').files;
        
        Files.Upload(droppedFiles);
        return false;
    });
}