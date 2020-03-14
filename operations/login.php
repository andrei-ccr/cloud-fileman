<?php
	/*header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");*/
	
	require_once($_SERVER['DOCUMENT_ROOT'] ."/obj/User.php");
	require_once($_SERVER['DOCUMENT_ROOT'] ."/obj/Guest.php");
	
	if(!(isset($_POST['email']) && isset($_POST['pass']))) {
		if(!isset($_POST['guest'])) {
			http_response_code(400);
			exit;
		} else {
			$newGuest = null;
			try {
				$newGuest = new Guest();
				$guest_disc_id = $newGuest->disc_id;
				$guest_permission_id = $newGuest->permission_id;
			} catch (InsertGuestException $e) {
				http_response_code(400);
				exit;
			} catch (Exception $e) {
				http_response_code(400);
				exit;
			}

			echo json_encode(array("result" => "guest", "discid" => $guest_disc_id, "permid" => $guest_permission_id));
			http_response_code(200);
			exit;
		}
	}
	
	$user = null;
	try {
		$user = new User($_POST['email'], $_POST['pass']);
	} catch (MemberNotFoundException $e) {
		http_response_code(400);
		exit;
	} catch (PutPermissionException $e) {
		http_response_code(400);
		exit;
	} catch (Exception $e) {
		http_response_code(400);
		exit;
	}

	echo json_encode(array(
		"result" => "member", 
		"userid" => $user->user_id,
		"permid" => $user->permission_id, 
		"discid"=> $user->disc_id) 
	);

	http_response_code(200);
	exit;

?>