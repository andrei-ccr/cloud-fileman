<?php 
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	require_once("sys/api/web/validatelogin.php");

	if($isLoggedIn) {
		header("Location:/");
		exit;
	}
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
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#55C1EC"/>
	<link rel="icon" href="favicon.png">

	<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script defer src="js/jquery.mobile.custom.min.js"></script>

	<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500" rel="stylesheet">

	<link rel="stylesheet" type="text/css" href="css/style-page-login.css?v=1">

	<script script type="module" src="js/index-auth.js"></script>

	<title>Register - Cloud Fileman</title>
</head>
<body>
	<?php if(isset($_GET['success'])): ?>
		<div class="box-container">
			<p class="box-container-txt">A new account has been created. You can now <a href="/">log in</a></p>
		</div>
	<?php else: ?>
		<div class="box-container" id="register-container">
			<p class="box-container-txt">Complete the form below to create a new account</p>
			<input type="text" placeholder="Email" id="email" name="email">
			<input type="text" placeholder="Confirm Email" id="cemail" name="cemail">
			<input type="text" placeholder="Username (optional)" id="username" name="username"><br>
			<input type="password" placeholder="Password" id="pass" name="pass">
			<input type="password" placeholder="Confirm Password" id="cpass" name="cpass">
			<button id="btn-register" style="margin: 10px 20px;">Register</button>
		</div>
		<div class="box-container" id="register-container">
			<p class="box-container-txt">Already have an account? <a href="/">Login</a></p>
		</div>
	<?php endif;?>
</body>
</html>
