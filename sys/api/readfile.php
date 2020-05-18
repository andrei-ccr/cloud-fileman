<?php
    require_once("../obj/Disc.php");
    require_once("../obj/File.php");

	if(!(isset($_POST['discid']) && isset($_POST['fid']) && isset($_POST['permid']))) {
		http_response_code(400);
		exit;
	}

	try {
		$disc = new Disc($_POST['discid'], $_POST['permid']);
        $f = new File((int)$_POST['fid'], $_POST['permid']);
        if($disc->GetDiscId() != $f->GetDiscId()) 
            throw new Exception("File is not on the current disc");

        if($f->IsDir()) {
            throw new Exception("Editing function doesn't work on folders");
        }
        
        $file_content = $f->ReadBinaryData();
        if(is_null($file_content) ) {
            $file_content = "";
        }

        if(strlen($file_content) >= (1024*1024*1024*2-1)) {
            
            echo json_encode(array("error"=>"File exceeds 2GB"));
            http_response_code(400);
            exit;
        }

        echo json_encode(array("content" => $file_content, "dummydata"=>"dummydata", "filename" => $f->GetFilename()));
        http_response_code(200);
		exit;
	} catch (Exception $e) {
        http_response_code(400);
        echo json_encode(array("error" => $e->getMessage()));
		exit;
	}

?>