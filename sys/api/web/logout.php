<?php
    if(session_status() == PHP_SESSION_NONE) session_start();

    if(isset($_POST['member'])) {
        ob_start();
        setcookie("uid", "-1", time()-3600, '/') or die("");
        setcookie("per", "-1", time()-3600, '/') or die("");
        setcookie("did", "-1", time()-3600, '/') or die("");
        ob_end_flush();
        if(isset($_SESSION['uid'])) unset($_SESSION['uid']);
        if(isset($_SESSION['per'])) unset($_SESSION['per']);
        if(isset($_SESSION['did'])) unset($_SESSION['did']);

        http_response_code(200);
        exit;

    } else if (isset($_POST['guest'])) {
        ob_start();
		setcookie("guest", "-1", time()-3600, '/') or die(""); 
		setcookie("own", "-1", time()-3600, '/') or die(""); 
        ob_end_flush();

        http_response_code(200);
        exit;
    }
    else {
        http_response_code(400);
        exit;
    }
    