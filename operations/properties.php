<?php
	session_start();
	require_once("../obj/Disc.php");
	require_once("../obj/File.php");

	

	if(isset($_GET['fid'])) {
		//Get file properties
		try {
			$file = new File((int)$_GET['fid']);
			$size = $file->GetSize();
			if(isset($_GET['format'])) {
				$size = Disc::FormatBytes($size);
				echo json_encode(array("size" => $size[0], "unit" => $size[1]));
			} else {
				echo json_encode(array("size" => $size));
			}
			
			http_response_code(200);
			
		} catch (Exception $e) {
			http_response_code(400);
			exit;
		}
	
		exit;
	} else {
		if(isset($_GET['cdid'])) {
			//Get current directory properties
			if($_GET['cdid'] == 0) {
				$maxspace = Disc::FormatBytes(MAX_SPACE);
				$freespace = DiskSize::FormatBytes(DiskSize::GetFreeSpace($contact->conn, $contact->GetEmail()));
				
				echo json_encode(array("freespace" => $freespace, "maxspace" => $maxspace));
			}
		} else {
			http_response_code(400);
			exit;
		}
	}

	if(!isset($_GET['discid'])) {
		http_response_code(400);
		exit;
	}

?>