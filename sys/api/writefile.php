<?php
    require_once("../obj/Disc.php");
    require_once("../obj/File.php");

	if(!(isset($_POST['discid']) && isset($_POST['fid']) && isset($_POST['content']) && isset($_POST['permid']))) {
		echo json_encode(array("error" => "Required vars not set"));
		http_response_code(400);
		exit;
    }
    

	try {

		$disc = new Disc($_POST['discid'], $_POST['permid']);
        $f = new File((int)$_POST['fid'], $_POST['permid'] );

        if($disc->GetDiscId() != $f->GetDiscId()) 
            throw new Exception("File is not on the current disc");

		if($disc->GetFreeSpace() + $f->GetSize() < strlen($_POST['content'])) {
			throw new Exception("File size exceeds free space limit.",3);
		}
		
        $f->WriteBinaryData($_POST['content']);

        echo json_encode(array("success" => true));
        http_response_code(200);
        exit;
        
	} catch (Exception $e) {
		echo json_encode(array("error" => $e->getMessage()));
		http_response_code(400);
		exit;
	}

?>