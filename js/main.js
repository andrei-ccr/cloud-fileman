var Status = {
	targetFile : null,
	isMobile : false,
	targetFilename : ""
};

if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
	|| /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) { 
	Status.isMobile = true;
}

$(document).ready(function() {
	if(Status.isMobile){
		$("#mobile-bar").show();
	}
});


$(document).on("mousedown", function(e) {
	if (!$(e.target).parents(".cm").length > 0) {
		$(".cm").remove();
	}
	
	if(!$(e.target).is("input")) {
		if($(".rename-input").length>0) {
			Files.Rename(Status.targetFile.data("id"), $(".rename-input").val(), 1);
			$(".rename-input").replaceWith("<span>"+$(".rename-input").val()+"</span>");
		}
	}

	if( (!$(e.target).parents("#bar").length>0) && (!$(e.target).is("#bar")) && (!$(e.target).is(".f")) && (!$(e.target).parents(".f").length>0) && (!$(e.target).parents(".cm").length>0) ) {
		Files.Deselect();
	}
});

$(document).on("click", ".listing-container .f", function(e) {
	if(!$(this).hasClass("selected")) {
		Files.Select($(this));
	} else {
		if($(this).hasClass("dir")) {
			Files.Change($(this).data("id"));
			Properties.HideInfo();
		}
	}
});

//File rename
$(document).on('keypress', ".rename-input", function(e) {
	let key = e.which;
	if(key == 13) {
		if($.trim($(".rename-input").val()) != "") {
			Files.Rename(Status.targetFile.data("id"), $(".rename-input").val());
			$(".rename-input").replaceWith("<span>"+$(".rename-input").val()+"</span>");
		}
	}
});

$(document).on('click', ".rename-input", function(e) {
	e.stopPropagation();
});


//Rightclicks on file zone or a file
$(document).on('contextmenu', "#file-zone, .listing-container .f", function(e) {
	e.preventDefault();
	e.stopPropagation();

	if(Status.isMobile) {
		return false;
	}

	if( $(this).hasClass("f") ) { 
		Files.Select($(this)); 
	}

	json_status = JSON.stringify(Status);

	$.post("inc/context-menu", {status : json_status}, function(resp) {
		$("body").append(resp);
		$(".context-menu").show(50).css({
			top: e.clientY + "px",
			left: e.clientX + "px"
		});
	});

});


//Go to the root of disk
$(document).on('click', "#root-location", function(e) {
	Files.Change(0);
});


//Click on menu button on the top bar
$(document).on('click', "#bar .fa-ellipsis-v", function(e) {
	e.preventDefault();
	
	json_status = JSON.stringify(Status);

	$.post("inc/bar-menu", {status : json_status}, function(resp) {
		$("body").append(resp);
		$(".m-context-menu").show(50).css({
			top: "35px",
			right: "0px"
		});
	});
});

//Dismiss error message on click.
$(document).on("click", "#errors", function() {
	$(this).html("");
});

//Dismiss guest message on click.
$(document).on("click", "#guest", function() {
	$(this).remove();
});


//Drag and drop file upload 
$(document).on('dragover','#file-zone', function(e) {
	e.preventDefault();
	e.stopPropagation();
});
$(document).on('dragenter','#file-zone', function(e) {
	e.preventDefault();
	e.stopPropagation();
});
$(document).on('drop','#file-zone', function(e) {
	var droppedFiles = e.originalEvent.dataTransfer.files;
	e.preventDefault();
	e.stopPropagation();
	
	if(droppedFiles.length <= 0) return false; //If the dropped object is not a file
	
	Files.Upload(droppedFiles);
});


$(document).on('change', "#file-up", function() {
	var droppedFiles = document.getElementById('file-up').files;
	
	Files.Upload(droppedFiles);
	return false;
});

