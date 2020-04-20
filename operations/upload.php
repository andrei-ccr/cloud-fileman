<?php
	require_once("../obj/Disc.php");

	if(!(isset($_FILES['discdata']))) {
		http_response_code(400);
		echo json_encode(array("internalError" => "No disc id and current directory id.", "error" => "Internal error."));
		exit;
	}

	$discdata = json_decode(file_get_contents($_FILES['discdata']['tmp_name']));
	unlink($_FILES['discdata']['tmp_name']);

	$disc;
	try {
		$disc = new Disc($discdata->discid);
	} catch (Exception $e) {
		http_response_code(400);
		echo json_encode(array("internalError" => "Invalid disc id.", "error" => "Internal error."));
		exit;
	}

	if(!isset($_FILES['file0'])) {
		http_response_code(400);
		echo json_encode(array("internalError" => "Invalid upload.", "error"=>""));
		exit;
	}
	
	//print_r($_FILES['UploadProgressTrackId']);

	$i=0;
	foreach($_FILES as $file) {
		//Ignore the first file. This contains only json data.
		if($i==0) {
			$i++;
			continue;
		}
		try {
			$disc->UploadFile($file, $discdata->cd);
		} catch (Exception $e) {
			http_response_code(400);
			$error_array = array("internalError" => "Upload failed ({$e})", "error" => "");
			switch($e->getCode()) {
				case 3:
					$error_array["error"] = "Not enough free space!";
				break;
				default:
					$error_array["error"] = "Internal error";
				break;
			}
			echo json_encode($error_array);
			exit;
		}
	}
	
	echo json_encode(array("success" => "File(s) uploaded successfully." ));
	http_response_code(200);
	
?>