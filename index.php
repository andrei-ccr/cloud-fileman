<?php 
	require_once("operations/web/validatelogin.php");
?>

<?php
	header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
	header("Pragma: no-cache"); // HTTP 1.0.
	header("Expires: 0"); // Proxies.
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