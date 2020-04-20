import { Files } from './files-api.js';

import { Status } from './states.js';

export const Properties = {

	ShowDiskSpace: function() {
		let m = "";
		let hdl = $("#dinfo").data("hdl");

		$.ajax({
			url: "operations/properties",
			cache: false,
			method: "get",
			data: {cdid: "0", h: hdl},
			dataType: "json"
		})
		.done(function(res) {
			m = "Liber: " + res['freespace'][0] + " "+ res['freespace'][1] + " din " + res['maxspace'][0] + " " + res['maxspace'][1];
			$("#memory #n").html(m);
		});
	},

	ShowCDInfo: function() {
		let t = "Directory";
		let cdid = $('#dinfo').data("cd");
		let fn = ""

		$("#fileicon").html("<i class='fas fa-folder'></i>");
		Properties.ShowFolderItems(cdid);

		$("#fileicon").css("display", "inline-block");
		$("#filename").html(fn);
		$("#filetype").html(t);
		
		$("#info-bar .disc-info").hide();
		$("#info-bar .file-info").show();
	},

	ShowFileInfo: function() {
		let t;
		let fn = Status.targetFile.children("span").html();

		if(Status.targetFile.hasClass("dir")) {
			t = "Directory";
			$("#fileicon").html("<svg class='svg-icon' viewBox='0 0 20 20' style='width: 1em; height: 1em;'><path d='M17.927,5.828h-4.41l-1.929-1.961c-0.078-0.079-0.186-0.125-0.297-0.125H4.159c-0.229,0-0.417,0.188-0.417,0.417v1.669H2.073c-0.229,0-0.417,0.188-0.417,0.417v9.596c0,0.229,0.188,0.417,0.417,0.417h15.854c0.229,0,0.417-0.188,0.417-0.417V6.245C18.344,6.016,18.156,5.828,17.927,5.828 M4.577,4.577h6.539l1.231,1.251h-7.77V4.577z M17.51,15.424H2.491V6.663H17.51V15.424z' style='fill: #e0b85b;'></path></svg>");
			Properties.ShowFolderItems();
		} else {
			var fx = Files.GetFileExtension(fn);
			if(fx['ext']!=null) {
				var ext = fx['ext'];
				t = ext.toUpperCase() + " File";
			} else {
				t = "File";
			}

			$("#fileicon").html(fx['icon']);
			Properties.ShowFileSize();
		}

		$("#fileicon").css("display", "inline-block");
		$("#filename").html(fn);
		$("#filetype").html(t);
		
		$("#info-bar .disc-info").hide();
		$("#info-bar .file-info").show();
	},

	HideFileInfo: function() {
		$("#fileicon").hide();
		$("#filename").html("");
		$("#filetype").html("");
		$("#filesize").html("");
		
		$("#info-bar .disc-info").show();
		$("#info-bar .file-info").hide();
	},

	ShowFileSize: function() {
		if(Status.targetFile == null) return -1;
		if(Status.targetFile.hasClass("dir")) return -1; //Size is not calculated for directories

		let fid = Status.targetFile.data("id");
		let hdl = $("#dinfo").data("hdl");

		$.getJSON("operations/properties", {'fid': fid, 'h' : hdl}, function (res) {
			$("#filesize").html("Size: " + res.size + " " + res.unit);
			return res.size;
		});
	},

	ShowFolderItems: function(folderid = -1) {
		
		let fid;
		if(folderid == -1) {
			if(Status.targetFile == null) return -1;
			if(!Status.targetFile.hasClass("dir")) return -1; //Items count is not calculated for files
			fid = Status.targetFile.data("id");
		} else {
			fid = folderid;
		}
			
		let hdl = $("#dinfo").data("hdl");

		$.getJSON("operations/properties", {'fid': fid, 'h' : hdl}, function (res) {
			$("#filesize").html(res.filecount + " item(s)");
			return res.fileCount;
		});
	}
};
