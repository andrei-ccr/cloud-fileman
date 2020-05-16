export var Status = {
	targetFile : null,
	isMobile : false,
	targetFilename : ""
};

export var ClipboardStatus = {
	file : null,
	cut : false
}

export function GetDiscData() {
    return {
        discid: $("#dinfo").data("did"),
        cd: $("#dinfo").data("cd"),
        permid: $("#dinfo").data("hdl")
    };
}
