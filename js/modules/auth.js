export function Login(Email, Pwd) {
	$.ajax({
		url: "/operations/login",
		data: {email: Email, pass: Pwd},
		cache: false,
		method: "post",
		dataType: "json"
	})
	.done(function(JSONResp) {
		if(JSONResp['result'] == "member") {
			AuthenticateInBrowser(JSONResp);
		} else {
			ShowAuthError("An error has occured! Try again later.");
		}
	})
	.fail(function() {
		ShowAuthError("The email or password is incorrect!");
	});
}

export function Register(Email, Pwd) {
	$.ajax({
		url: "/operations/register",
		data: {email: Email, pass: Pwd},
		cache: false,
		method: "post",
	})
	.done(function(resp) {	
		ShowAuthMessage("New account created successfully! You can now log in!");
	})
	.fail(function() {
		ShowAuthError("Couldn't create a new account! Try again later.");
	});
}

export function LoginAsGuest() {
	$.ajax({
		url: "/operations/login",
		data: {guest: true},
		cache: false,
		method: "post",
		dataType: "json"
	})
	.done(function(JSONResp) {
		if(JSONResp['result'] == "guest") {
			GuestAuthenticateInBrowser(JSONResp)
		} else {
			ShowAuthError("An error has occured! Try again later.");
		}
	})
	.fail(function() {
		ShowAuthError("Couldn't log in as a guest! Try again later.");
	});
}

export function ShowAuthError(error_msg) {
	
	if($("#error-container").length>0) { $("#error-container").remove(); }
	$("body").prepend('<div class="box-container" id="error-container"><p class="box-container-txt" style="color:red">' + error_msg + '</p></div>');
	
}

export function ShowAuthMessage(msg) {
	
	if($("#error-container").length>0) { $("#error-container").remove(); }
	$("body").prepend('<div class="box-container" id="error-container"><p class="box-container-txt" style="color:blue">' + msg + '</p></div>');
	
}

function AuthenticateInBrowser(JSONResponse, AsSession=false) {

	$.ajax({
		url: "/operations/web/login",
		data: {
			uid: JSONResponse['userid'], 
			per: JSONResponse['permid'], 
			did: JSONResponse['discid'],
			remember: AsSession
		},
		cache: false,
		method: "post"
	})
	.done(function() {
		location.reload();
	})
	.fail(function() {
		ShowAuthError("An error has occured! Try again later.");
	});
}

function GuestAuthenticateInBrowser(JSONResponse) {

	$.ajax({
		url: "/operations/web/login",
		data: {gdid: JSONResponse['discid'], gperid: JSONResponse['permid']},
		cache: false,
		method: "post"
	})
	.done(function() {
		location.reload();
	})
	.fail(function() {
		ShowAuthError("An error has occured! Try again later.");
	});
}

export function ValidateInput(EmailInput, PwdInput) {

	if ($.trim(EmailInput).length <= 0) {
		ShowAuthError("You must enter an email address!");
		return false;
	}

	if ($.trim(EmailInput).length >= 255) {
		ShowAuthError("The email address is not valid!");
		return false;
	}

	if ($.trim(PwdInput).length <= 0) {
		ShowAuthError("You must enter your password!");
		return false;
	}

	if ($.trim(PwdInput).length > 60) {
		ShowAuthError("The password cannot be longer 60 characters!");
		return false;
	}

	if (PwdInput.length <= 5) {
		ShowAuthError("Password must be longer than 5 characters!");
		return false;
	}

	return true;

}
