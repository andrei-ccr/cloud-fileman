<?php
    require_once("../obj/Disc.php");
    require_once("../obj/File.php");

	if(!(isset($_POST['discid']) && isset($_POST['fid']) && isset($_POST['content']) )) {
		http_response_code(400);
		exit;
    }
    
    if(strlen($_POST['content']) > (1024*1024) ) {
        http_response_code(400);
        exit;
    }

	try {

		$disc = new Disc($_POST['discid']);
        $f = new File($_POST['fid']);

        if($disc->GetDiscId() != $f->GetDiscId()) 
            throw new Exception("File is not on the current disc");

        $f->WriteBinaryData($_POST['content']);

        echo json_encode(array("success" => true));
        http_response_code(200);
        exit;
        
	} catch (Exception $e) {
		http_response_code(400);
		exit;
	}

?>