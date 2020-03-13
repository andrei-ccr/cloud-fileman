<?php

declare(strict_types=1);

require_once("User.php");
class Guest extends User {
    public function __construct() {
        User::__construct(User::GUEST_USER, "");
    }
}