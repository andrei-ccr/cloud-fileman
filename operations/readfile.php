<?php
    require_once("../obj/Disc.php");
    require_once("../obj/File.php");

	if(!(isset($_POST['discid']) && isset($_POST['fid']))) {
		http_response_code(400);
		exit;
	}

	try {
		$disc = new Disc($_POST['discid']);
        $f = new File((int)$_POST['fid']);
        if($disc->GetDiscId() != $f->GetDiscId()) 
            throw new Exception("File is not on the current disc");

        $file_content = $f->ReadBinaryData();
        if(strlen($file_content) > (1024*1024)) {
            
            echo json_encode(array("error"=>"File exceeds 1MB"));
            http_response_code(400);
            exit;
        }

        echo json_encode(array("content" => $file_content, "dummydata"=>"dummydata"));
        http_response_code(200);
		exit;
	} catch (Exception $e) {
        http_response_code(400);
        echo json_encode(array("error" => $e->getMessage()));
		exit;
	}

?>