$(document).ready(function() {
	$(document).on('click', '#btn-enter', function(e) {
		$emailInput = $("#email").val();
		$passInput = $("#pass").val();
		console.log($emailInput);
		console.log($passInput);
		
		$.ajax({
			url: "/operations/login",
			data: {email: $emailInput, pass: $passInput},
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
					ShowAnError("A aparut o problema si nu va puteti loga. Incercati mai tarziu.");
				});
			}
		})
		.fail(function() {
			ShowAnError("Email-ul sau parola nu sunt corecte. Incearca din nou.");
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
					ShowAnError("A aparut o problema si nu va puteti loga. Incercati mai tarziu.");
				});
			}
		})
		.fail(function() {
			ShowAnError("A aparut o problema si nu va puteti loga. Incercati mai tarziu.");
		});
	});

});


function ShowAnError($error_msg) {
	if($("#error-container").length>0) { $("#error-container").remove(); }
	$("body").prepend('<div class="box-container" id="error-container"><p class="box-container-txt" style="color:red">' + $error_msg + '</p></div>');
	
}


