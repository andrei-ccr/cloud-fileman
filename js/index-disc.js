import { ChangeCurrentDirectory, Rename, Select, DeselectAll, ReadCurrentDirectory} from './modules/files.js';
import { ShowCDInfo } from './modules/properties.js';
import { IntegrateBarMenu, IntegrateContextMenu, DeclareMenuButtons } from './modules/menus.js';
import { Status, GetDiscData } from './modules/states.js';
import { Upload, IntegrateDragDropUploader } from './modules/uploader.js';
import { DeclareModalButtons, ShowMessage } from './modules/modals.js';

if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
	|| /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) { 
	Status.isMobile = true;
}

IntegrateBarMenu();
IntegrateContextMenu();
DeclareMenuButtons();

DeclareModalButtons();

IntegrateDragDropUploader();


$(document).on('change', "#file-up", function() {
	var files = document.getElementById('file-up').files;
	
	Upload(files);
	return;
});

//Clicking anywhere on the screen
$(document).on("mousedown", function(e) {

	//Remove any context menu
	if(!$(e.target).parents(".cm").length > 0) {
		$(".cm").remove();
	}
	
	//If renaming is in proccess, finish it
	if(!$(e.target).is("input")) {
		if($(".rename-input").length>0) {
			Rename(Status.targetFile.data("id"), $(".rename-input").val(), 1);
			$(".rename-input").replaceWith("<p>"+$(".rename-input").val()+"</p>");
		}
	}

	//If clicked inside files container, deselect any currently selected files
	if( (!$(e.target).is("#info-bar")) && (!$(e.target).parents("#bar").length>0) && (!$(e.target).is("#bar")) && (!$(e.target).is(".f")) && (!$(e.target).parents(".f").length>0) && (!$(e.target).parents(".cm").length>0) ) {
		DeselectAll();
	}
});


//Clicking on a file
$(document).on("click", ".file-container .f", function(e) {

	//Select the file. If it's a folder and it's already selected, enter it.
	if(!$(this).hasClass("selected")) {
		Select($(this));
	} else {
		if($(this).hasClass("dir")) {
			ChangeCurrentDirectory($(this).data("id"));
			ShowCDInfo();
		}
	}
});

//File rename
$(document).on('keypress', ".rename-input", function(e) {
	let key = e.which;
	if(key == 13) {
		if($.trim($(".rename-input").val()) != "") {
			Rename(Status.targetFile.data("id"), $(".rename-input").val());
			$(".rename-input").replaceWith("<p>"+$(".rename-input").val()+"</p>");
		}
	}
});
$(document).on('click', ".rename-input", function(e) {
	e.stopPropagation();
});


//Go to the root of disk
$(document).on('click', "#root-location", function(e) {
	ChangeCurrentDirectory(0);
});

$(document).on("click",".close-modal", function() {
	$(".modal").remove();
});

$(document).on("input", "#search-bar", function() {
	
	let searchQuery = $(this).val();
	if(searchQuery.length == 0) {
		ReadCurrentDirectory();
		return;
	}

	let dd = GetDiscData();

	$("#file-listing").html("<span class='msg'>Searching...</span>");
	$.ajax({
		url: 'sys/api/search', 
		data: { query: searchQuery, discid: dd.discid, permid: dd.permid },
		dataType: 'json',
		cache: false,
		type: 'post'
	})
	.done(function(JSONResp) {
		if(JSONResp.found === false){
			$("#file-listing").html("<span class='msg'>No results found.</span>");
		} else {
			$("#file-listing").html("");
			let FileDOM = "";
			let FilesJSON = JSONResp.result;
			$("#file-listing").html("<span class='msg'  style='cursor: default; font-size: 1.6rem; margin: 0;'> results found for '" + searchQuery + "'</span>");
			FilesJSON.forEach(function(file) {
				FileDOM = "<div class='f noselect " + ((file["isDir"]!=false)?"dir":"") + "' data-id='"+file["fid"]+"'><div>";
					if(file.isDir != false) 
						FileDOM += "<svg class='svg-icon' viewBox='0 0 20 20' style='width: 1em; height: 1em;'><path d='M17.927,5.828h-4.41l-1.929-1.961c-0.078-0.079-0.186-0.125-0.297-0.125H4.159c-0.229,0-0.417,0.188-0.417,0.417v1.669H2.073c-0.229,0-0.417,0.188-0.417,0.417v9.596c0,0.229,0.188,0.417,0.417,0.417h15.854c0.229,0,0.417-0.188,0.417-0.417V6.245C18.344,6.016,18.156,5.828,17.927,5.828 M4.577,4.577h6.539l1.231,1.251h-7.77V4.577z M17.51,15.424H2.491V6.663H17.51V15.424z' style='fill: #777;'></path></svg>";
					else
						FileDOM += "<svg class='svg-icon' viewBox='0 0 20 20' style='width: 1em; height: 1em;'><path d='M15.475,6.692l-4.084-4.083C11.32,2.538,11.223,2.5,11.125,2.5h-6c-0.413,0-0.75,0.337-0.75,0.75v13.5c0,0.412,0.337,0.75,0.75,0.75h9.75c0.412,0,0.75-0.338,0.75-0.75V6.94C15.609,6.839,15.554,6.771,15.475,6.692 M11.5,3.779l2.843,2.846H11.5V3.779z M14.875,16.75h-9.75V3.25h5.625V7c0,0.206,0.168,0.375,0.375,0.375h3.75V16.75z' style='fill: #777;'></path></svg>";
					
					FileDOM += "<p>" + file["filename"] + "</p></div></div>";
					$("#file-listing").append(FileDOM);
			});
		}
	})
	.fail(function(res) {
		ShowMessage("<i class='fas fa-exclamation-triangle'></i> Searching process failed!");
	});

})




