<?php
declare(strict_types=1);

require_once("Connection.php");
require_once("Security.php");

class User extends Connection {

    private ?string $email;
    private ?string $password;
    private ?bool $loggedAsGuest;
    private ?string $permission_id;
    private int $disc_id;
    private int $user_id;

    protected const GUEST_USER = "##GUEST";

    public function __construct() {
        Connection::__construct();

        if (method_exists($this,$f='__construct'.func_num_args())) {
            call_user_func_array(array($this,$f),func_get_args());
        } else {
            throw new Exception("Cannot instantiate object");
        }

    }

    //Handle is permission id
    public function __construct1(string $handle) {

        try {

            $stmt = $this->conn->prepare("SELECT * FROM discs d LEFT JOIN discs_users ud ON d.id=ud.disc_id LEFT JOIN users u ON ud.user_id = u.id WHERE d.permission_id=:handle");
            $stmt->bindValue(":handle", $handle);
            $stmt->execute();

        } catch (PDOException $e) {
            throw new Exception("Error on setting data ".$e->getMessage());
        }

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            if($row["name"] == "__Temp") {
                $this->email = User::GUEST_USER;
                $this->loggedAsGuest = true;
                $this->permission_id = $handle;
                $this->disc_id = (int)$row['id'];
            } 
            else {
                $this->email = $row['email'];
                $this->loggedAsGuest = false;
                $this->permission_id = $handle;
                $this->disc_id = (int)$row['disc_id'];
                $this->user_id = (int)$row['user_id'];
            }
        } else {
            throw new Exception("Couldn't fetch any record with this handle");
        }
    }

    public function __construct2(string $email, string $password) {
        
        $this->email = $email;
        $this->password = Security::PasswordHashFunction($password);
        $this->loggedAsGuest = false;

        if($email != User::GUEST_USER) {
            $this->LoginAsMember();
        } else {
            $this->LoginAsGuest();
        }
    }

    public function LoginAsMember() {
        if($this->loggedAsGuest) return; //TODO: Make transition from guest to member
        if((trim($this->email) == "") || (trim($this->password)=="")) return;
        
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users u LEFT JOIN discs_users du ON du.user_id=u.id WHERE u.email=:email AND u.password=:pass");
            $stmt->bindValue(":email", $this->email);
            $stmt->bindValue(":pass", $this->password);
            $stmt->execute();
        } 
        catch (PDOException $e) {
            $this->deconstruct();
            throw new MemberNotFoundException("Prepared statement threw an exception");
        }

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->user_id = (int)$row['user_id'];
            $this->disc_id = (int)$row['disc_id'];
            $this->permission_id = Security::GeneratePermId(); //Generate new permission id for this login
        } else {
            $this->deconstruct();
            throw new MemberNotFoundException("Row fetch returned false");
        }

        try {
            //Put the new permission id in the database
            $stmt2 = $this->conn->prepare("UPDATE discs SET permission_id=:permid WHERE id=:discid");
            $stmt2->bindParam(":permid", $this->permission_id);
            $stmt2->bindParam(":discid", $this->disc_id);
            $stmt2->execute();

        } catch (PDOException $e) {
            $this->deconstruct();
            throw new PutPermissionException();
        }         
    }

    public function LoginAsGuest() : void {
        
        $new_temp_permid = Security::GeneratePermId();
        try {
            $stmt = $this->conn->prepare("INSERT INTO discs(name, temporary, visibility, permission_id) VALUES('__Temp', 1, 'public', :permid)");
            $stmt->bindParam(":permid", $new_temp_permid);
            $stmt->execute();
        }
        catch (PDOException $e) {
            $this->deconstruct();
            throw new InsertGuestException();
        }

        $guest_disc_id = (int)$this->conn->lastInsertId();
        if($guest_disc_id == 0) throw new Exception("Something went wrong while inserting new guest");

        $this->loggedAsGuest = true;
        $this->disc_id = $guest_disc_id;
        $this->permission_id = $new_temp_permid;

    }

    /** 
     * Remove any disc and file associated if the
     * user is a guest or erase the permission id if the user is a member.
     * 
     */
    public function Logout() : void {
     
        try {
            if($this->loggedAsGuest == true) {
                $stmt = $this->conn->prepare("DELETE d, fd, f FROM discs d LEFT JOIN files_discs fd ON fd.disc_id = d.id LEFT JOIN files f ON fd.file_id = f.id WHERE d.id=:disc_id");
                $stmt->bindValue(":disc_id", $this->disc_id);
                $stmt->execute();
            } else {
                $stmt = $this->conn->prepare("UPDATE discs SET permission_id=NULL WHERE id=:disc_id");
                $stmt->bindValue(":disc_id", $this->disc_id);
                $stmt->execute();
            }

        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        } finally {
            $this->deconstruct();
        }

    }



    public function GetEmail(bool $hideDomain = false) : string {
        $emailPieces = explode("@", $this->email);
        if($hideDomain) {
            return $emailPieces[0];
        } else {
            return $this->email;
        }
    }

    public function GetPermissionId() : string {
        return $this->permission_id;
    }

    public function GetUserId() : int {
        return $this->user_id;
    }

    public function GetDiscId() : int {
        return (int)$this->disc_id;
    }

    function Deconstruct() :void {
        $this->email = null;
        $this->loggedAsGuest = null;
        $this->permission_id = null;
        $this->disc_id = 0;
        $this->user_id = 0;
    }

    public static function RegisterUser(string $email, string $password) : void {
        $conn = new Connection();
        $p = Security::PasswordHashFunction($password);

        try {
            $stmt = $conn->conn->prepare("INSERT INTO users(email, password) VALUES (:email, :pass)");
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":pass", $p);
            $stmt->execute();
        }
        catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
        
    }

}

?>