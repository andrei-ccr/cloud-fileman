
$(document).on("click", "#cm-new-folder", function(e) {
	Files.Newdir();
});

$(document).on("click", "#cm-refresh", function() {
	Files.Read();
});

$(document).on("click", "#cm-download", function() {
	let fileid = Status.targetFile.data("id");
	Files.Download(fileid);
});

$(document).on("click", "#cm-delete", function() {
	let fileid = Status.targetFile.data("id");
	Files.Trash(fileid);
});

$(document).on("click", "#cm-rename", function() {
	Status.targetFile.find("span").replaceWith("<input type='text' value='"+Status.targetFilename+"' class='rename-input' autofocus>");
	Status.targetFile.css("width", "auto");
	$(".rename-input").focus();
	$(".rename-input").select();
});

$(document).on("click", "#cm-open", function() {
	var fileid = Status.targetFile.data("id");
	
	if(Status.targetFile.hasClass("dir")) {
		Files.Change(fileid);
	}
});

$(document).on("click", "#cm-view-tiles", function() {
	$("#file-listing").removeClass("big-thumbs-view");
	$("#file-listing").removeClass("icons-view");
	$.post("operations/settings", {"files_view":0});
});

$(document).on("click", "#cm-view-icons", function() {
	$("#file-listing").removeClass("big-thumbs-view");
	$("#file-listing").addClass("icons-view");
	$.post("operations/settings", {"files_view":1});
});

$(document).on("click", "#cm-view-bthumbs", function() {
	$("#file-listing").addClass("big-thumbs-view");
	$("#file-listing").removeClass("icons-view");
	$.post("operations/settings", {"files_view":2});
});

$(document).on('click', "#cm-upload, #bar .fa-upload", function() {
	$("#file-up").trigger('click');
});

$(document).on('click', "#cm-to-settings", function() {
	return false;
});

$(document).on('click', "#cm-logout", function() {
	$.get("/operations/logout", function() {
		window.location = "/" //Reload without GET params
	});
});

$(document).on('click', "#cm-login", function() {
	document.cookie = "guest=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
	location.reload();
});


$(document).on("click", ".cm li", function(e) {
	e.stopPropagation();
	$(".cm").remove();
});