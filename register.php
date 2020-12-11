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

	<title>Register &bull; Cloudman File</title>
</head>
<body>
	<?php if(isset($_GET['success'])): ?>
		<div class="box-container">
			<p class="box-container-txt">Your new account has been created. You can now <a href="/">log in.</a></p>
		</div>
	<?php else: ?>
		<div class="box-container" id="register-container">
			<p class="box-container-txt">Register to access your free virtual storage space where you can store files of any kind.</p>
			<input type="text" placeholder="Email" id="email" name="email" style="width:300px;">
			<div style="display:block;"></div>
			<input type="text" placeholder="Confirm Email" id="cemail" name="cemail" style="width:300px;">
			<div style="display:block; font-size: 20px; color: #0f7dcc;">&diamond;</div>
			<input type="password" placeholder="Password" id="pass" name="pass" style="width:300px;">
			<div style="display:block; "></div>
			<input type="password" placeholder="Confirm Password" id="cpass" name="cpass" style="width:300px;">
			<div style="display:block;"></div>
			<button id="btn-register" style="margin: 10px 20px; width:320px;">Register</button>
			<p class="box-container-txt" style="width: 300px; font-size: 12px; text-align: center; margin: auto; line-height: 20px;">I read and I agree to the Terms of Service and Privacy Policy including Cookie Policy</p> 
			<div style="display:block; margin:20px;"></div>
			<p class="box-container-txt" style="font-size:14px; color:#555; text-align: center;">Already have an account? <a href="/">Log In</a></p>
		</div>
		
		 
	<?php endif;?>
	<footer style="display: block; margin: auto; text-align: center; color: #888; font-size: 12px;">&copy; Cloudman File 2020</footer>
</body>
</html>
