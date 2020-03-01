$(document).ready(function() {
	$(document).on('click', '#btn-enter', function(e) {
		$emailInput = $("#email-input").val();
		$passInput = $("#pass-input").val();
		
		$.ajax({
			url: "/operations/login",
			data: {email: $emailInput, pass: $passInput},
			cache: false,
			method: "post",
			dataType: "json"
		})
		.done(function(resp) {
			if(resp['result'] == "member") {
				location.reload();
			} else if(resp['result'] == "register") {
				
			}
		})
		.fail(function() {
			console.log("System error");
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
				location.reload();
			}
		})
		.fail(function() {
			console.log("System error: couldn't log in as guest. Retry later.");
		});
	});

});

