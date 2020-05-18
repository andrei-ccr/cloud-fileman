<?php
    require_once("../obj/User.php");

    if(!isset($_POST['user']) && !isset($_POST['pwd']) && !isset($_POST['s'])) {
        http_response_code(400);
        exit;
    }

    try {
        $us = new UserSettings($_POST['user'], $_POST['pwd']);
        $setting_function_name = $_POST['s'];

        $func = (isset($_POST['v']))?"Set":"Get";
        $func.= $setting_function_name;

        if(isset($_POST['v'])) {
            $us->$func($_POST['v']);
        }
        else
            echo $us->$func();
        
        http_response_code(200);
        exit;

    } catch (Exception $e) {
        http_response_code(400);
        exit;
    }

?>