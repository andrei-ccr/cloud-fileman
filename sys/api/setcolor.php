<?php
	require_once("../obj/File.php");
	
	if(!(isset($_POST['fid']) && isset($_POST['color']) && isset($_POST['permid']))) {
		http_response_code(400);
		echo json_encode(array("error" => "No file id and new file name."));
		exit;
	}

	//Check if $fid is a positive integer
	if(!is_numeric($_POST['fid']) || ($_POST['fid']<0)) {
		http_response_code(400);
		die(json_encode(array("error" => "Check file id. Invalid id.")));
	}

	$fid = (int)$_POST['fid'];

	
	try {
		$file = new File($fid, $_POST['permid']);
		$file->SetColor($_POST['color']);
		http_response_code(200);
		echo json_encode(array("success" => true));

	} catch (Exception $e) {
		http_response_code(400);
		echo json_encode( array ("error" => "Check file id. Invalid resource or server error." ) );
		exit;
	}

	

?>