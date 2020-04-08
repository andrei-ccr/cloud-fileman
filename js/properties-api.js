import { Files } from './files-api.js';

import { Status } from './states.js';

export const Properties = {

	ShowDiskSpace: function() {
		let m = "";

		$.ajax({
			url: "operations/properties",
			cache: false,
			method: "post",
			data: {cdid: "0"},
			dataType: "json"
		})
		.done(function(res) {
			m = res['freespace'][0] + res['freespace'][1] + " / " + res['maxspace'][0] + res['maxspace'][1];
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
			$("#fileicon").html("<i class='fas fa-folder'></i>");
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
