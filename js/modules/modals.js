import { GetDiscData } from "./states.js";

export function ShowMessage(Message) {
	$("body").append(`
		<div class="modal">
			<div class="container">
				<a class="close-modal">Close</a>
				<p>` + Message + `</p>
			</div>
		</div>`
	);
	setTimeout(function(){ $(".modal").remove() }, 5000);
}

export function ShowFileDetailsModal(Filename, Size, C, M, A, Shared, Stared) {
    $("body").append(`
    <div class="modal">
        <div class="container">
            <div style="margin-bottom: 5rem;"><svg class='svg-icon' viewBox='0 0 20 20' style='width: 3rem; height: 3rem;'><path d='M17.927,5.828h-4.41l-1.929-1.961c-0.078-0.079-0.186-0.125-0.297-0.125H4.159c-0.229,0-0.417,0.188-0.417,0.417v1.669H2.073c-0.229,0-0.417,0.188-0.417,0.417v9.596c0,0.229,0.188,0.417,0.417,0.417h15.854c0.229,0,0.417-0.188,0.417-0.417V6.245C18.344,6.016,18.156,5.828,17.927,5.828 M4.577,4.577h6.539l1.231,1.251h-7.77V4.577z M17.51,15.424H2.491V6.663H17.51V15.424z' style='fill:#555;'></path></svg><h2 style="font-size: 1.7rem;
    font-weight: 500; margin: 0;">`+ Filename +`</h2></div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr;">
                <span class="prop-title">Size: </span><span class="prop-value">` + Size + `</span>
                <span class="prop-title">Uploaded/created: </span><span class="prop-value">`+ C +`</span>
                <span class="prop-title">Last modified: </span><span class="prop-value">`+ M +`</span>
                <span class="prop-title">Last accessed: </span><span class="prop-value">`+ A +`</span>
                <span class="prop-title">Shared: </span><span class="prop-value">` + Shared + `</span>
                <span class="prop-title">Stared: </span><span class="prop-value">` + Stared + `</span>
            </div>
            
            <button class="close-modal close-modal-btn" style="margin-top:2rem; background-color:white; color:#0f7dcc; font-size: 1.5rem; border:1px solid #0f7dcc;">Close</button>
        </div>
    </div>
    `);

}

export function ShowEditModal(FileId) {
    let dd = GetDiscData();

    $.ajax({
        url: 'sys/api/readfile', 
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
            url: 'sys/api/writefile', 
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
