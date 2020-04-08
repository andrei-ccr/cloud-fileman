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

    public static function RegisterUser($email, $password) {
        $conn = new Connection(DEBUG_SERVER);
        $p = User::PasswordHashFunction($password);

        try {
            $stmt = $conn->conn->prepare("INSERT INTO users(email, password) VALUES (:email, :pass)");
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":pass", $p);
            $stmt->execute();
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        
    }

    public function __construct() {
        Connection::__construct(DEBUG_SERVER);

        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this,$f='__construct'.$i)) {
            call_user_func_array(array($this,$f),$a);
        }

    }

    //Handle is permission id
    public function __construct1(string $handle) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM discs d LEFT JOIN discs_users ud ON d.id=ud.disc_id LEFT JOIN users u ON ud.user_id = u.id WHERE d.permission_id=:handle");
            $stmt->bindParam(":handle", $handle);
            $stmt->execute();
            if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if($row["name"] == "__Temp") {
                    $this->email = User::GUEST_USER;
                    $this->password = "";
                    $this->loggedAsGuest = true;
                    $this->permission_id = $handle;
                    $this->disc_id = (int)$row['id'];
                } else {
                    $this->email = $row['email'];
                    $this->password = $row['password'];
                    $this->loggedAsGuest = false;
                    $this->permission_id = $handle;
                    $this->disc_id = (int)$row['disc_id'];
                    $this->user_id = (int)$row['user_id'];
                }
            } else {
                throw new MemberNotFoundException("Couldn't fetch any record with this handle");
            }
        } catch(MemberNotFoundException $e) { 
            throw new MemberNotFoundException($e->getMessage());
        } catch (PDOException $e) {
            throw new MemberNotFoundException("Couldn't execute fetch with this handle. ".$e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Error on setting data ".$e->getMessage());
        }
    }

    public function __construct2(string $email, string $password) {
        
        $this->email = $email;
        $this->password = User::PasswordHashFunction($password);
        $this->loggedAsGuest = false;

        if($email != User::GUEST_USER) {
            $this->LoginAsMember();
        } else {
            $this->LoginAsGuest();
        }
    }


    public function LoginAsGuest() : void {
        try {

            $new_temp_permid = $this->GeneratePermId();

            $stmt = $this->conn->prepare("INSERT INTO discs(name, temporary, visibility, permission_id) VALUES('__Temp', 1, 'public', :permid)");
            $stmt->bindParam(":permid", $new_temp_permid);
            $stmt->execute();
            $guest_disc_id = (int)$this->conn->lastInsertId();

            $this->loggedAsGuest = true;
            $this->disc_id = $guest_disc_id;
            $this->permission_id = $new_temp_permid;

        } catch (Exception $e) {
            $this->loggedAsGuest = false;
            $this->disc_id = 0;
            $this->permission_id = "";

            throw new InsertGuestException();
        }
        
    }

    /** 
     * This function is meant to remove any disc and file associated if the
     * user is a guest or erase the permission id if the user is a member.
     * 
     */
    public function Logout() : void {
    
        try {
            if($this->loggedAsGuest == true) {
                $stmt = $this->conn->prepare("DELETE FROM discs WHERE id=:disc_id");
                $stmt->bindParam(":disc_id", $this->disc_id);
                $stmt->execute();
                //TODO: Remove files on the disc
            } else {
                $stmt = $this->conn->prepare("UPDATE discs SET permission_id=NULL WHERE id=:disc_id");
                $stmt->bindParam(":disc_id", $this->disc_id);
                $stmt->execute();
            }

        } catch (Exception $e) {

        } finally {
            $this->email = "";
            $this->password = "";
            $this->loggedAsGuest = false;
            $this->user_id = 0;
            $this->disc_id = 0;
            $this->permission_id = "";
        }

    }

    public function LoginAsMember() {
        if($this->loggedAsGuest) return; //TODO: Make transition from guest to member
        if((trim($this->email) == "") || (trim($this->password)=="")) return;
        
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users u LEFT JOIN discs_users du ON du.user_id=u.id WHERE u.email=:email AND u.password=:pass");
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":pass", $this->password);
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

    static function PasswordHashFunction($password) : string {
        return hash("sha256", $password);
    }

    public function GetEmail(bool $hideDomain = false) : string {
        $emailPieces = explode("@", $this->email);
        if($hideDomain) {
            return $emailPieces[0];
        } else {
            return $this->email;
        }
    }

}

class UserSettings extends Connection{

    private int $user_id;

    /* Define settings list below */
    private bool $show_context_menu;

    
    public function __construct(int $user_id, string $password) {
        Connection::__construct(DEBUG_SERVER);

        try {
            $stmt = $this->conn->prepare("SELECT * FROM settings WHERE user_id=:uid");
            $stmt->bindParam(":uid", $user_id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row !== false) {
                $this->user_id = (int)$user_id;

                /* Assign settings values below this line */
                $this->show_context_menu = (bool)$row['show_context_menu'];

            }
            
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    /* Add settings getters and setters below */
    
    public function SetShowContextMenu(bool $value) {
        try {
            $stmt = $this->conn->prepare("UPDATE settings SET show_context_menu=:val WHERE user_id=:uid");
            $stmt->bindParam(":uid", $this->user_id);
            $stmt->bindParam(":val", $value, PDO::PARAM_BOOL);
            $stmt->execute();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $this->show_context_menu = (bool)$value;

    }
    public function GetShowContextMenu() : bool {
        return $this->show_context_menu;
    }
}

?>