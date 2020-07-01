export function Login(Email, Pwd) {
	$.ajax({
		url: "/sys/api/login",
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

export function Register(Email, Pwd, Username="#") {
	$.ajax({
		url: "/sys/api/register",
		data: {email: Email, pass: Pwd, username: Username},
		cache: false,
		method: "post",
	})
	.done(function(resp) {	
		window.location.href="/register?success=1";
	})
	.fail(function() {
		ShowAuthError("Couldn't create a new account! Try again later.");
	});
}

export function LoginAsGuest() {
	$.ajax({
		url: "/sys/api/login",
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
	$(".box-container").first().prepend('<div id="error-container"><p class="box-container-txt" style="color:red">' + error_msg + '</p></div>');
	
}

function AuthenticateInBrowser(JSONResponse, AsSession=false) {

	$.ajax({
		url: "/sys/api/web/login",
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
		url: "/sys/api/web/login",
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

	$("#email").css("border-color", "#65acc5");
	$("#pass").css("border-color", "#65acc5");
	if($("#error-container").length>0) { $("#error-container").remove(); }

	if (($.trim(EmailInput).length <= 0) || ($.trim(EmailInput).length >= 255)) {
		ShowAuthError("Enter a valid email address!");
		$("#email").css("border-color", "red");
		return false;
	}

	if ($.trim(PwdInput).length <= 0) {
		$("#pass").css("border-color", "red");
		return false;
	}

	return true;

}

export function ValidateInputRegister(EmailInput, ConfirmEmailInput, PwdInput, ConfirmPwdInput, UsernameInput="") {

	$("#email").css("border-color", "#65acc5");
	$("#pass").css("border-color", "#65acc5");
	$("#username").css("border-color", "#65acc5");
	$("#cemail").css("border-color", "#65acc5");
	$("#cpass").css("border-color", "#65acc5");

	let regexSpecialChars = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;

	if($("#error-container").length>0) { $("#error-container").remove(); }

	if (($.trim(EmailInput).length <= 0) || ($.trim(EmailInput).length >= 255)) {
		ShowAuthError("Enter a valid email address!");
		$("#email").css("border-color", "red");
		return false;
	}

	if ( $.trim(ConfirmEmailInput) !== $.trim(EmailInput))  {
		ShowAuthError("Email address doesn't match!");
		$("#email").css("border-color", "red");
		$("#cemail").css("border-color", "red");
		return false;
	}

	if ( regexSpecialChars.test(UsernameInput))  {
		ShowAuthError("Username cannot contain special characters");
		$("#username").css("border-color", "red");
		return false;
	}

	if ($.trim(PwdInput).length <= 0) {
		ShowAuthError("Password cannot be empty!");
		$("#pass").css("border-color", "red");
		return false;
	}

	if ( $.trim(ConfirmPwdInput) != $.trim(PwdInput))  {
		ShowAuthError("Password doesn't match!");
		$("#pass").css("border-color", "red");
		$("#cpass").css("border-color", "red");
		return false;
	}

	return true;

}
