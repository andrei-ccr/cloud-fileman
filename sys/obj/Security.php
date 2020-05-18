<?php
    class Security {

        public static function RandomStr(int $length=10) : string {
			$str = "";
			$arr = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9');
			
			for($i=0;$i<$length;$i++) {
				$str .= $arr[mt_rand(0, count($arr)-1)];
			}
			return $str;
		}

        public static function GenerateKey(string $name) : string {
            $kn = $name . Security::RandomStr();
            $kn = hash("sha256", $kn);
            $kn .= "!";
            
            return $kn;
        }

        public static function PasswordHashFunction($password) : string {
            return password_hash($password, PASSWORD_BCRYPT);
        }

        public static function PasswordVerifyFunction($password, $hash) : bool {
            return password_verify($password, $hash);
        }

        public static function GeneratePermId() : string {
            $permid = Security::RandomStr(64) . time();
            return $permid;
        }
    }
		
?>