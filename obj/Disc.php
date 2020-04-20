<?php
	declare(strict_types=1);
	require_once($_SERVER['DOCUMENT_ROOT'] . "/obj/Connection.php");
	require_once("File.php");

	if(session_status() == PHP_SESSION_NONE) session_start();
	
	define("KB", 1024);
	define("MB", 1024*KB);
	define("GB", 1024*MB);
	define("TB", 1024*GB);

	class Disc extends Connection {

		private $dummyFile = array(
			"id" => 0,
			"name" => "_dummy_",
			"isDir" => false
		);
		
		//Properties
		protected int $discid;
		public bool $temporary;
		public $visibility;
		public string $name;
		public int $maxSpace;
		public $dateCreated;
		
		public function __construct(int $discid) {
			if($discid>0) {
				Connection::__construct(DEBUG_SERVER);
				$stmt = $this->conn->prepare("SELECT * FROM discs WHERE id=:id LIMIT 1");
				$stmt->bindParam(":id", $discid);
				$res = $stmt->execute();

				if($res == false) throw new Exception("Database query failed.");

				$row = $stmt->fetch();
				if($row !== FALSE) {
					$this->discid = (int)$discid;
					$this->temporary = (bool)$row['temporary']; 
					$this->visibility = $row['visibility'];
					$this->name = $row['name'];
					$this->maxSpace = (int)$row['space'];
					$this->dateCreated = $row['date_created'];

					if($this->IsDiscExpired()) {
						throw new Exception("Disc is expired.");
					}
					
				} else {
					throw new Exception("No disc exists with this id.");
				}
			} else {
				throw new Exception("Invalid disc id.");
			}
		}


		/**
		 * Checks if 30 minutes have passed since the creation of this disc. Returns true if it did, false otherwise.
		 * 
		 * @param void
		 *
		 * @return bool Returns true if disc is expired, false otherwise.
		 */ 
		protected function IsDiscExpired() : bool{
			if($this->temporary == false) {
				//This disc is not temporary. It cannot expire.
				return false; 
			}

			if($this->dateCreated <= time() + (60*60*2) - (60*30)) {
				return false; // TODO:This should be true
			} else {
				return false;
			}
		}


		/**
		 * Creates a new empty directory on the disk with the specified name $name and returns the id.
		 *
		 * @param string $name 	The name of the new directory
		 * 
		 * @return int	Returns the id of the directory 
		 *
		 * @throws Exception Throws an Exception if filename is invalid or database query fails
		 */ 
		public function CreateDirectory(string $name, int $cd=0) : int {
			
			if(!Disc::ValidFilename($name)) {
				throw new Exception("Invalid name provided.");
			}

			if($cd != 0) {
				try {
					$file = new File($cd);
					if(!$file->IsDir()) throw new Exception();
				} catch (Exception $e) {
					throw new Exception("Current directory id is not a valid directory.");
				}
			}
			
			$current_dir_id = $cd;

			$kn = Disc::GenerateKey($name);
			
			$stmt = $this->conn->prepare("INSERT INTO files(name, key_name, isDir, parent_id) VALUES(:name, :keyname, 1, :parid)");
			$stmt->bindParam(":name", $name);
			$stmt->bindParam(":keyname", $kn);
			$stmt->bindParam(":parid", $current_dir_id);
			$res = $stmt->execute();
			
			if($res == false) {
				throw new Exception($stmt->error);
			}

			$fid = (int)$this->conn->lastInsertId();

			$stmt = $this->conn->prepare("INSERT INTO files_discs(disc_id, file_id) VALUES(:discid, :fid)");
			$stmt->bindParam(":discid", $this->discid);
			$stmt->bindParam(":fid", $fid);
			$res = $stmt->execute();
			
			if($res == false) {
				throw new Exception($stmt->error);
			}

			return (int)$fid;
		}


		/**
		 * Creates a new file with no content with the specified name $name and returns the id.
		 *
		 * @param string $name 	The name of the new file
		 * 
		 * @return int	Returns the id of the file 
		 *
		 * @throws Exception Throws an Exception if filename is invalid or database query fails
		 */ 
		public function CreateFile(string $name, int $cd=0, string $data=null) : int {
			
			if(!Disc::ValidFilename($name)) {
				throw new Exception("Invalid name provided.");
			}

			if($cd != 0) {
				try {
					$file = new File($cd);
					if(!$file->IsDir()) throw new Exception();
				} catch (Exception $e) {
					throw new Exception("Current directory id is not a valid directory.");
				}
			}
			
			$current_dir_id = $cd;
			
			$kn = Disc::GenerateKey($name);
			
			$stmt = $this->conn->prepare("INSERT INTO files(name, key_name, isDir, parent_id, binary_data) VALUES(:name, :keyname, 0, :parid, :bin_data)");
			$stmt->bindParam(":name", $name);
			$stmt->bindParam(":keyname", $kn);
			$stmt->bindParam(":parid", $current_dir_id);
			$stmt->bindParam(":bin_data", $data);
			$res = $stmt->execute();
			
			if($res == false) {
				throw new Exception($stmt->error);
			}

			$fid = (int)$this->conn->lastInsertId();

			$stmt = $this->conn->prepare("INSERT INTO files_discs(disc_id, file_id) VALUES(:discid, :fid)");
			$stmt->bindParam(":discid", $this->discid);
			$stmt->bindParam(":fid", $fid);
			$res = $stmt->execute();
			
			if($res == false) {
				throw new Exception($stmt->error);
			}

			return (int)$fid;
		}

		/**
		 * Uploads a file to the server. 
		 *
		 * @param array $file 	Contains the file as send through Form Data
		 * 
		 * @return void
		 *
		 * @throws Exception Throws an Exception in case of error
		 */ 
		public function UploadFile(array $file, int $cdid=0, bool $uploadLocal=false) : void {
			
			if(!Disc::ValidFilename($file['name'])) {
				throw new Exception("Invalid name provided.",0);
			}

			if($file['error'] > 0) {
				throw new Exception("Upload error. Code: " . $file['error'],1);
			}
			
			if($file['size'] > 1*GB ) {
				throw new Exception("File size exceeds 1GB limit.",2);
			}

			if($this->GetFreeSpace() < $file['size']) {
				throw new Exception("File size exceeds free space limit.",3);
			}
			
			if($cdid != 0) {
				try {
					$f = new File($cdid);
					if(!$f->IsDir()) throw new Exception();
				} catch (Exception $e) {
					throw new Exception("Current directory id is not a valid directory.",4);
				}
			}
			
			$filename = $file['name'];
			$filesize = $file['size'];

			$kn = Disc::GenerateKey($filename);
			$blobdata = file_get_contents($file['tmp_name']);
			
			try {
				$stmt = $this->conn->prepare("INSERT INTO files(name, key_name, size, parent_id, binary_data) VALUES(:name, :kn, :size, :pid, :bindat)");
				$stmt->bindParam(":name", $filename);
				$stmt->bindParam(":kn", $kn);
				$stmt->bindParam(":size", $filesize);
				$stmt->bindParam(":pid", $cdid);
				$stmt->bindParam(":bindat", $blobdata);
				$stmt->execute();

			} catch (Exception $e) {
				throw new Exception("Inserting data into database has failed.",5);
			}

			$uploaded_fid = $this->conn->lastInsertId();
			try {
				$stmt = $this->conn->prepare("INSERT INTO files_discs(disc_id, file_id) VALUES(:did, :fid)");
				$stmt->bindParam(":did", $this->discid);
				$stmt->bindParam(":fid", $uploaded_fid);
				$stmt->execute();

			} catch (Exception $e) {
				throw new Exception("Inserting data into database has failed.",6);
			}
		}


		/**
		 * Reads a directory $id and returns an array of file objects. 
		 *
		 * @param array $id 	The id of the directory
		 * 
		 * @return array 
		 *
		 * @throws Exception Throws an Exception in case of error
		 */ 
		public function ReadDirectory(int $id) : array {
			try {
				$stmt = $this->conn->prepare("SELECT f.id, f.name, f.isDir FROM files f LEFT JOIN files_discs fd ON fd.file_id= f.id WHERE f.parent_id=:parid AND fd.disc_id=:discid");
				$stmt->bindParam(":parid", $id);
				$stmt->bindParam(":discid", $this->discid);
				$stmt->execute();
			} catch (PDOException $e) {
				throw new Exception("Failed to read the files.");
			}
			
			$f = $stmt->fetchAll(PDO::FETCH_ASSOC);

			//Create a dummy hidden file because this folder doens't contain any files
			if(count($f) <= 0) {
				$f = array($this->dummyFile);
			};
			return $f;
		}
		
		public function GetUsedSpace() {
			try {
				$sum = 0;
				$did = $this->discid;
				$stmt = $this->conn->prepare("
					SELECT f.size AS filesize 
					FROM files f 
					LEFT JOIN files_discs fd ON f.id = fd.file_id
					LEFT JOIN discs d ON fd.disc_id = d.id 
					WHERE d.id=:did");
				$stmt->bindParam(":did", $did);
				$stmt->execute();

				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if(count($rows)>0) {
					foreach($rows as $row) {
						$sum += (int)$row['filesize'];
					}
					
				} else {
					//No files on this disc.
					return 0;
				}
			} catch(Exception $e) {
				return -1;
			}

			return (int)$sum;
		}
		
		public function GetFreeSpace() : int {
			$used = (int)$this->GetUsedSpace();
			if($used == -1) return -1;
			
			return $this->maxSpace - $used;
		}

		public function GetDiscId() : int {
			return $this->discid;
		}
		
		public static function FormatBytes(float $bytes, int $prefferedUnit = 0) :array {
			if($bytes < KB) return array($bytes, "B");
			if($bytes < MB) return array(number_format($bytes/KB, 2), "KB");
			if($bytes < GB) return array(number_format($bytes/MB, 2), "MB");
			return array(number_format($bytes/GB, 2), "GB");
		}
		
		private static function RandomStr() : string {
			$str = "";
			$arr = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9');
			
			for($i=0;$i<10;$i++) {
				$str .= $arr[mt_rand(0, count($arr)-1)];
			}
			return $str;
		}

		public static function ValidFilename(string $filename) : bool {
			if(preg_match('/[\/:"*?<>|]/', $filename)) {
				return false;
			}
			if(strlen(trim($filename))<=0) {
				return false;
			}

			if(strlen($filename) > 255) {
				return false;
			}

			return true;
		}
		
		public static function GenerateKey(string $filename) : string {
			$kn = $filename . Disc::RandomStr();
			$kn = hash("sha256", $kn);
			$kn .= "!";
			
			return $kn;
		}

	}


		/*public static function ValidDisk(PDO $conn, int $id) : bool {
			$stmt = $conn->prepare("SELECT email FROM contacts WHERE id=:id");
			$stmt->bindParam(":id", $id);
			$stmt->execute();

			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			if($res) {
				return true;
			} else return false;
		}

		public static function ValidFile(PDO $conn, string $key) : bool {
			$stmt = $conn->prepare("SELECT * FROM files WHERE keyname=:key");
			$stmt->bindParam(":key", $key);
			$stmt->execute();

			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			if($res) {
				return true;
			} else 
				return false;
		}*/
?>