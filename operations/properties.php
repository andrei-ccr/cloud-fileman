<?php
	require_once("../obj/User.php");
	require_once("../obj/Disc.php");
	require_once("../obj/File.php");

	//If handle is not set, exit.
	if(!isset($_GET['h'])) {
		http_response_code(400);
		echo json_encode(array("internalError"=>"No handle."));
		exit;
	}

	try {
		$u = new User($_GET['h']);
	} catch (Exception $e) {
		http_response_code(400);
		echo json_encode(array("internalError"=>"Bad handle."));
		exit;
	}

	if(isset($_GET['fid'])) {
		try {
			//Get file properties
			$file = new File((int)$_GET['fid']);

			if($file->GetDiscId() != $u->disc_id ) {
				throw new Exception("File is not in current disc");
			}

			if($file->IsDir()) {
				$count = $file->GetItemsCount();

				echo json_encode(array("filecount" => $count));
				http_response_code(200);
				exit;
			} else {
				$size = $file->GetSize();
				$size = Disc::FormatBytes($size);
				echo json_encode(array("size" => $size[0], "unit" => $size[1]));
		
				http_response_code(200);
				exit;
			}

		} catch (Exception $e) {
			http_response_code(400);
			echo json_encode(array("internalError"=>$e->getMessage()));
			exit;
		}
	
	} else if(isset($_GET['cdid'])) {
		try {
			$d = new Disc($u->disc_id);

			if($_GET['cdid'] == 0) {
				$maxspace = Disc::FormatBytes($d->maxSpace);
				$freespace = Disc::FormatBytes($d->GetFreeSpace());
				
				echo json_encode(array("freespace" => $freespace, "maxspace" => $maxspace, "filecount" => "0"));
				http_response_code(200);
				exit;
			} else {
				$file = new File((int)$_GET['cdid']);
				if($d->GetDiscId() != $file->GetDiscId()) {
					throw new Exception("Requested dir not on current disc");
				}

				$count = $file->GetItemsCount();
				echo json_encode(array("fileCount" => $count));
				http_response_code(200);
				exit;
			}
		} catch (Exception $e) {
			http_response_code(400);
			echo json_encode(array("internalError"=>$e->getMessage()));
			exit;
		}
		

	} else {
		http_response_code(400);
		echo json_encode(array("internalError"=>"No parameters."));
		exit;
	}


?>