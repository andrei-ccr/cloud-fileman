<?php 
	require_once("operations/web/validatelogin.php");
?>

<?php
	if(!$isLoggedIn) {
		header("Location: /");
		exit;
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="theme-color" content="#55C1EC"/>
	<link rel="icon" href="favicon.png">

	<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<!--<script defer src="js/jquery.mobile.custom.min.js"></script>-->

	<link href="https://fonts.googleapis.com/css?family=Roboto:400,500" rel="stylesheet">

	<link rel="stylesheet" type="text/css" href="css/style-page-disc.css?v=1">

	<script script type="module" src="js/index-settings.js"></script>

	<title>Settings</title>
	
</head>
<style>
	div.settings-side-bar a {
		color: #656565;
		font-size: 15px;
		display: block;
		padding: 16px;
		font-weight: 400;
		cursor: pointer;
	}

	div.settings-container {
		background: #fff;
		width: 100%;
		padding: 45px;
	}
	div.settings-side-bar a:hover {
		font-weight: 500;
	}

	div.settings-side-bar a.tab-selected {
		font-weight: 500;
    	color: #2a52c5;
	}
</style>
<body>
	<div class="settings-page" style="width: 100%; height: 100%; display:flex;">
		<div class="settings-side-bar" style="width: 250px; background: #eaeaea; height: 100%; border-right:1px solid #c3c3c3;">
			<span style="background: #2a52c5; text-align: center; display: block; color: #fff; padding: 10px; font-size:13px; font-weight: 500;">Settings</span>
			<a class="settings-tab s-tab-account">Account</a>
			<a class="settings-tab s-tab-fileman">File Manager</a>
			<a class="settings-tab s-tab-about">About</a>
			<a class="settings-tab s-tab-back">Back to your files</a>
		</div>
		<div class="settings-container">
		
		</div>
	</div>
	
</body>
</html>