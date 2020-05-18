<?php

require_once("User.php");

/**
 * 
 * Helper class to initialize a guest user.
 *  
 */
class Guest extends User {
    public function __construct() {
        User::__construct(User::GUEST_USER, "");
    }
}