<?php
	declare(strict_types=1);
	require_once("Exceptions.php");
	
	class Connection {
		public $conn = null;
		
		public function __construct() {
			$this->conn = null;
			$cleardb_url = parse_url(getenv("CLEARDB_DATABASE_URL"));
			
			$credentials = array( 
				"username" => $cleardb_url["user"], 
				"password" => $cleardb_url["pass"], 
				"host" => $cleardb_url["host"], 
				"db" => substr($cleardb_url["path"],1) 
			);
			
			try {
				$this->conn = new PDO("mysql:host=" . $credentials['host'] . ";dbname=" . $credentials['db'] . ";charset=utf8" , $credentials['username'], $credentials['password']);
				$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} 
			catch(PDOException $e) {
				$this->conn = null;
				throw new PDOException("Database connection error: " . $e->getMessage());
			}
			catch(Exception $e) {
				$this->conn = null;
				throw new Exception("Internal connection error: " . $e->getMessage());
			}
		}
	}

?>