import { Status, GetDiscData } from './states.js';

	export function ShowDiskSpace() {

		let dd = GetDiscData();

		$.ajax({
			url: "sys/api/properties",
			cache: false,
			method: "get",
			data: {cdid: "0", h: dd.permid},
			dataType: "json"
		})
		.done(function(res) {
			$("#memory #n").html("Free: " + res['freespace'][0] + " "+ res['freespace'][1] + " of " + res['maxspace'][0] + " " + res['maxspace'][1]);
		})
		.fail(function() {
			$("#memory #n").html("Couldn't retrieve storage space information!");
		});
	}

	export function ShowCDInfo() {
		let t = "Directory";
		let cdid = $('#dinfo').data("cd");
		let fn = ""

		$("#fileicon").html("<i class='fas fa-folder'></i>");
		ShowFolderItems(cdid);

		$("#fileicon").css("display", "inline-block");
		$("#filename").html(fn);
		$("#filetype").html(t);
		
		$("#info-bar .disc-info").hide();
		$("#info-bar .file-info").show();
	}

	export function ExtractFileMeta(Filename) {
		let FileExt = Filename.split(".");
		let FileIcon;

		FileExt = (FileExt.length==1)?null:FileExt[FileExt.length-1];
		FileIcon = "<svg class='svg-icon' viewBox='0 0 20 20' style='width: 1em; height: 1em;'><path d='M15.475,6.692l-4.084-4.083C11.32,2.538,11.223,2.5,11.125,2.5h-6c-0.413,0-0.75,0.337-0.75,0.75v13.5c0,0.412,0.337,0.75,0.75,0.75h9.75c0.412,0,0.75-0.338,0.75-0.75V6.94C15.609,6.839,15.554,6.771,15.475,6.692 M11.5,3.779l2.843,2.846H11.5V3.779z M14.875,16.75h-9.75V3.25h5.625V7c0,0.206,0.168,0.375,0.375,0.375h3.75V16.75z' style='fill: #0869ff;'></path></svg> ";
		
		var result = {
			fn : Filename.replace(FileExt, ""),
			ext : FileExt,
			icon : FileIcon
		};

		return result;
	}

	export function ShowFileInfo() {
		let t;
		let fn = Status.targetFile.find("p").html();

		if(Status.targetFile.hasClass("dir")) {
			t = "Directory";
			$("#fileicon").html("<i class='fas fa-folder'></i>");
			ShowFolderItems();
		} else {
			var fx = ExtractFileMeta(fn);
			if(fx['ext']!=null) {
				var ext = fx['ext'];
				t = ext.toUpperCase() + " File";
			} else {
				t = "File";
			}

			$("#fileicon").html(fx['icon']);
			ShowFileSize();
		}

		$("#fileicon").css("display", "inline-block");
		$("#filename").html(fn);
		$("#filetype").html(t);
		
		$("#info-bar .disc-info").hide();
		$("#info-bar .file-info").show();
	}

	export function ShowDiskInfo() {
		$("#fileicon").hide();
		$("#filename").html("");
		$("#filetype").html("");
		$("#filesize").html("");

		ShowDiskSpace();
		
		$("#info-bar .disc-info").show();
		$("#info-bar .file-info").hide();
	}

	export function ShowFileSize() {
		if(Status.targetFile == null) return -1;
		if(Status.targetFile.hasClass("dir")) return -1; //Size is not calculated for directories

		let fid = Status.targetFile.data("id");
		let hdl = $("#dinfo").data("hdl");

		$.getJSON("sys/api/properties", {'fid': fid, 'h' : hdl}, function (res) {
			$("#filesize").html("Size: " + res.size + " " + res.unit);
			return res.size;
		});
	}

	export function ShowFolderItems(folderid = -1) {
		
		let fid;
		if(folderid == -1) {
			if(Status.targetFile == null) return -1;
			if(!Status.targetFile.hasClass("dir")) return -1; //Items count is not calculated for files
			fid = Status.targetFile.data("id");
		} else {
			fid = folderid;
		}
			
		let hdl = $("#dinfo").data("hdl");

		$.getJSON("sys/api/properties", {'fid': fid, 'h' : hdl}, function (res) {
			$("#filesize").html(res.filecount + " item(s)");
			return res.fileCount;
		});
	}

