<?php
	require_once("../obj/Disc.php");

	if(!(isset($_FILES['discdata']))) {
		http_response_code(400);
		echo json_encode(array("error" => "No disc id and current directory id."));
		exit;
	}
	$discdata = json_decode(file_get_contents($_FILES['discdata']['tmp_name']));
	unlink($_FILES['discdata']['tmp_name']);

	$disc;
	try {
		$disc = new Disc($discdata->discid);
	} catch (Exception $e) {
		http_response_code(400);
		echo json_encode(array("error" => "Invalid disc id."));
		exit;
	}

	if(!isset($_FILES['file0'])) {
		http_response_code(400);
		echo json_encode(array("error" => "Invalid upload."));
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
			echo json_encode(array("error" => "Upload failed ({$e})"));
			exit;
		}
	}
	
	echo json_encode(array("success" => "File(s) uploaded successfully." ));
	http_response_code(200);
	
?>