<?php
	/*header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");*/
	
	require_once($_SERVER['DOCUMENT_ROOT'] ."/obj/Connection.php");
	$conn = new Connection();
	
	if(!isset($_POST['email']) || !isset($_POST['pass'])) {
		if(!isset($_POST['guest'])) {
			http_response_code(400);
			exit;
		} else {
			$stmt = $conn->conn->prepare("INSERT INTO discs(name, temporary, visibility) VALUES('__Temp', 1, 'public')");
			$result = $stmt->execute();
			$guest_disc_id = $conn->conn->lastInsertId();
			
			ob_start();
			setcookie("guest", $guest_disc_id, time()+1800, '/') or die(""); // Guest session expires in 30 minutes
			ob_end_flush();

			echo json_encode(array("result" => "guest", "discid" => $guest_disc_id));
			http_response_code(200);
			exit;
		}
	}
	
	
	$email = $_POST['email'];
	$passHash = hash("sha256", $_POST['pass']);
	
	$stmt = $conn->conn->prepare("SELECT * FROM users WHERE email=:email AND password=:pass");
	$stmt->bindParam(":email", $email);
	$stmt->bindParam(":pass", $passHash);
	$res = $stmt->execute();
	if($res === false) {
		http_response_code(400);
		exit;
	}
	
	if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		//Member exists.
		$stmt = $conn->conn->prepare("SELECT * FROM discs_users WHERE user_id=:uid");
		$stmt->bindParam(":uid", $row['id']);
		$res = $stmt->execute();
		if($res === false) {
			http_response_code(400);
			exit;
		}
		$did = ($stmt->fetch(PDO::FETCH_ASSOC))['disc_id'];

		//Website only setup.
		ob_start();
		setcookie("uid", $row["id"], time()+60*60*24*15, '/') or die(""); // Member session expires in 15 days
		setcookie("passh", $row["password"], time()+60*60*24*15, '/') or die(""); // Member session expires in 15 days
		ob_end_flush();
		$_SESSION['permid'] = $row['permission_id'];

		//Return data
		echo json_encode(array("result" => "member", "userid" => $row['id'], "permid" => $row['permission_id'], "discid"=> $did) );
		http_response_code(200);
		exit;
		
	} else {
		//Member doesn't exist. Register account
		echo json_encode(array("result" => "register"));
		http_response_code(200);
		exit;
		
	}
	
	/*
	$res = false;
	if(!$contact->IsLoggedIn()) {
		$res = $contact->Login($_POST['email'], $_POST['pass']);
	}
	
	echo json_encode(array("result" => $res));*/
?>