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
			require_once("inc/head-disc-page.php");
			$pageTitle = "Cloud Files";
		} else {
			require_once("inc/head-auth-page.php");
			$pageTitle = "Cloud Files";
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