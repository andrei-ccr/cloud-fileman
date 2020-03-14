<?php

declare(strict_types=1);

require_once("Connection.php");

class User extends Connection {

    private string $email;
    private string $password;
    private bool $loggedAsGuest;

    //TODO: Make these private and create getters
    public string $permission_id;
    public int $disc_id;
    public int $user_id;

    protected const GUEST_USER = "##GUEST";

    public function __construct(string $email, string $password) {
        Connection::__construct(DEBUG_SERVER);
        $this->email = $email;
        $this->password = $password;
        $this->loggedAsGuest = false;
        if($email != User::GUEST_USER) {
            $this->LoginAsMember();
        } else {
            $this->LoginAsGuest();
        }
    }



    public function LoginAsGuest() : void {
        try {
           $stmt = $this->conn->prepare("INSERT INTO discs(name, temporary, visibility) VALUES('__Temp', 1, 'public')");
		    $stmt->execute();
            $guest_disc_id = (int)$this->conn->lastInsertId();

            $this->loggedAsGuest = true;
            $this->disc_id = $guest_disc_id;
            $this->permission_id = $this->GeneratePermId(); 

        } catch (Exception $e) {
            $this->loggedAsGuest = false;
            $this->disc_id = 0;
            $this->permission_id = "";

            throw new InsertGuestException();
        }
        
    }

    /** 
     * This function is meant to remove any disc and file associated with this guest
     * 
     */
    public function LogoutGuest() : void {
        if($this->loggedAsGuest === FALSE) { return; }

    }

    public function LoginAsMember() {
        if($this->loggedAsGuest) return; //TODO: Make transition from guest to member

        $passHash = hash("sha256", $this->password);
        
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users u LEFT JOIN discs_users du ON du.user_id=u.id WHERE u.email=:email AND u.password=:pass");
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":pass", $passHash);
            $stmt->execute();
            if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->user_id = (int)$row['user_id'];
                $this->disc_id = (int)$row['disc_id'];
                $this->permission_id = $this->GeneratePermId(); //Generate new permission id for this login
            } else {
                $this->user_id = 0;
                $this->disc_id = 0;
                $this->permission_id = "";
                throw new MemberNotFoundException("Row fetch returned false");
            }
        } catch (Exception $e) {
            $this->user_id = 0;
            $this->disc_id = 0;
            $this->permission_id = "";
            throw new MemberNotFoundException("Prepared statement threw an exception");
        }

        try {
            //Put the new permission id in the database
            $stmt2 = $this->conn->prepare("UPDATE discs SET permission_id=:permid WHERE id=:discid");
            $stmt2->bindParam(":permid", $this->permission_id);
            $stmt2->bindParam(":discid", $this->disc_id);
            $stmt2->execute();
        } catch (Exception $e) {
            $this->user_id = 0;
            $this->disc_id = 0;
            $this->permission_id = "";
            throw new PutPermissionException();
        }         
    }


    function RandomStr() : string {
        $str = "";
        $arr = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9');
        
        for($i=0;$i<64;$i++) {
            $str .= $arr[mt_rand(0, count($arr)-1)];
        }
        return $str;
    }

    function GeneratePermId() : string {
        $permid = $this->RandomStr() . time();
        return $permid;
    }

}

?>