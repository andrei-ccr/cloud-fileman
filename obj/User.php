<?php

class User extends Connection {

    public function __construct() {
        Connection::__construct(DEBUG_SERVER);
        LoginAsGuest();
    }

    public function __construct(string $email, string $password) {
        Connection::__construct(DEBUG_SERVER);
        LoginAsMember($email, $password);
    }

    public function LoginAsGuest() : int {
        $stmt = $this->conn->prepare("INSERT INTO discs(name, temporary, visibility) VALUES('__Temp', 1, 'public')");
		$stmt->execute();
        $guest_disc_id = $conn->conn->lastInsertId();
        return $guest_disc_id;
    }

    public function LoginAsMember(string $email, string $password) {

        $passHash = hash("sha256", $password);
        
        $stmt = $conn->conn->prepare("SELECT * FROM users u LEFT JOIN discs_users du ON du.user_id=u.id WHERE u.email=:email AND u.password=:pass");
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":pass", $passHash);
        $stmt->execute();
        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $did = ($stmt->fetch(PDO::FETCH_ASSOC))['disc_id'];
        }
    }

}

?>