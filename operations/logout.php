<?php

	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
	
	require_once($_SERVER['DOCUMENT_ROOT']  . "/obj/User.php");
	
	if(isset($_POST['mhandle'])) {
		try {
			$user = new User($_POST['mhandle']);
			$user->Logout();
		} catch (Exception $e) {
			http_response_code(500);
		}
		
	} else {
		http_response_code(400);
		exit;
	}
?>