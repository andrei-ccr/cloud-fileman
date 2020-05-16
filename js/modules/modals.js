import { GetDiscData } from "./states.js";

export function ShowEditModal(FileId) {
    let dd = GetDiscData();

    $.ajax({
        url: 'operations/readfile', 
        data: {discid: dd.discid, fid: FileId, permid: dd.permid},
        dataType: 'json',
        cache: false,
        type: 'post'
    })
    .done( function(res){
        if(res.content.length >= 2147483646) {
            $("#errors").html("<i class='fas fa-exclamation-circle'></i> Warning! Files this big cannot be edited!");
        }

        $.post("inc/modal-edit-file", {content: res.content, fid: FileId, filename: res.filename }, function(resp) {
            $("body").append(resp);
        });
    })
    .fail( function() {
        $("#errors").html("<i class='fas fa-exclamation-circle'></i> Couldn't read the contents of this file!");
    });
}

export function DeclareModalButtons() {

    $(document).on("click", ".modal #save", function(e) {
        let fileid = $(".modal").data("fid");
        let dd = GetDiscData();
        let c = $(".modal textarea").val();
        
        if(c.length >= 2147483646) {
            $("#errors").html("<i class='fas fa-exclamation-circle'></i> Files this big cannot be saved!");
            return;
        }

        $.ajax({
            url: 'operations/writefile', 
            data: {discid: dd.discid, fid: fileid, content: c, permid: dd.permid},
            dataType: 'json',
            cache: false,
            type: 'post'
        })
        .done( function(res){
            $(".modal").remove();
        })
        .fail( function() {
            $("#errors").html("<i class='fas fa-exclamation-circle'></i> Couldn't save the file!");
        });
    
    });
    
    $(document).on("click", ".modal #cancel", function(e) {
        $(".modal").remove();
    });
}
