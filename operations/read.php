<?php
	require_once("../obj/Disc.php");

	if(!(isset($_POST['discid']) && isset($_POST['cd']))) {
		http_response_code(400);
		exit;
	}

	try {
		$disc = new Disc($_POST['discid']);
		$f = $disc->ReadDirectory($_POST['cd']);
		http_response_code(200);
		echo json_encode($f);
		exit;
	} catch (Exception $e) {
		http_response_code(400);
		exit;
	}

?>