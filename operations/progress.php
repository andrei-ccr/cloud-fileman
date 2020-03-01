<?php
	session_start();
	header('Content-type: application/json');
	if(isset($_SESSION['upload_progress_123']))
		echo json_encode($_SESSION["upload_progress_123"]);
	else 
		echo json_encode(array("done"=>"1"));
	

?>