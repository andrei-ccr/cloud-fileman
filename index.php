<?php 
	require_once("obj/Disc.php");
	$isLoggedIn = true;
	$disc = null;

	if(isset($_COOKIE['guest'])) {
		
		//Guest's disc id is invalid
		if(!is_numeric($_COOKIE['guest'])) {
			setcookie("guest", "-1", time()-3600, '/') or die("");
			$isLoggedIn = false;
		}
		try {
			$disc = new Disc($_COOKIE['guest']);
			if($disc->temporary == false) {
				//This is not a valid guest disc.
				setcookie("guest", "-1", time()-3600, '/') or die("");
				$isLoggedIn = false;
			}
		} catch(Exception $e){
			setcookie("guest", "-1", time()-3600, '/') or die("");
			$isLoggedIn = false;
			$error = $e->getMessage();
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
			$pageTitle = "Disc";
		} else {
			require_once("inc/auth-page-head-includes.php");
			$pageTitle = "File manager in Cloud";
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