<?php
	/*header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");*/
	
	require_once($_SERVER['DOCUMENT_ROOT'] ."/sys/obj/User.php");
	
	if(!isset($_POST['email']) && !isset($_POST['pass'])) {
		http_response_code(400);
		exit;
	}
	
	try {
		User::RegisterUser($_POST['email'], $_POST['pass']);
		http_response_code(200);
		exit;
	} 
	catch (Exception $e) {
		http_response_code(400);
		exit;
	}

?>