<?php
	require_once("../obj/Disc.php");
	
	if(isset($_POST['discid'])) {

		//Check if $disc_id is a positive integer
		if(!is_numeric($_POST['discid']) || ($_POST['discid'] < 0)) {
			http_response_code(400);
			die(json_encode(array("error" => "Check disc id. Invalid id.")));
		}

		try {
			$disc = new Disc($_POST['discid']);
			if(isset($_POST['cd'])) {
				$dir_id = $disc->CreateFile("File", $_POST['cd']);
			} else {
				$dir_id = $disc->CreateFile("File");
			}

			if($dir_id<=0) {
				http_response_code(500);
				echo json_encode(array("error" => "Server error. Try again later."));
			}
			
			http_response_code(200);
			echo json_encode(array("new_fid" => $dir_id));
			exit;
			
		} catch(Exception $e) {
			http_response_code(400);
			echo json_encode(array("error" => "Check disc id. Invalid resource."));
			exit;
		}
		
	} else {
		http_response_code(400);
		echo json_encode(array("error" => "No disc id. {$_POST['discid']}"));
		exit;
	}
	

	
?>