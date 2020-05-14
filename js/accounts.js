export function AuthSystem() {
	$(document).on('click', '#btn-enter', function(e) {
		let emailInput = $("#email").val();
		let passInput = $("#pass").val();
		
		$.ajax({
			url: "/operations/login",
			data: {email: emailInput, pass: passInput},
			cache: false,
			method: "post",
			dataType: "json"
		})
		.done(function(resp) {
			if(resp['result'] == "member") {
				//Client side login for web browser
				$.ajax({
					url: "/operations/web/login",
					data: {
						uid: resp['userid'], 
						per: resp['permid'], 
						did: resp['discid'],
						remember: null
					},
					cache: false,
					method: "post"
				})
				.done(function(res) {
					location.reload();
				})
				.fail(function() {
					ShowAuthError("An error has occured! Try again later.");
				});
			}
		})
		.fail(function() {
			ShowAuthError("The email or password is incorrect!");
		});
	});

	$(document).on('click', '#btn-register', function(e) {
		let emailInput = $("#email").val();
		let passInput = $("#pass").val();
		if ($.trim(emailInput).length<=0) {
			ShowAuthError("Enter an email address!");
			return;
		}
		if ($.trim(passInput).length<=0) {
			ShowAuthError("Enter your password!");
			return;
		}
		if (passInput.length<=5) {
			ShowAuthError("Password should be longer than 5 characters!");
			return;
		}
		
		$.ajax({
			url: "/operations/register",
			data: {email: emailInput, pass: passInput},
			cache: false,
			method: "post",
		})
		.done(function(resp) {	
			ShowAuthError("New account created successfully!");
		})
		.fail(function() {
			ShowAuthError("Couldn't create a new account! Try again later.");
		});
	});
	
	$(document).on('click', '#btn-no-acc', function(e) {
		$.ajax({
			url: "/operations/login",
			data: {guest: true},
			cache: false,
			method: "post",
			dataType: "json"
		})
		.done(function(resp) {
			if(resp['result'] == "guest") {
				$.ajax({
					url: "/operations/web/login",
					data: {gdid: resp['discid'], gperid: resp['permid']},
					cache: false,
					method: "post"
				})
				.done(function(res) {
					location.reload();
				})
				.fail(function() {
					ShowAuthError("An error has occured! Try again later.");
				});
			}
		})
		.fail(function() {
			ShowAuthError("Couldn't log in as a guest! Try again later.");
		});
	});
}

export function ShowAuthError($error_msg) {
	
	if($("#error-container").length>0) { $("#error-container").remove(); }
	$("body").prepend('<div class="box-container" id="error-container"><p class="box-container-txt" style="color:red">' + $error_msg + '</p></div>');
	
}


