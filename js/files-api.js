var Files = {
	Paste : function() {
		
		if (ClipboardStatus.file == null) return;
		let fid = ClipboardStatus.file;
		let dest_id = $('#dinfo').data("cd");
		let transf_op = (ClipboardStatus.cut)?2:1;

		$.ajax( {
			url: "operations/transfer",
			data: {source_fid: fid, destination_folder: dest_id, transfer_op: transf_op},
			cache: false,
			type: 'post'
		})
		.done(function() {
			Files.Read();
		})
		.fail(function(jqXHR) {
			$("#errors").html("<i class='fas fa-exclamation-circle'></i> Nu se poate muta/copia aici. " + jqXHR.responseText);
		});
	},

	Change: function($id) {
		if($(".rename-input").length>0) {
			//Don't change path if renaming proccess is active
			return false;
		}

		$.ajax({
			url: 'operations/cd', 
			cache: false,         
			method: 'post',
			dataType: 'json',
			data: { fid: $id }
		})
		.done(function(resp) {
			$('#dinfo').data('cd', $id);
			Files.Read();

			//Write the current path on the bar
			$dir_list = resp["path"].split("/");
			$("#path-bar").html('<span id="root-location"><i class="fas fa-cloud"></i></span>');
			if(!(($dir_list.length == 1) && ($dir_list[0] == ""))){				
				for(var i=0;i<$dir_list.length;i++) {
					$("#path-bar").append('<span class="path-separator">&gt;</span><span class="path-location">' + $dir_list[i] + '</span>');
				}	
			}

		})
		.fail(function(resp) {
			$("#errors").html("<i class='fas fa-exclamation-circle'></i> Folderul nu poate fi accesat!");
		});
	},
	
	Newdir : function() {
		var did = $('#dinfo').data("did");
		var cdid = $('#dinfo').data("cd");
		$.ajax( {
			url: "operations/new",
			data: {discid: did, cd: cdid},
			cache: false,
			type: 'post'
		})
		.done(function() {
			Files.Read();
		})
		.fail(function(jqXHR) {
			$("#errors").html("<i class='fas fa-exclamation-circle'></i> Folderul nu poate fi creat aici. " + jqXHR.responseText);
		});
	},

	Newfile : function() {
		var did = $('#dinfo').data("did");
		var cdid = $('#dinfo').data("cd");
		$.ajax( {
			url: "operations/newfile",
			data: {discid: did, cd: cdid},
			cache: false,
			type: 'post'
		})
		.done(function() {
			Files.Read();
		})
		.fail(function(jqXHR) {
			$("#errors").html("<i class='fas fa-exclamation-circle'></i> Fisierul nu poate fi creat aici. " + jqXHR.responseText);
		});
	},
	
	Read : function() {
		$("#file-listing").empty();
		var did = $('#dinfo').data("did");
		var cdid = $('#dinfo').data("cd");

		var filesAjax = $.ajax({
			url: 'operations/read', 
			data: {discid: did, cd: cdid},
			dataType: 'json',
			cache: false,
			type: 'post'
		})
		.done( function(res){
			if(res.error) return false;

			var afile;
			res.forEach(function(elem) {
				if((elem['name'] == "_dummy_") && (elem['id'] == 0)) {
					$("#file-listing").append("<span class='msg'>Niciun fisier aici</span>");
				} else {
					
					afile = "<div class='f noselect " + ((elem['isDir']!=false)?"dir":"") + "' data-id='"+elem['id']+"'>";
					if(elem['isDir']!=false) 
						afile += "<i class='fas fa-folder'></i>";
					else
						afile += "<i class='fas fa-file'></i>";
					afile += "<span>" + elem['name'] + "</span>";
					afile += "</div>";
					$("#file-listing").append(afile);
				}
			});
		})
		.fail( function() {
			$("#errors").html("<i class='fas fa-exclamation-circle'></i> Nu s-au putut citi fisierele!");
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

		Properties.ShowInfo();
	},

	Upload : function(files) {
		var fd = new FormData();
		
		const json = JSON.stringify({
			discid: $("#dinfo").data("did"),
			cd: $("#dinfo").data("cd")
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
			Properties.GetDiskSpace();

		})
		.fail(function(resp){
			$("#errors").html(resp.error);
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

		Properties.HideInfo();
	},
	
	Download : function(fid) {
		$.ajax({
			url: 'operations/download', 
			cache: false,
			method: 'get',
			data: { fid: fid }
		})
		.done(function(resp) {
			$("body").append("<iframe src='operations/download?fid=" + fid + "' style='display: none;' ></iframe>");
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
		} else if(fileext == "txt" || fileext =="doc") {
			icn = "<i class='fas fa-file-alt'></i> ";
		} else {
			icn = "<i class='fas fa-file'></i> ";
		}

		var result = {
			fn : filename.replace(fileext, ""),
			ext : fileext,
			icon : icn
		};

		return result;
	},
	
	Rename: function(fid, newname, did) {
		$.ajax({
			url: 'operations/rename', 
			data: { fid: fid, fn: newname, discid: did  },
			dataType: 'json',
			cache: false,
			type: 'post'
		})
		.done(function(res) {
			if(res.result == false) {
				$("#errors").html("<i class='fas fa-exclamation-triangle'></i> Fisierul nu a putut fi redenumit.");
				Status.targetFile.children("span").html(res.oldfn);
			}
		});

		Status.targetFile.css("width", "90px");
	},
	
	Trash: function(fid) {
		$.ajax({
			url: 'operations/delete', 
			data: { fid: fid, totrash: 1 },
			dataType: 'json',
			cache: false,
			type: 'post'
		})
		.done(function() {
			Files.Read();
		});
	},
	
	Restore: function(fid) {
		$.ajax({
			url: 'operations/delete', 
			data: { fid: fid, restore: 1 },
			dataType: 'json',
			cache: false,
			type: 'post'
		})
		.done(function() {
			Locations.List.Trash();
		});
	},
	
	Delete: function(fid, preventRefresh = false) {
		$.ajax({
			url: 'operations/delete', 
			data: { fid: fid },
			dataType: 'json',
			cache: false,
			type: 'post'
		})
		.done(function(res) {
			if(res.result == true) {
				if(!preventRefresh) {
					Locations.List.Trash();
					Properties.GetDiskSpace();
				}
			} else {
				$("#errors").html("<i class='fas fa-exclamation-triangle'></i> Fisierul nu a putut fi sters.");
			}
		});
	}
};