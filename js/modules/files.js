import {Status, ClipboardStatus, GetDiscData} from './states.js';
import {aGetFileInfo, UpdateDiskSpace} from './properties.js';
import {ShowMessage} from './modals.js';

	export function SetColor(Ids, ColorCode) {
		let dd = GetDiscData();

		Ids.forEach(function(Id, index) {
			$.ajax({
				url: 'sys/api/setcolor', 
				data: { fid: Id, color: ColorCode, discid: dd.discid, permid: dd.permid },
				dataType: 'json',
				cache: false,
				type: 'post'
			})
			.done(function() {
				if(index == Ids.length-1)
					ReadCurrentDirectory();
			})
			.fail(function(res) {
				ShowMessage("<i class='fas fa-exclamation-triangle'></i> Couldn't change the file's color!");
			});
		});

	}

	export function PasteFile() {
		
		if (ClipboardStatus.file == null) return;

		let fid = ClipboardStatus.file;
		let transf_op = (ClipboardStatus.cut)?2:1;

		let dd = GetDiscData();

		$.ajax( {
			url: "sys/api/transfer",
			data: {source_fid: fid, destination_folder: dd.cd, transfer_op: transf_op, permid: dd.permid},
			cache: false,
			type: 'post'
		})
		.done(function() {
			ReadCurrentDirectory();
		})
		.fail(function(jqXHR) {
			ShowMessage("<i class='fas fa-exclamation-circle'></i> Can't move/copy here. " + jqXHR.responseText);
		});
	}

	export function ChangeCurrentDirectory(Id) {

		if($(".rename-input").length > 0) {
			//Don't change path while renaming
			return false;
		}

		let dd = GetDiscData();

		$.ajax({
			url: 'sys/api/cd', 
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
			$("#path-bar").html('<span id="root-location">Home</span>');
			if(!((DirList.length == 1) && (DirList[0] == ""))){				
				for(let i=0;i<DirList.length;i++) {
					$("#path-bar").append('<span class="path-separator">&#10151;</span><span class="path-location">' + DirList[i] + '</span>');
				}	
			}

		})
		.fail(function() {
			ShowMessage("<i class='fas fa-exclamation-circle'></i> Couldn't access the folder!");
		});
	}
	
	export function NewFolder() {

		let dd = GetDiscData();

		$.ajax( {
			url: "sys/api/new",
			data: {discid: dd.discid, cd: dd.cd, permid: dd.permid},
			cache: false,
			type: 'post'
		})
		.done(function() {
			ReadCurrentDirectory();
		})
		.fail(function(jqXHR) {
			ShowMessage("<i class='fas fa-exclamation-circle'></i> Couldn't create a new folder! " + jqXHR.responseText);
		});
	}

	export function NewFile() {

		let dd = GetDiscData();

		$.ajax( {
			url: "sys/api/newfile",
			data: {discid: dd.discid, cd: dd.cd, permid: dd.permid},
			cache: false,
			type: 'post'
		})
		.done(function() {
			ReadCurrentDirectory();
		})
		.fail(function(jqXHR) {
			ShowMessage("<i class='fas fa-exclamation-circle'></i> Couldn't create the file! " + jqXHR.responseText);
		});
	}

	export function ReadLocation(Loc) {
		$("#file-listing").html(`
			<div id="path-bar">
				
			</div>
			<div id="details-header"><span>Filename</span><span>Last modified</span><span>Size</span></div>
		`);

		let dd = GetDiscData();
		let FileExt = "";

		if(Loc == 1) {

		}
	}
	
	function GetFileExtensionFromStr(str) {
		if(str.lastIndexOf(".") != -1) {
			return str.substring(str.lastIndexOf(".") + 1, str.length);
		} else {
			return "";
		}
	}

	export function ReadCurrentDirectory() {
		$("#file-listing").html(`
			<div id="path-bar">
				<span id="root-location">Home</span>
			</div>
			<div id="details-header"><span>Filename</span><span>Last modified</span><span>Size</span></div>
		`);

		let dd = GetDiscData();
		let FileExt = "";

		$.ajax({
			url: 'sys/api/read', 
			data: {discid: dd.discid, cd: dd.cd, permid: dd.permid},
			dataType: 'json',
			cache: false,
			type: 'post'
		})
		.done( function(JSONResp){
			if(JSONResp.error) return false;

			let GetFileInfoRequests = [];

			JSONResp.forEach(function(file) {
				if((file.name == "_dummy_") && (file.id == 0)) {
					$("#file-listing").append("<span class='msg'>This folder is empty. Upload or create a new file</span>");
				} else {
					GetFileInfoRequests.push(aGetFileInfo(file.id));
				}
			});

			if(GetFileInfoRequests.length > 0) {
				$.when.apply(undefined, GetFileInfoRequests).then(function() {

					let ArrayFolderDOM = [""], ArrayFileDOM = [""];
					let FileDOM;

					$.each(arguments, function(index, fInfoResp) {

						JSONResp.forEach(function(file, ind) {
							if(ind != index) return;

							file.color = (file.color==null)?"#555":file.color;
							
							FileExt = GetFileExtensionFromStr(file.name);
							
							FileDOM = "<div class='f noselect " + ((file.isDir!=false)?"dir":"") + "' data-id='"+file.id+"'><div style='display: flex;'>";

							if(GetFileInfoRequests.length == 1) {
								if(file.isDir != false) {
									FileDOM += "<svg class='svg-icon' viewBox='0 0 20 20' style='width: 1em; height: 1em;'><path d='M17.927,5.828h-4.41l-1.929-1.961c-0.078-0.079-0.186-0.125-0.297-0.125H4.159c-0.229,0-0.417,0.188-0.417,0.417v1.669H2.073c-0.229,0-0.417,0.188-0.417,0.417v9.596c0,0.229,0.188,0.417,0.417,0.417h15.854c0.229,0,0.417-0.188,0.417-0.417V6.245C18.344,6.016,18.156,5.828,17.927,5.828 M4.577,4.577h6.539l1.231,1.251h-7.77V4.577z M17.51,15.424H2.491V6.663H17.51V15.424z' style='fill: " + file.color + ";'></path></svg>";
									FileDOM += "<p class='filename'>" + file.name + "</p></div><span>" + fInfoResp['modified'] + "</span><span>-</span></div>";
									ArrayFolderDOM.push(FileDOM);
								}
								else {
									FileDOM += "<svg class='svg-icon' viewBox='0 0 20 20' style='width: 1em; height: 1em;'><text style='font-size:0.5rem; fill: "+file.color+";' y='15' x='7'>"+ FileExt +"</text><path d='M15.475,6.692l-4.084-4.083C11.32,2.538,11.223,2.5,11.125,2.5h-6c-0.413,0-0.75,0.337-0.75,0.75v13.5c0,0.412,0.337,0.75,0.75,0.75h9.75c0.412,0,0.75-0.338,0.75-0.75V6.94C15.609,6.839,15.554,6.771,15.475,6.692 M11.5,3.779l2.843,2.846H11.5V3.779z M14.875,16.75h-9.75V3.25h5.625V7c0,0.206,0.168,0.375,0.375,0.375h3.75V16.75z' style='fill: " + file.color + ";'></path></svg>";
									FileDOM += "<p class='filename'>" + file.name + "</p></div><span>" + fInfoResp['modified'] + "</span><span>" + fInfoResp['size'] + " " + fInfoResp['unit'] +"</span></div>";
									ArrayFileDOM.push(FileDOM);
								}
							} else {
								if(file.isDir != false) {
									FileDOM += "<svg class='svg-icon' viewBox='0 0 20 20' style='width: 1em; height: 1em;'><path d='M17.927,5.828h-4.41l-1.929-1.961c-0.078-0.079-0.186-0.125-0.297-0.125H4.159c-0.229,0-0.417,0.188-0.417,0.417v1.669H2.073c-0.229,0-0.417,0.188-0.417,0.417v9.596c0,0.229,0.188,0.417,0.417,0.417h15.854c0.229,0,0.417-0.188,0.417-0.417V6.245C18.344,6.016,18.156,5.828,17.927,5.828 M4.577,4.577h6.539l1.231,1.251h-7.77V4.577z M17.51,15.424H2.491V6.663H17.51V15.424z' style='fill: " + file.color + ";'></path></svg>";
									FileDOM += "<p class='filename'>" + file.name + "</p></div><span>" + fInfoResp[0]['modified'] + "</span><span>-</span></div>";
									ArrayFolderDOM.push(FileDOM);
								}
								else {
									FileDOM += "<svg class='svg-icon' viewBox='0 0 20 20' style='width: 1em; height: 1em;'><text style='font-size:0.5rem; fill: "+file.color+";' y='15' x='7'>"+ FileExt +"</text><path d='M15.475,6.692l-4.084-4.083C11.32,2.538,11.223,2.5,11.125,2.5h-6c-0.413,0-0.75,0.337-0.75,0.75v13.5c0,0.412,0.337,0.75,0.75,0.75h9.75c0.412,0,0.75-0.338,0.75-0.75V6.94C15.609,6.839,15.554,6.771,15.475,6.692 M11.5,3.779l2.843,2.846H11.5V3.779z M14.875,16.75h-9.75V3.25h5.625V7c0,0.206,0.168,0.375,0.375,0.375h3.75V16.75z' style='fill: " + file.color + ";'></path></svg>";
									FileDOM += "<p class='filename'>" + file.name + "</p></div><span>" + fInfoResp[0]['modified'] + "</span><span>" + fInfoResp[0]['size'] + " " + fInfoResp[0]['unit'] +"</span></div>";
									ArrayFileDOM.push(FileDOM);
								}
							}
							
						});
					});

					ArrayFolderDOM.forEach(function(val){
						$("#file-listing").append(val);
					});
					ArrayFileDOM.forEach(function(val){
						$("#file-listing").append(val);
					});

				});
			}
			
		})
		.fail( function() {
			ShowMessage("<i class='fas fa-exclamation-circle'></i> Couldn't read the folder!");
		});
	}
	
	export function Select(FileDOM, AllowMultipleSelection=false) {
		if( $(".rename-input").length > 0) {
			// Don't select if there is a renaming in proccess
			return false;
		}

		if(!AllowMultipleSelection)
			DeselectAll();

		Status.selectedFiles.push([FileDOM.data("id"), FileDOM]);
		FileDOM.addClass("selected");

	}

	export function Deselect(FileDOM) {
		
		let i = FileDOM.data("id");
		let x = -1;

		Status.selectedFiles.forEach(function(val, ind) {
			if(val[0] == i) {
				x = ind;
				return;
			}
		});
		Status.selectedFiles.splice(x, 1);
		
		FileDOM.removeClass("selected");

	}
	
	export function DeselectAll() {
		if($(".rename-input").length>0) {
			// Don't deselect if there is a renaming in proccess
			return false;
		}

		Status.selectedFiles = [];
		$(".f").removeClass("selected");
		
	}
	
	export function Download(Id) {
		let dd = GetDiscData();

		$.ajax({
			url: 'sys/api/download', 
			cache: false,
			method: 'get',
			data: { fid: Id, permid: dd.permid }
		})
		.done(function() {
			$("body").append("<iframe src='sys/api/download?fid=" + Id + "&permid=" + dd.permid + "' style='display: none;' ></iframe>");
			setTimeout(function() {
				$("iframe").remove();
			}, 3000);
		});
	}


	
	export function Rename(Id, NewName) {

		let dd = GetDiscData();

		$.ajax({
			url: 'sys/api/rename', 
			data: { fid: Id, fn: NewName, discid: dd.discid, permid: dd.permid },
			dataType: 'json',
			cache: false,
			type: 'post'
		})
		.done(function() {
			
		})
		.fail(function(res) {
			Status.selectedFiles[0][1].children("p").html(res.responseJSON.old_fn);
			ShowMessage("<i class='fas fa-exclamation-triangle'></i> Couldn't rename the file!");
		});

	}
	
	export function Trash(Ids) {

		let dd = GetDiscData();

		Ids.forEach(function(Id, index) {
			
			$.ajax({
				url: 'sys/api/delete',
				data: { fid: Id, permid: dd.permid },
				dataType: 'json',
				cache: false,
				type: 'post'
			})
			.done(function() {
				if(index == (Ids.length - 1)) ReadCurrentDirectory();
				UpdateDiskSpace();
			})
			.fail(function() {
				ShowMessage("<i class='fas fa-exclamation-triangle'></i> Couldn't delete the file!");
			});
		});

		
	}
