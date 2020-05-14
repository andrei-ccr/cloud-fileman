<?php
	declare(strict_types=1);
	require_once("Exceptions.php");
	
	class Connection {
		public $conn = null;
		
		public function __construct() {
			$this->conn = null;
			
			$credentials = array( 
				"username" => "admin", 
				"password" => "1", 
				"host" => "localhost", 
				"db" => "cloud_disc" 
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