import { Status, ClipboardStatus } from './states.js';
import { Select, PasteFile, NewFolder, NewFile, ReadCurrentDirectory, Download, Trash, ChangeCurrentDirectory } from './files.js';
import { ShowEditModal } from './modals.js';


export function IntegrateBarMenu() {

	//Click on menu button on the top bar
	$(document).on('click', "#bar .fa-ellipsis-v", function(e) {
		e.preventDefault();
		
		let json_status = JSON.stringify(Status);

		$.post("inc/bar-menu", {status : json_status}, function(resp) {
			$("body").append(resp);
			$(".m-context-menu").show(50).css({
				top: "35px",
				right: "0px"
			});
		});
	});
}

export function IntegrateContextMenu() {

	//Right clicks on file zone or a file
	$(document).on('contextmenu', ".file-container, .file-container .f", function(e) {
		e.preventDefault();
		e.stopPropagation();

		if(Status.isMobile) {
			return false;
		}

		if( $(this).hasClass("f") ) { 
			Select($(this)); 
		}

		let json_status = JSON.stringify(Status);
		let json_clipboard = JSON.stringify(ClipboardStatus);

		$.post("inc/context-menu", {
			status : json_status, 
			clipboard : json_clipboard
		}, function(resp) {
			$("body").append(resp);
			$(".context-menu").show(50).css({
				top: e.clientY + "px",
				left: e.clientX + "px"
			});
		});

	});
}

export function DeclareMenuButtons() {
	$(document).on("click", ".cm-edit", function(e) {
		let fileid = Status.targetFile.data("id");
		ShowEditModal(fileid);
	});

	$(document).on("click", ".cm-cut", function(e) {
		let fileid = Status.targetFile.data("id");
		ClipboardStatus.file = fileid;
		ClipboardStatus.cut = true;

	});

	$(document).on("click", ".cm-copy", function(e) {
		let fileid = Status.targetFile.data("id");
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
		let fileid = Status.targetFile.data("id");
		Download(fileid);
	});

	$(document).on("click", ".cm-delete", function() {
		let fileid = Status.targetFile.data("id");
		Trash(fileid);
	});

	$(document).on("click", ".cm-rename", function() {
		Status.targetFile.find("p").replaceWith("<input type='text' value='"+Status.targetFilename+"' class='rename-input' autofocus>");
		Status.targetFile.css("width", "auto");
		$(".rename-input").focus();
		$(".rename-input").select();
	});

	$(document).on("click", ".cm-open", function() {
		let fileid = Status.targetFile.data("id");
		
		if(Status.targetFile.hasClass("dir")) {
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
