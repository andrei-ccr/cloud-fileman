<?php
	if (session_status() == PHP_SESSION_NONE) session_start();
	require_once("../obj/File.php");
	
	if(isset($_POST['fid']) && isset($_POST['permid'])) {
		
		//Check if $fid is a positive integer
		if(!is_numeric($_POST['fid']) || ($_POST['fid']<0)) {
			http_response_code(400);
			die(json_encode(array("error" => "Check file id. Invalid id.")));
		}
		
		$fid = (int)$_POST['fid'];
		
		//This is the root of the Disc
		if($fid == 0) {

			//Set SESSION vars. For website only
			$_SESSION['cdid'] = $fid;
			$_SESSION['dir_list'] = array();

			echo json_encode(array("path" => "", "cdid" => $fid, "root" => true));
			http_response_code(200);
			exit;
		}

		//Change to entered directory here
		try {
			$directory = new File($fid, $_POST['permid']);
			if($directory->IsDir()) {

				//Set SESSION vars
				$_SESSION['cdid'] = $fid;
				if(!isset($_SESSION['dir_list'])) { $_SESSION['dir_list'] = array(); }
				array_push($_SESSION['dir_list'], $directory->GetFilename());

				echo json_encode(array("path" => implode("/", $_SESSION['dir_list']), "cdid" => $fid, "root" => false));
				http_response_code(200);
				exit;
			} else {
				
				//This is not a directory
				http_response_code(400);
				die(json_encode(array("error" => "Check file id. Not a directory.")));
			}
			
		} catch(Exception $e) {
			//Exception thrown. Probably the resource doesn't exist in database.
			http_response_code(400);
			die(json_encode(array("error" => "Check file id. Invalid resource.")));
		}
		
	} else {
		http_response_code(400);
		echo json_encode(array("error" => "No file id or permission."));
		exit;
	}
	
?>