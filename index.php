<?php 
	require_once("obj/Disc.php");
	require_once("obj/User.php");

	if(session_status() == PHP_SESSION_NONE) session_start();

	$isLoggedIn = true;
	$disc = null;
	$discid = -1;
	$username = "Neinregistrat";

	if(isset($_COOKIE['guest']) && isset($_COOKIE['own'])) {
		//Guest's disc id is invalid
		if(!is_numeric($_COOKIE['guest'])) {
			setcookie("guest", "-1", time()-3600, '/') or die("");
			setcookie("own", "-1", time()-3600, '/') or die("");
			$isLoggedIn = false;
		}
		try {
			$disc = new Disc($_COOKIE['guest']);
			if($disc->temporary == false) {
				//This is not a valid guest disc.
				setcookie("guest", "-1", time()-3600, '/') or die("");
				setcookie("own", "-1", time()-3600, '/') or die("");
				$isLoggedIn = false;
			}

			$user = new User($_COOKIE['own']);
			$handle = $user->permission_id;
		} catch(Exception $e){
			setcookie("guest", "-1", time()-3600, '/') or die("");
			setcookie("own", "-1", time()-3600, '/') or die("");
			$isLoggedIn = false;
			$error = $e->getMessage();
		}

		if($isLoggedIn) {
			$discid = $_COOKIE['guest'];
		}
		
	} else if(isset($_COOKIE['uid']) && isset($_COOKIE['per']) && isset($_COOKIE['did'])) {

		try {
			$disc = new Disc($_COOKIE['did']);
			if($disc->temporary == true) {
				//This is not a valid member disc.
				setcookie("uid", "-1", time()-3600, '/') or die("");
				setcookie("per", "-1", time()-3600, '/') or die("");
				setcookie("did", "-1", time()-3600, '/') or die("");
				$isLoggedIn = false;
			}

			$user = new User($_COOKIE['per']);
			$handle = $user->permission_id;
		} catch(Exception $e){
			setcookie("uid", "-1", time()-3600, '/') or die("");
			setcookie("per", "-1", time()-3600, '/') or die("");
			setcookie("did", "-1", time()-3600, '/') or die("");
			$isLoggedIn = false;
			$error = $e->getMessage();
		}
		if($isLoggedIn) {
			$discid = $_COOKIE['did'];
			$username = "Membru";
		}
		
	} else if(isset($_SESSION['uid']) && isset($_SESSION['per']) && isset($_SESSION['did'])) { 

		try {
			$disc = new Disc($_SESSION['did']);
			if($disc->temporary == true) {
				//This is not a valid member disc.
				unset($_SESSION['uid']);
				unset($_SESSION['per']);
				unset($_SESSION['did']);
				$isLoggedIn = false;
				$error = "Invalid Disc";
			}

			$user = new User($_SESSION['per']);
			$handle = $user->permission_id;
		} catch(Exception $e){
			unset($_SESSION['uid']);
			unset($_SESSION['per']);
			unset($_SESSION['did']);
			$isLoggedIn = false;
			$error = $e->getMessage();
		}
		if($isLoggedIn) {
			$discid = $_SESSION['did'];
			$username = "Membru";
		}
	} else {
		$isLoggedIn = false;
	}
?>

<?php
	header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
	header("Pragma: no-cache"); // HTTP 1.0.
	header("Expires: 0"); // Proxies.
	if(session_status() == PHP_SESSION_NONE) session_start(); //Start session
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<?php 
		if($isLoggedIn) {
			require_once("inc/disc-page-head-includes.php");
			$pageTitle = "Cloud File Manager";
		} else {
			require_once("inc/auth-page-head-includes.php");
			$pageTitle = "Cloud File Manager";
		}
	?>
	
	<title><?php echo $pageTitle;?></title>
	
</head>
<body>
	
	<?php
		if($isLoggedIn) {
			require_once("page/disc.php");
		} else {
			require_once("page/login.php");
		}
	?>
</body>
</html>