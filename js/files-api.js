import {Status, ClipboardStatus} from './states.js';
import {Properties} from './properties-api.js';

export const Files = {
	Paste : function() {
		
		if (ClipboardStatus.file == null) return;

		let fid = ClipboardStatus.file;
		let dest_id = $('#dinfo').data("cd");
		let perm = $('#dinfo').data("hdl");
		let transf_op = (ClipboardStatus.cut)?2:1;

		$.ajax( {
			url: "operations/transfer",
			data: {source_fid: fid, destination_folder: dest_id, transfer_op: transf_op, permid: perm},
			cache: false,
			type: 'post'
		})
		.done(function() {
			Files.Read();
		})
		.fail(function(jqXHR) {
			$("#errors").html("<i class='fas fa-exclamation-circle'></i> Can't move/copy here. " + jqXHR.responseText);
		});
	},

	Change: function($id) {
		if($(".rename-input").length>0) {
			//Don't change path if renaming proccess is active
			return false;
		}

		let perm = $('#dinfo').data("hdl");

		$.ajax({
			url: 'operations/cd', 
			cache: false,         
			method: 'post',
			dataType: 'json',
			data: { fid: $id, permid: perm }
		})
		.done(function(resp) {
			$('#dinfo').data('cd', $id);
			Files.Read();

			//Write the current path on the bar
			let $dir_list = resp["path"].split("/");
			$("#path-bar").html('<span id="root-location"><i class="fas fa-cloud"></i></span>');
			if(!(($dir_list.length == 1) && ($dir_list[0] == ""))){				
				for(var i=0;i<$dir_list.length;i++) {
					$("#path-bar").append('<span class="path-separator">&gt;</span><span class="path-location">' + $dir_list[i] + '</span>');
				}	
			}

		})
		.fail(function(resp) {
			$("#errors").html("<i class='fas fa-exclamation-circle'></i> Couldn't access the folder!");
		});
	},
	
	Newdir : function() {
		var did = $('#dinfo').data("did");
		var cdid = $('#dinfo').data("cd");
		let perm = $('#dinfo').data("hdl");
		$.ajax( {
			url: "operations/new",
			data: {discid: did, cd: cdid, permid: perm},
			cache: false,
			type: 'post'
		})
		.done(function() {
			Files.Read();
		})
		.fail(function(jqXHR) {
			$("#errors").html("<i class='fas fa-exclamation-circle'></i> Couldn't create the folder! " + jqXHR.responseText);
		});
	},

	Newfile : function() {
		var did = $('#dinfo').data("did");
		var cdid = $('#dinfo').data("cd");
		let perm = $('#dinfo').data("hdl");

		$.ajax( {
			url: "operations/newfile",
			data: {discid: did, cd: cdid, permid: perm},
			cache: false,
			type: 'post'
		})
		.done(function() {
			Files.Read();
		})
		.fail(function(jqXHR) {
			$("#errors").html("<i class='fas fa-exclamation-circle'></i> Couldn't create the file! " + jqXHR.responseText);
		});
	},
	
	Read : function() {
		$("#file-listing").empty();
		var did = $('#dinfo').data("did");
		var cdid = $('#dinfo').data("cd");
		let perm = $('#dinfo').data("hdl");

		var filesAjax = $.ajax({
			url: 'operations/read', 
			data: {discid: did, cd: cdid, permid: perm},
			dataType: 'json',
			cache: false,
			type: 'post'
		})
		.done( function(res){
			if(res.error) return false;

			var afile;
			res.forEach(function(elem) {
				if((elem['name'] == "_dummy_") && (elem['id'] == 0)) {
					$("#file-listing").append("<span class='msg'>Folder is empty</span>");
				} else {
					
					afile = "<div class='f noselect " + ((elem['isDir']!=false)?"dir":"") + "' data-id='"+elem['id']+"'>";
					if(elem['isDir']!=false) 
						afile += "<svg class='svg-icon' viewBox='0 0 20 20' style='width: 1em; height: 1em;'><path d='M17.927,5.828h-4.41l-1.929-1.961c-0.078-0.079-0.186-0.125-0.297-0.125H4.159c-0.229,0-0.417,0.188-0.417,0.417v1.669H2.073c-0.229,0-0.417,0.188-0.417,0.417v9.596c0,0.229,0.188,0.417,0.417,0.417h15.854c0.229,0,0.417-0.188,0.417-0.417V6.245C18.344,6.016,18.156,5.828,17.927,5.828 M4.577,4.577h6.539l1.231,1.251h-7.77V4.577z M17.51,15.424H2.491V6.663H17.51V15.424z' style='fill: #e0b85b;'></path></svg>";
					else
						afile += "<svg class='svg-icon' viewBox='0 0 20 20' style='width: 1em; height: 1em;'><path d='M15.475,6.692l-4.084-4.083C11.32,2.538,11.223,2.5,11.125,2.5h-6c-0.413,0-0.75,0.337-0.75,0.75v13.5c0,0.412,0.337,0.75,0.75,0.75h9.75c0.412,0,0.75-0.338,0.75-0.75V6.94C15.609,6.839,15.554,6.771,15.475,6.692 M11.5,3.779l2.843,2.846H11.5V3.779z M14.875,16.75h-9.75V3.25h5.625V7c0,0.206,0.168,0.375,0.375,0.375h3.75V16.75z' style='fill: #0869ff;'></path></svg>";
					
						afile += "<span>" + elem['name'] + "</span>";
					afile += "</div>";
					$("#file-listing").append(afile);
				}
			});

			if(cdid != 0)
				Properties.ShowCDInfo();
			else {
				Properties.HideFileInfo();
				Properties.ShowDiskSpace();
			}
		})
		.fail( function() {
			$("#errors").html("<i class='fas fa-exclamation-circle'></i> Couldn't read the folder!");
		});
	},
	
	Select : function(fileElement) {
		if( $(".rename-input").length > 0) {
			// Don't select if there is a renaming in proccess
			return false;
		}

		Files.Deselect();
		Status.targetFile = fileElement;
		Status.targetFilename = fileElement.find("span").html();
		fileElement.addClass("selected");

		Properties.ShowFileInfo();
	},

	Upload : function(files) {
		var fd = new FormData();
		
		const json = JSON.stringify({
			discid: $("#dinfo").data("did"),
			cd: $("#dinfo").data("cd"),
			permid: $("#dinfo").data("hdl")
		});
		const blob = new Blob([json], {
			type: 'application/json'
		});

		fd.append(UploadProgressTrackId, "123"); //For upload progress tracking
		fd.append("discdata", blob); //Required data (Disc ID and Current Directory Id)

		$.each(files, function(ind, val) {
			fd.append("file" + ind, val);
		});
		
		$.ajax({
			url: 'operations/upload', 
			dataType: 'json',
			cache: false,
			contentType: false,
			processData: false,
			data: fd,                        
			type: 'post'
		})
		.done(function(resp){
			$("#errors").html("");
			Files.Read();
			Properties.ShowDiskSpace();

		})
		.fail(function(resp){
			$("#errors").html(resp.responseJSON.error);
		});
	},
	
	Deselect : function() {
		if($(".rename-input").length>0) {
			// Don't deselect if there is a renaming in proccess
			return false;
		}

		Status.targetFile = null;
		Status.targetFilename = "";
		$(".f").removeClass("selected");

		if($("#dinfo").data("cd") != 0) {
			Properties.HideFileInfo();
			Properties.ShowCDInfo();
		} else {
			Properties.HideFileInfo();
		}
		
	},
	
	Download : function(fid) {
		let perm = $('#dinfo').data("hdl");

		$.ajax({
			url: 'operations/download', 
			cache: false,
			method: 'get',
			data: { fid: fid, permid: perm }
		})
		.done(function(resp) {
			$("body").append("<iframe src='operations/download?fid=" + fid + "&permid=" + perm + "' style='display: none;' ></iframe>");
			setTimeout(function() {
				$("iframe").remove();
			}, 3000);
		});
	},

	GetFileExtension : function(filename) {
		var fileext = filename.split(".");
		var icn;

		if(fileext.length==1) fileext = null;
		else fileext = fileext[fileext.length-1];

		if(fileext == "png" || fileext =="jpg" || fileext =="jpeg" || fileext=="gif") {
			icn = "<i class='fas fa-file-image'></i> ";
		} else if(fileext == "wav" || fileext =="mp3") {
			icn = "<i class='fas fa-file-audio'></i> ";
		} else if(fileext == "mp4" || fileext =="avi" || fileext=="mov" || fileext=="wmv") {
			icn = "<i class='fas fa-file-video'></i>";
		} else if(fileext == "zip" || fileext =="rar" || fileext=="7z") {
			icn = "<i class='fas fa-file-archive'></i> ";
		} else {
			icn = "<svg class='svg-icon' viewBox='0 0 20 20' style='width: 1em; height: 1em;'><path d='M15.475,6.692l-4.084-4.083C11.32,2.538,11.223,2.5,11.125,2.5h-6c-0.413,0-0.75,0.337-0.75,0.75v13.5c0,0.412,0.337,0.75,0.75,0.75h9.75c0.412,0,0.75-0.338,0.75-0.75V6.94C15.609,6.839,15.554,6.771,15.475,6.692 M11.5,3.779l2.843,2.846H11.5V3.779z M14.875,16.75h-9.75V3.25h5.625V7c0,0.206,0.168,0.375,0.375,0.375h3.75V16.75z' style='fill: #0869ff;'></path></svg> ";
		}

		var result = {
			fn : filename.replace(fileext, ""),
			ext : fileext,
			icon : icn
		};

		return result;
	},
	
	Rename: function(fid, newname, did) {
		let perm = $('#dinfo').data("hdl");

		$.ajax({
			url: 'operations/rename', 
			data: { fid: fid, fn: newname, discid: did, permid: perm  },
			dataType: 'json',
			cache: false,
			type: 'post'
		})
		.done(function(res) {
			if(res.result == false) {
				$("#errors").html("<i class='fas fa-exclamation-triangle'></i> Couldn't rename the file!");
				Status.targetFile.children("span").html(res.oldfn);
			}
		});

		Status.targetFile.css("width", "90px");
	},
	
	Trash: function(fid) {
		let perm = $('#dinfo').data("hdl");
		$.ajax({
			url: 'operations/delete', 
			data: { fid: fid, permid: perm },
			dataType: 'json',
			cache: false,
			type: 'post'
		})
		.done(function() {
			Files.Read();
		})
		.fail(function() {
			$("#errors").html("<i class='fas fa-exclamation-triangle'></i> Couldn't delete the file!");
		});
	}
};