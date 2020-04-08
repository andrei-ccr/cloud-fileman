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
					ShowAuthError("A aparut o problema si nu va puteti loga. Incercati mai tarziu.");
				});
			}
		})
		.fail(function() {
			ShowAuthError("Email-ul sau parola nu sunt corecte. Incearca din nou.");
		});
	});

	$(document).on('click', '#btn-register', function(e) {
		let emailInput = $("#email").val();
		let passInput = $("#pass").val();
		if ($.trim(emailInput).length<=0) {
			ShowAuthError("Completeaza adresa de email!");
			return;
		}
		if ($.trim(passInput).length<=0) {
			ShowAuthError("Completeaza parola!");
			return;
		}
		if (passInput.length<=5) {
			ShowAuthError("Parola trebuie sa fie mai lunga de 5 caractere!");
			return;
		}
		
		$.ajax({
			url: "/operations/register",
			data: {email: emailInput, pass: passInput},
			cache: false,
			method: "post",
		})
		.done(function(resp) {	
			ShowAuthError("Contul a fost creat cu SUCCES!");
		})
		.fail(function() {
			ShowAuthError("Contul nu a putut fi creat. Incearca din nou!");
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
					ShowAuthError("A aparut o problema si nu va puteti loga. Incercati mai tarziu.");
				});
			}
		})
		.fail(function() {
			ShowAuthError("A aparut o problema si nu va puteti loga. Incercati mai tarziu.");
		});
	});
}

export function ShowAuthError($error_msg) {
	if($("#error-container").length>0) { $("#error-container").remove(); }
	$("body").prepend('<div class="box-container" id="error-container"><p class="box-container-txt" style="color:red">' + $error_msg + '</p></div>');
	
}


