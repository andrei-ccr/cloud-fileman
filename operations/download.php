<?php
	require_once("../obj/File.php");
	
	if(!isset($_GET['fid'])) {
		http_response_code(403);
		exit;
	}
	
	$fid = $_GET['fid'];
	
	//Load the file in memory
	try {
		$file = new File((int)$fid);
		if($file->IsDir()) {
			//Folders are not downloadable yet.
			http_response_code(405);
			exit;
		}
	} catch(Exception $e) {
		http_response_code(400);
		exit;
	}
	
	header('Content-type: application/octet-stream');
	header('Content-Disposition: attachment; filename=' . $file->GetFilename());
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	readfile($_SERVER['DOCUMENT_ROOT'] ."/uploads/" . $file->GetKeyname());
	http_response_code(200);
	exit;
	
	
?>