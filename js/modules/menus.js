import { Status, ClipboardStatus } from './states.js';
import { ReadLocation, Select, PasteFile, NewFolder, NewFile, ReadCurrentDirectory, Download, Trash, ChangeCurrentDirectory, SetColor } from './files.js';
import { ShowEditModal } from './modals.js';
import { ShowDetails } from './properties.js';


export function IntegrateContextMenu() {

	//Right clicks on file zone or a file
	$(document).on('contextmenu', ".file-container, .file-container .f", function(e) {
		e.preventDefault();
		e.stopPropagation();

		if(Status.isMobile) {
			return false;
		}

		if( $(this).hasClass("selected") == false ) { 
			Select($(this)); 
		}

		let height = $("body").outerHeight();
		let json_status = JSON.stringify(Status);
		let json_clipboard = JSON.stringify(ClipboardStatus);
		let bool_isDir = $(this).hasClass("dir");

		$.post("inc/context-menu", {
			status : json_status, 
			clipboard : json_clipboard,
			isDir : bool_isDir
		}, function(resp) {
			$("body").append(resp);
			let offset = 0
			if (e.clientY + $(".context-menu").outerHeight() > height) {
				offset = height - (e.clientY + $(".context-menu").outerHeight());
				$(".context-menu").show().css({
					top: e.clientY + offset + "px",
					left: e.clientX + 15 + "px"
				});
			} else {
				$(".context-menu").show().css({
					top: e.clientY + "px",
					left: e.clientX + 15 + "px"
				});
			}
			
		});

	});
}

export function DeclareMenuButtons() {

	$(document).on("click", ".cm-file-info", function(e) {
		ShowDetails();
	});

	$(document).on("click", ".cm-color-orange", function() {
		let fileids = [];
		Status.selectedFiles.forEach(function(file) {
			fileids.push(file[0]);
		});

		SetColor(fileids, "#db6701");
	});
	$(document).on("click", ".cm-color-yellow", function() {
		let fileids = [];
		Status.selectedFiles.forEach(function(file) {
			fileids.push(file[0]);
		});

		SetColor(fileids,"#d9ae04");
	});
	$(document).on("click", ".cm-color-green", function() {
		let fileids = [];
		Status.selectedFiles.forEach(function(file) {
			fileids.push(file[0]);
		});

		SetColor(fileids,"#09bf2e");
	});
	$(document).on("click", ".cm-color-cyan", function() {
		let fileids = [];
		Status.selectedFiles.forEach(function(file) {
			fileids.push(file[0]);
		});

		SetColor(fileids,"#12adea");
	});
	$(document).on("click", ".cm-color-blue", function() {
		let fileids = [];
		Status.selectedFiles.forEach(function(file) {
			fileids.push(file[0]);
		});

		SetColor(fileids,"#053bdb");
	});
	$(document).on("click", ".cm-color-pink", function() {
		let fileids = [];
		Status.selectedFiles.forEach(function(file) {
			fileids.push(file[0]);
		});

		SetColor(fileids,"#db11bb");
	});
	$(document).on("click", ".cm-color-purple", function() {
		let fileids = [];
		Status.selectedFiles.forEach(function(file) {
			fileids.push(file[0]);
		});

		SetColor(fileids,"#7b00bf");
	});
	$(document).on("click", ".cm-color-red", function() {
		let fileids = [];
		Status.selectedFiles.forEach(function(file) {
			fileids.push(file[0]);
		});

		SetColor(fileids,"#e80909");
	});

	$(document).on("click", ".nav-btn", function(e) {
		$(".nav-btn").removeClass("selected");
		$(this).addClass("selected");
	});

	$(document).on("click", ".cm-nav-files", function(e) {
		ChangeCurrentDirectory(0);
	});
	$(document).on("click", ".cm-nav-recent", function(e) {
		ReadLocation(3);
	});
	$(document).on("click", ".cm-nav-shared", function(e) {
		ReadLocation(2);
	});
	$(document).on("click", ".cm-nav-starred", function(e) {
		ReadLocation(1);
	});
	$(document).on("click", ".cm-nav-deleted", function(e) {
		ReadLocation(4);
	});

	$(document).on("click", ".cm-edit", function(e) {
		//Edit only the first selected file
		let fileid = Status.selectedFiles[0][1].data("id");
		ShowEditModal(fileid);
	});

	$(document).on("click", ".cm-cut", function(e) {
		//TODO: Get all selected files
		let fileid = Status.selectedFiles[0][1].data("id");
		ClipboardStatus.file = fileid;
		ClipboardStatus.cut = true;

	});

	$(document).on("click", ".cm-copy", function(e) {
		//TODO: Get all selected files
		let fileid = Status.selectedFiles[0][1].data("id");
		ClipboardStatus.file = fileid;
		ClipboardStatus.cut = false;

	});

	$(document).on("click", ".cm-paste", function(e) {
		if($(this).hasClass("cm-disabled")) return;

		PasteFile();
		ClipboardStatus.file = null;
	});


	$(document).on("click", ".cm-new-folder", function(e) {
		NewFolder();
	});

	$(document).on("click", ".cm-new-file", function(e) {
		NewFile();
	});

	$(document).on("click", ".cm-refresh", function() {
		ReadCurrentDirectory();
	});

	$(document).on("click", ".cm-download", function() {
		//TODO: Get all selected files
		let fileid = Status.selectedFiles[0][1].data("id");
		Download(fileid);
	});

	$(document).on("click", ".cm-delete", function() {
		//TODO: Get all selected files
		let fileid = Status.selectedFiles[0][1].data("id");
		Trash(fileid);
	});

	$(document).on("click", ".cm-rename", function() {
		Status.selectedFiles[0][1].find("p").replaceWith("<input type='text' value='" + Status.selectedFiles[0][1] + "' class='rename-input' autofocus>");
		Status.selectedFiles[0][1].css("width", "auto");
		$(".rename-input").focus();
		$(".rename-input").select();
	});

	$(document).on("click", ".cm-open", function() {
		let fileid = Status.selectedFiles[0][1].data("id");
		
		if(Status.selectedFiles[0][1].hasClass("dir")) {
			ChangeCurrentDirectory(fileid);
		}
	});

	$(document).on("click", ".cm-view-icons", function() {
		$("#file-listing").removeClass("list-view");
		$("#file-listing").addClass("icons-view");
		//$.post("sys/api/settings", {"files_view":1});
	});

	$(document).on("click", ".cm-view-list", function() {
		$("#file-listing").addClass("list-view");
		$("#file-listing").removeClass("icons-view");
		//$.post("sys/api/settings", {"files_view":2});
	});

	$(document).on('click', ".cm-upload, #bar .fa-upload", function() {
		$("#file-up").trigger('click');
	});

	$(document).on('click', ".cm-to-help", function() {
		window.location.href = "/help";
	});

	$(document).on('click', ".cm-logout", function() {
		let mhand = $("#dinfo").data("hdl");
		$.post("/sys/api/logout", {mhandle:mhand}, function() {
			$.post("/sys/api/web/logout", {member:1}, function() {
				window.location = "/";
			});
		});
	});

	$(document).on('click', ".cm-logout-guest", function() {
		let mhand = $("#dinfo").data("hdl");
		$.post("/sys/api/logout", {mhandle:mhand}, function() {
			$.post("/sys/api/web/logout", {guest:1}, function() {
				window.location = "/";
			});
		});
	});

	$(document).on('click', ".cm-login", function() {
		document.cookie = "guest=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
		location.reload();
	});


	$(document).on("click", ".cm li", function(e) {
		e.stopPropagation();
		$(".cm").remove();
	});

}
