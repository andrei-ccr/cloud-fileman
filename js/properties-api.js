import { Files } from './files-api.js';

import { Status, ClipboardStatus } from './states.js';

export const Properties = {
	GetDiskSpace: function() {
		$.ajax({
			url: "operations/properties",
			cache: false,
			method: "post",
			dataType: "json"
		})
		.done(function(res) {
			var $mem = res['freespace'][0] + res['freespace'][1] + " / " + res['maxspace'][0] + res['maxspace'][1];
			$("#memory #n").html($mem);
		});
	},

	ShowInfo: function() {
		var t;
		var fn = Status.targetFile.children("span").html();
		if(Status.targetFile.hasClass("dir")) {
			t="Directory";
			$("#fileicon").html("<i class='fas fa-folder'></i>");
		} else {
			var fx = Files.GetFileExtension(fn);
			if(fx['ext']!=null) {
				var ext = fx['ext'];
				t = ext.toUpperCase() + " File";
			} else {
				t = "File";
			}

			$("#fileicon").html(fx['icon']);
		}

		$("#fileicon").css("display", "inline-block");
		$("#filename").html(fn);
		$("#filetype").html(t);
		if(Status.targetFile.data("marked")>0)
			$("#marked").html("Marked");
		else
			$("#marked").html("");
		Properties.GetSize();
		
		$("#info-bar #gen").hide();
		$("#info-bar #inf").show();
	},

	HideInfo: function() {
		$("#fileicon").hide();
		$("#filename").html("");
		$("#filetype").html("");
		$("#filesize").html("");
		$("#marked").html("");
		
		$("#info-bar #gen").show();
		$("#info-bar #inf").hide();
	},

	GetSize: function() {
		if(Status.targetFile == null) return -1;
		if(Status.targetFile.hasClass("dir")) return -1; //Size is not calculated for directories

		$.getJSON("operations/properties", {fid: Status.targetFile.data("id"), format:1}, function (res) {
			$("#filesize").html("Size: " + res.size + " " + res.unit);
			return res.size;
		});
	}
};
