<?php

	require_once($_SERVER['DOCUMENT_ROOT'] . "/obj/Connection.php");
	require_once("../obj/File.php");
	//require_once("../obj/FileServerGoogle.php");
	
	
	if(!isset($_POST['fid']) && !isset($_GET['fid'])) {
		http_response_code(403);
		exit;
	}
	
	$fid = (isset($_POST['fid']))?$_POST['fid']:$_GET['fid'];
	
	// ### TODO ###
	//Add a GET token that is generated during the POST call and then read during the GET call.
	//The token is available only once and expires after use or after some time.
	//Start the download only if the token is valid. 
	
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
	
	//Download the file. Works only from GET request
	if((isset($_GET['fid']))) {
		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . $file->GetFilename());
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		readfile($_SERVER['DOCUMENT_ROOT'] ."/disk/uploads/" . $file->GetKeyname());
		/*if($dat->IsLocal()) {
			readfile($_SERVER['DOCUMENT_ROOT'] ."/disk/uploads/" . $dat->GetKeyname());
		} else {
			file_put_contents("php://output", $dat->GetFileBody());
		}*/
		exit;
	}
	
?>