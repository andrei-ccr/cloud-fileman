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
	<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>

	<link href="https://fonts.googleapis.com/css?family=Roboto:400,500" rel="stylesheet">

	<link rel="stylesheet" type="text/css" href="css/style-page-disc.css?v=1">

	<script script type="module" src="js/index-settings.js"></script>

	<title>Account</title>
	
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
		background: #f3f3f3;
    width: 100%;
    padding: 45px;
    display: grid;
    grid-column-gap: 40px;
    grid-row-gap: 40px;
    grid-template-columns: repeat( auto-fit, minmax(250px, 1fr) );
	}
	div.settings-side-bar a:hover {
		font-weight: 500;
	}

	div.settings-side-bar a.tab-selected {
		font-weight: 500;
    	color: #2a52c5;
	}

	div.settings-container > .info-box {
		display: inline-block;
    box-shadow: 0px 0px 4px #ddd;
    padding: 10px;
    background: #fff;
	}
	div.settings-container > .info-box > h4 {
		margin: 0;
		margin-bottom: 15px;
		font-weight: 300;
		color: #8e8e8e;
		font-size: 18px;
	}

	h3 {
		font-weight:300;
		margin: 30px 0px;
    margin-top: 60px;

	}

	div.edit-text-input {
		margin: 15px;
		display:inline-block;
	}

	div.edit-text-input > label {
		font-size: 13px;
    	color: #757575;
    	display: block;
	}

	div.edit-text-input > input {
		border: 0;
   	 	border-bottom: 1px solid #999;
    	margin: 10px;
    	margin-left: 0;
    	outline: 0;
	}

	.settings-container button {
		background: #0869ff;
		border: 0px solid #d4d4d4;
		padding: 9px 12px;
		border-radius: 5px;
		color: white;
		font-size: 12px;
		font-weight: 500;
		font-family: 'Roboto', sans-serif;
		cursor: pointer;
		margin: 15px;
	}

	/* The switch - the box around the slider */
.switch {
	position: relative;
    display: inline-block;
    width: 50px;
    height: 21px;
}

/* Hide default HTML checkbox */
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

/* The slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
  border-radius: 34px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 15px;
    width: 15px;
    left: 4px;
    bottom: 3px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

label.checkbox-label {
	vertical-align: middle;
    color: #555;
    font-size: 15px;
    margin-right: 15px;
}
</style>
<body>
	<div class="settings-page" style="width: 100%; height: 100%; display:flex;">
		<div class="settings-side-bar" style="    width: 250px;
    background: #ffffff;
    height: 100%;
    border-right: 1px solid #c3c3c3;">
			<span style="background: #0869ff; text-align: center; display: block; color: #fff; padding: 10px; font-size:13px; font-weight: 500;">Account</span>
			<a class="settings-tab s-tab-account">Dashboard</a>
			<a class="settings-tab s-tab-fileman">Settings</a>
			<a class="settings-tab s-tab-back">Back to Files</a>
		</div>
		<div class="settings-container">
		
		</div>
	</div>
	
</body>
</html>