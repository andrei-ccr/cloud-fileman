<?php
	/**
	 * The following variables are set when calling this file
	 * 
	 * $isLoggedIn = true/false
	 * $username = The username
	 * $profile_pic = First letter of the username
	 * $disc = A Disc object of the user's disc
	 * $user = A User object representing current user
	 * ($error) = Only if there is an error
	 * 
	 */

	require_once("obj/Disc.php");
	require_once("obj/User.php");

	if(session_status() == PHP_SESSION_NONE) session_start();

	$isLoggedIn = true;
	$username = "Neinregistrat";
	$profile_pic = "?";

	if(isset($_COOKIE['guest']) && isset($_COOKIE['own'])) {

		try {
			$disc = new Disc($_COOKIE['guest']);
			if($disc->temporary == false) {
				throw new Exception("Not a guest disc");
			}

			$user = new User($_COOKIE['own']);
			$handle = $user->permission_id;
			$username = $user->GetEmail(true);

		} catch(Exception $e){
			setcookie("guest", "-1", time()-3600, '/') or die("");
			setcookie("own", "-1", time()-3600, '/') or die("");
			$isLoggedIn = false;
			$error = $e->getMessage();
		}
		
	} else if(isset($_COOKIE['uid']) && isset($_COOKIE['per']) && isset($_COOKIE['did'])) {

		try {
			$disc = new Disc($_COOKIE['did']);
			if($disc->temporary == true) {
				throw new Exception("Not a member disc");
			}

			$user = new User($_COOKIE['per']);
			$handle = $user->permission_id;
			$username = $user->GetEmail(true);
			$profile_pic = strtoupper(substr($username,0,1));

		} catch(Exception $e){
			setcookie("uid", "-1", time()-3600, '/') or die("");
			setcookie("per", "-1", time()-3600, '/') or die("");
			setcookie("did", "-1", time()-3600, '/') or die("");
			$isLoggedIn = false;
			$error = $e->getMessage();
		}
		
	} else if(isset($_SESSION['uid']) && isset($_SESSION['per']) && isset($_SESSION['did'])) { 

		try {
			$disc = new Disc($_SESSION['did']);
			if($disc->temporary == true) {
				throw new Exception("Not a member disc");
			}

			$user = new User($_SESSION['per']);
			$handle = $user->permission_id;
			$username = $user->GetEmail(true);
			$profile_pic = strtoupper(substr($username,0,1));

		} catch(Exception $e){
			unset($_SESSION['uid']);
			unset($_SESSION['per']);
			unset($_SESSION['did']);
			$isLoggedIn = false;
			$error = $e->getMessage();
		}

	} else {
		$isLoggedIn = false;
	}
?>