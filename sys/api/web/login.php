<?php
    if(session_status() == PHP_SESSION_NONE) session_start();

    if(isset($_POST['uid']) && isset($_POST['did']) && isset($_POST['per'])) {

        if(isset($_POST['remember'])) {
            ob_start();
            setcookie("uid", $_POST['uid'], time()+60*60*24*15, '/') or die("");
            setcookie("per", $_POST['per'], time()+60*60*24*15, '/') or die("");
            setcookie("did", $_POST['did'], time()+60*60*24*15, '/') or die("");
            ob_end_flush();
        } else {
            $_SESSION['uid'] = $_POST['uid'];
            $_SESSION['per'] = $_POST['per'];
            $_SESSION['did'] = $_POST['did'];
        }

        http_response_code(200);
        exit;
		
    }

    else if(isset($_POST['gdid']) && isset($_POST['gperid'])) {

		ob_start();
		setcookie("guest", $_POST['gdid'], time()+1800, '/') or die(""); 
		setcookie("own", $_POST['gperid'], time()+1800, '/') or die(""); 
        ob_end_flush();
        
        http_response_code(200);
        exit;
    }

    else {
        http_response_code(400);
        exit;
    }

?>