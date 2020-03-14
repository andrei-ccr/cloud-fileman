<?php
	declare(strict_types=1);
	require_once("Exceptions.php");

	define("DEBUG_SERVER", true);
	
	class Connection {
		public $conn = null;
		
		public function __construct(bool $local = false) {
			$this->conn = null;
			
			$credentials = array( "username" => "root", "password" => "", "host" => "localhost", "db" => "cloud_disc" );
			
			try {
				$this->conn = new PDO("mysql:host=" . $credentials['host'] . ";dbname=" . $credentials['db'], $credentials['username'], $credentials['password']);
				$this->conn->exec("set names utf8");
			} catch(PDOException $exception) {
				$this->conn = null;
				echo "Connection error: " . $exception->getMessage();
			}
		}
	}

?>