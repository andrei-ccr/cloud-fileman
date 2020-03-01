<?php

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
	
	require_once($_SERVER['DOCUMENT_ROOT']  . "/obj/Contact.php");
	
	$contact = new Contact();
	
	$contact->Logout();
?>