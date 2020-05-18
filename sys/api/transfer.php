<?php
	require_once("../obj/File.php");
	
	if(!(isset($_POST['source_fid']) && isset($_POST['destination_folder']) && isset($_POST['transfer_op']) && isset($_POST['permid']))) {
		http_response_code(400);
		echo json_encode(array("error" => "Must provide target file and destination and transfer operation"));
		exit;
    }
    
    if( ((int)$_POST['transfer_op'] != 1) && ((int)$_POST['transfer_op'] != 2) ) {
        http_response_code(400);
		echo json_encode(array("error" => "Invalid transfer operation"));
		exit;
    }
	
	try {
		$file = new File((int)$_POST['source_fid'], $_POST['permid']);
		
        if((int)$_POST['transfer_op'] == 1) {
            $file->Copy($_POST['destination_folder']);
        } else if ((int)$_POST['transfer_op'] == 2) {
            $file->Move($_POST['destination_folder']);
        }

		http_response_code(200);
		echo json_encode(array("success" => true));

	} catch (Exception $e) {
		http_response_code(400);
		echo json_encode( array ("error" => $e->getMessage()) );
		exit;
	}

	

?>