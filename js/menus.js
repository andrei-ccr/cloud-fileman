import { Status, ClipboardStatus } from './states.js';
import { Files } from './files-api.js';

export function Modals() {
	$(document).on("click", ".modal #save", function(e) {
		let fileid = $(".modal").data("fid");
		let did = $('#dinfo').data("did");
		let c = $(".modal textarea").val();

		$.ajax({
			url: 'operations/writefile', 
			data: {discid: did, fid: fileid, content: c},
			dataType: 'json',
			cache: false,
			type: 'post'
		})
		.done( function(res){
			$(".modal").remove();
		})
		.fail( function() {
			$("#errors").html("<i class='fas fa-exclamation-circle'></i> Nu s-a putut salva!");
		});

	});

	$(document).on("click", ".modal #cancel", function(e) {
		$(".modal").remove();
	});
}


export function Menus() {

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


	//Right clicks on file zone or a file
	$(document).on('contextmenu', "#file-zone, .listing-container .f", function(e) {
		e.preventDefault();
		e.stopPropagation();

		if(Status.isMobile) {
			return false;
		}

		if( $(this).hasClass("f") ) { 
			Files.Select($(this)); 
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

	$(document).on("click", ".cm-edit", function(e) {
		let fileid = Status.targetFile.data("id");
		let did = $('#dinfo').data("did");

		$.ajax({
			url: 'operations/readfile', 
			data: {discid: did, fid: fileid},
			dataType: 'json',
			cache: false,
			type: 'post'
		})
		.done( function(res){
			$.post("inc/modal-edit-file", {content: res.content, fid: fileid }, function(resp) {
				$("body").append(resp);
			});
		})
		.fail( function() {
			$("#errors").html("<i class='fas fa-exclamation-circle'></i> Nu s-a putut citi fisierul!");
		});

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

		Files.Paste();
		ClipboardStatus.file = null;
	});


	$(document).on("click", ".cm-new-folder", function(e) {
		Files.Newdir();
	});

	$(document).on("click", ".cm-new-file", function(e) {
		Files.Newfile();
	});

	$(document).on("click", ".cm-refresh", function() {
		Files.Read();
	});

	$(document).on("click", ".cm-download", function() {
		let fileid = Status.targetFile.data("id");
		Files.Download(fileid);
	});

	$(document).on("click", ".cm-delete", function() {
		let fileid = Status.targetFile.data("id");
		Files.Trash(fileid);
	});

	$(document).on("click", ".cm-rename", function() {
		Status.targetFile.find("span").replaceWith("<input type='text' value='"+Status.targetFilename+"' class='rename-input' autofocus>");
		Status.targetFile.css("width", "auto");
		$(".rename-input").focus();
		$(".rename-input").select();
	});

	$(document).on("click", ".cm-open", function() {
		let fileid = Status.targetFile.data("id");
		
		if(Status.targetFile.hasClass("dir")) {
			Files.Change(fileid);
		}
	});

	$(document).on("click", ".cm-view-tiles", function() {
		$("#file-listing").removeClass("big-thumbs-view");
		$("#file-listing").removeClass("icons-view");
		$.post("operations/settings", {"files_view":0});
	});

	$(document).on("click", ".cm-view-icons", function() {
		$("#file-listing").removeClass("big-thumbs-view");
		$("#file-listing").addClass("icons-view");
		$.post("operations/settings", {"files_view":1});
	});

	$(document).on("click", ".cm-view-bthumbs", function() {
		$("#file-listing").addClass("big-thumbs-view");
		$("#file-listing").removeClass("icons-view");
		$.post("operations/settings", {"files_view":2});
	});

	$(document).on('click', ".cm-upload, #bar .fa-upload", function() {
		$("#file-up").trigger('click');
	});

	$(document).on('click', ".cm-to-settings, #profile-pic, #profile-name", function() {
		window.location.href = "/settings";
	});

	$(document).on('click', ".cm-to-help", function() {
		window.location.href = "/help";
	});

	$(document).on('click', ".cm-logout", function() {
		let mhand = $("#dinfo").data("hdl");
		$.post("/operations/logout", {mhandle:mhand}, function() {
			$.post("/operations/web/logout", {member:1}, function() {
				window.location = "/";
			});
		});
	});

	$(document).on('click', ".cm-logout-guest", function() {
		let mhand = $("#dinfo").data("hdl");
		$.post("/operations/logout", {mhandle:mhand}, function() {
			$.post("/operations/web/logout", {guest:1}, function() {
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
