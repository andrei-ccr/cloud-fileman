<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	require_once("sys/api/web/validatelogin.php");
?>

<?php
	header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
	header("Pragma: no-cache"); // HTTP 1.0.
	header("Expires: 0"); // Proxies.
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<?php 
		if($isLoggedIn) {
			$pageTitle = "Cloud Files";
			require_once("inc/head-disc-page.php");
			
		} else {
			$pageTitle = "Cloud Files";
			require_once("inc/head-auth-page.php");
		}
	?>
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