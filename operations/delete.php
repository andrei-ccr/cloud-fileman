<?php
	require_once("../obj/File.php");
	
	if(isset($_POST['fid']) && isset($_POST['permid'])) {

		//Check if $fid is a positive integer
		if(!is_numeric($_POST['fid']) || ($_POST['fid']<0)) {
			http_response_code(400);
			die(json_encode(array("error" => "Check file id. Invalid id.")));
		}
		
		$fid = (int)$_POST['fid'];
	
		try {
			$file = new File($fid, $_POST['permid']);
			$file->Delete();
			
			http_response_code(200);
			echo json_encode(array("deleteSuccess" => true));
			exit;
			
		} catch(Exception $e) {
			//Exception thrown. Probably the resource doesn't exist in database.
			http_response_code(400);
			echo json_encode(array("error" => "Check file id. Invalid resource."));
			exit;
		}
		
	} else {
		http_response_code(400);
		echo json_encode(array("error" => "No file id or permission."));
		exit;
	}
	
?>