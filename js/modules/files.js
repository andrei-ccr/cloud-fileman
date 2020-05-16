import {Status, ClipboardStatus, GetDiscData} from './states.js';
import {ShowDiskInfo, ShowFileInfo, ShowCDInfo} from './properties.js';

	export function PasteFile() {
		
		if (ClipboardStatus.file == null) return;

		let fid = ClipboardStatus.file;
		let transf_op = (ClipboardStatus.cut)?2:1;

		let dd = GetDiscData();

		$.ajax( {
			url: "operations/transfer",
			data: {source_fid: fid, destination_folder: dd.cd, transfer_op: transf_op, permid: dd.permid},
			cache: false,
			type: 'post'
		})
		.done(function() {
			ReadCurrentDirectory();
		})
		.fail(function(jqXHR) {
			$("#errors").html("<i class='fas fa-exclamation-circle'></i> Can't move/copy here. " + jqXHR.responseText);
		});
	}

	export function ChangeCurrentDirectory(Id) {

		if($(".rename-input").length>0) {
			//Don't change path while renaming
			return false;
		}

		let dd = GetDiscData();

		$.ajax({
			url: 'operations/cd', 
			cache: false,         
			method: 'post',
			dataType: 'json',
			data: { fid: Id, permid: dd.permid }
		})
		.done(function(JSONResp) {
			$('#dinfo').data('cd', JSONResp.cdid);
			ReadCurrentDirectory();

			//Write the current path on the bar
			let DirList = JSONResp.path.split("/");
			$("#path-bar").html('<span id="root-location"><i class="fas fa-cloud"></i></span>');
			if(!((DirList.length == 1) && (DirList[0] == ""))){				
				for(let i=0;i<DirList.length;i++) {
					$("#path-bar").append('<span class="path-separator">&gt;</span><span class="path-location">' + DirList[i] + '</span>');
				}	
			}

		})
		.fail(function() {
			$("#errors").html("<i class='fas fa-exclamation-circle'></i> Couldn't access that folder!");
		});
	}
	
	export function NewFolder() {

		let dd = GetDiscData();

		$.ajax( {
			url: "operations/new",
			data: {discid: dd.discid, cd: dd.cd, permid: dd.permid},
			cache: false,
			type: 'post'
		})
		.done(function() {
			ReadCurrentDirectory();
		})
		.fail(function(jqXHR) {
			$("#errors").html("<i class='fas fa-exclamation-circle'></i> Couldn't create the folder! " + jqXHR.responseText);
		});
	}

	export function NewFile() {

		let dd = GetDiscData();

		$.ajax( {
			url: "operations/newfile",
			data: {discid: dd.discid, cd: dd.cd, permid: dd.permid},
			cache: false,
			type: 'post'
		})
		.done(function() {
			ReadCurrentDirectory();
		})
		.fail(function(jqXHR) {
			$("#errors").html("<i class='fas fa-exclamation-circle'></i> Couldn't create the file! " + jqXHR.responseText);
		});
	}
	
	export function ReadCurrentDirectory() {
		$("#file-listing").empty();

		let dd = GetDiscData();

		$.ajax({
			url: 'operations/read', 
			data: {discid: dd.discid, cd: dd.cd, permid: dd.permid},
			dataType: 'json',
			cache: false,
			type: 'post'
		})
		.done( function(JSONResp){
			if(JSONResp.error) return false;

			let FileDOM;
			JSONResp.forEach(function(file) {
				if((file.name == "_dummy_") && (file.id == 0)) {
					$("#file-listing").append("<span class='msg'>Folder is empty</span>");
				} else {
					
					FileDOM = "<div class='f noselect " + ((file.isDir!=false)?"dir":"") + "' data-id='"+file.id+"'>";
					if(file.isDir != false) 
						FileDOM += "<svg class='svg-icon' viewBox='0 0 20 20' style='width: 1em; height: 1em;'><path d='M17.927,5.828h-4.41l-1.929-1.961c-0.078-0.079-0.186-0.125-0.297-0.125H4.159c-0.229,0-0.417,0.188-0.417,0.417v1.669H2.073c-0.229,0-0.417,0.188-0.417,0.417v9.596c0,0.229,0.188,0.417,0.417,0.417h15.854c0.229,0,0.417-0.188,0.417-0.417V6.245C18.344,6.016,18.156,5.828,17.927,5.828 M4.577,4.577h6.539l1.231,1.251h-7.77V4.577z M17.51,15.424H2.491V6.663H17.51V15.424z' style='fill: #e0b85b;'></path></svg>";
					else
						FileDOM += "<svg class='svg-icon' viewBox='0 0 20 20' style='width: 1em; height: 1em;'><path d='M15.475,6.692l-4.084-4.083C11.32,2.538,11.223,2.5,11.125,2.5h-6c-0.413,0-0.75,0.337-0.75,0.75v13.5c0,0.412,0.337,0.75,0.75,0.75h9.75c0.412,0,0.75-0.338,0.75-0.75V6.94C15.609,6.839,15.554,6.771,15.475,6.692 M11.5,3.779l2.843,2.846H11.5V3.779z M14.875,16.75h-9.75V3.25h5.625V7c0,0.206,0.168,0.375,0.375,0.375h3.75V16.75z' style='fill: #0869ff;'></path></svg>";
					
					FileDOM += "<span>" + file.name + "</span></div>";
					$("#file-listing").append(FileDOM);
				}
			});

			if(dd.cd != 0)
				ShowCDInfo();
			else {
				ShowDiskInfo();
			}
		})
		.fail( function() {
			$("#errors").html("<i class='fas fa-exclamation-circle'></i> Couldn't read the folder!");
		});
	}
	
	export function Select(FileDOM) {
		if( $(".rename-input").length > 0) {
			// Don't select if there is a renaming in proccess
			return false;
		}

		DeselectAll();
		Status.targetFile = FileDOM;
		Status.targetFilename = FileDOM.find("span").html();
		FileDOM.addClass("selected");

		ShowFileInfo();
	}

	
	
	export function DeselectAll() {
		if($(".rename-input").length>0) {
			// Don't deselect if there is a renaming in proccess
			return false;
		}

		Status.targetFile = null;
		Status.targetFilename = "";
		$(".f").removeClass("selected");

		if($("#dinfo").data("cd") != 0) {
			ShowCDInfo();
		} else {
			ShowDiskInfo();
		}
		
	}
	
	export function Download(Id) {
		let dd = GetDiscData();

		$.ajax({
			url: 'operations/download', 
			cache: false,
			method: 'get',
			data: { fid: Id, permid: dd.permid }
		})
		.done(function() {
			$("body").append("<iframe src='operations/download?fid=" + Id + "&permid=" + dd.permid + "' style='display: none;' ></iframe>");
			setTimeout(function() {
				$("iframe").remove();
			}, 3000);
		});
	}


	
	export function Rename(Id, NewName) {

		let dd = GetDiscData();

		$.ajax({
			url: 'operations/rename', 
			data: { fid: Id, fn: NewName, discid: dd.discid, permid: dd.permid },
			dataType: 'json',
			cache: false,
			type: 'post'
		})
		.done(function() {
			$("#errors").html("");
		})
		.fail(function(res) {
			Status.targetFile.children("span").html(res.responseJSON.old_fn);
			$("#errors").html("<i class='fas fa-exclamation-triangle'></i> Couldn't rename the file!");
		});

		Status.targetFile.css("width", "90px");
	}
	
	export function Trash(Id) {

		let dd = GetDiscData();

		$.ajax({
			url: 'operations/delete', 
			data: { fid: Id, permid: dd.permid },
			dataType: 'json',
			cache: false,
			type: 'post'
		})
		.done(function() {
			ReadCurrentDirectory();
		})
		.fail(function() {
			$("#errors").html("<i class='fas fa-exclamation-triangle'></i> Couldn't delete the file!");
		});
	}
