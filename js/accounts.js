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
				location.reload();
			} else if(resp['result'] == "register") {
				
			}
		})
		.fail(function() {
			if($("#error-container").length>0) {
				$("#error-container").remove();
			}
			$("body").prepend('<div class="box-container" id="error-container"><p class="box-container-txt" style="color:red">Email-ul sau parola sunt incorecte. Incearca din nou.</p></div>');
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
			$("body").prepend('<p class="box-container-txt" style="color:red">A aparut o problema si nu va puteti loga. Incercati mai tarziu.</p>');
			console.log("System error: couldn't log in as guest. Retry later.");
		});
	});

});

