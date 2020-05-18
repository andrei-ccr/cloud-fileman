<?php
	require_once("../obj/Disc.php");
	
	if(!(isset($_POST['query']) && isset($_POST['discid']) && isset($_POST['permid']))) {
		http_response_code(400);
		echo json_encode(array("error" => "Query or permission id missing"));
		exit;
    }
	
	try {
		$disc = new Disc((int)$_POST['discid'], $_POST['permid']);
		
        $result = $disc->IsOnDisc($_POST['query']);

        if(is_null($result)) {
            echo json_encode(array("found" => false));
        } else {
            echo json_encode(array("found" => true, "result" => $result));
            //echo json_encode($result);
        }

		http_response_code(200);
		exit;

	} catch (Exception $e) {
		http_response_code(400);
		echo json_encode( array ("error" => $e->getMessage()) );
		exit;
	}

?>