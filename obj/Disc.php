<?php
	declare(strict_types=1);
	require_once("Connection.php");
	require_once("Security.php");
	require_once("File.php");
	
	define("KB", 1024);
	define("MB", 1024*KB);
	define("GB", 1024*MB);
	define("TB", 1024*GB);

	class Disc extends Connection {

		private int $discid;
		private bool $temporary;
		private int $maxSpace;
		private $dateCreated;
		private string $permission_id;
		
		public function __construct(int $discid, string $permission_id) {
			if($discid <= 0) {
				throw new Exception("Invalid negative disc id");
			}

			Connection::__construct();

			$stmt = $this->conn->prepare("SELECT * FROM discs WHERE id=:id AND permission_id=:permid LIMIT 1");
			$stmt->bindValue(":id", $discid);
			$stmt->bindValue(":permid", $permission_id);
			$stmt->execute();

			$row = $stmt->fetch();

			if($row !== FALSE) {

				$this->discid = (int)$discid;
				$this->temporary = (bool)$row['temporary']; 
				$this->maxSpace = (int)$row['space'];
				$this->dateCreated = $row['date_created'];
				$this->permission_id = $permission_id;

				if($this->IsDiscExpired()) {
					throw new Exception("Disc is expired");
				}
				
			} else {
				throw new Exception("No disc exists with this id exists");
			}
		
		}


		/**
		 * Checks if disc is temporary and older than 30 minutes since it was created.
		 * 
		 * @param void
		 *
		 * @return bool Returns true if disc is expired, false otherwise.
		 */ 
		public function IsDiscExpired() : bool{
			if($this->temporary == false) {
				//This disc is not temporary. It cannot expire.
				return false; 
			}
			$dt = new DateTime($this->dateCreated);
			if($dt->getTimestamp() <= (time() + 60*30)) {
				return true; 
			} else {
				return false;
			}
		}


		/**
		 * Searches for one or more files based on the files' content. Currently searches only in filename.
		 * 
		 * @param string $content  The content to search for
		 *
		 * @return bool Returns an array of files or null if nothing is found
		 * 
		 * @throws
		 */ 
		public function IsOnDisc(string $content) {
			try {
				$stmt = $this->conn->prepare("SELECT *, f.id AS fid, f.name AS filename FROM files f LEFT JOIN files_discs fd ON f.id=fd.file_id LEFT JOIN discs d ON fd.disc_id=d.id WHERE d.id=:did AND f.name LIKE :squery ");
				$stmt->bindValue(":did", $this->discid, PDO::PARAM_INT);
				$stmt->bindValue(":squery", '%'. $content . '%');
				$stmt->execute();
			} 
			catch (PDOException $e) {
				throw new Exception("Searching in database failed: " . $e->getMessage());
			}

			$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($files === FALSE) {
				return null;
			}
			if(count($files) == 0) {
				return null;
			}
			return $files;

			
		}


		/**
		 * Creates a new empty directory on the disk with the specified name $name and returns the id.
		 *
		 * @param string $name 	The name of the new directory
		 * 
		 * @param int $cd  The directory in which the new directory will be created
		 * 
		 * @return int	Returns the id of the directory 
		 *
		 * @throws Exception Throws an Exception if filename is invalid or database query fails
		 */ 
		public function CreateDirectory(string $name, int $cd=0) : int {
			
			if(!Disc::ValidFilename($name)) {
				throw new Exception("Invalid name provided");
			}

			if($cd != 0) {
				$file = new File($cd, $this->permission_id);
				if(!$file->IsDir()) throw new Exception("Target is not a directory");
			}

			$kn = Security::GenerateKey($name);
			
			try {
				$stmt = $this->conn->prepare("INSERT INTO files(name, key_name, isDir, parent_id) VALUES(:name, :keyname, 1, :parid)");
				$stmt->bindValue(":name", $name);
				$stmt->bindValue(":keyname", $kn);
				$stmt->bindValue(":parid", $cd);
				$stmt->execute();
			} 
			catch (PDOException $e) {
				throw new Exception("Couldn't create new directory: " . $e->getMessage());
			}
			
			$fid = (int)$this->conn->lastInsertId();

			try {
				$stmt = $this->conn->prepare("INSERT INTO files_discs(disc_id, file_id) VALUES(:discid, :fid)");
				$stmt->bindValue(":discid", $this->discid);
				$stmt->bindValue(":fid", $fid);
				$stmt->execute();
			}
			catch (PDOException $e) {
				throw new Exception("Couldn't create new directory: " . $e->getMessage());
			}
			
			//TODO: Make only one query

			return (int)$fid;
		}


		/**
		 * Creates a new file with no content with the specified name $name and returns the id.
		 *
		 * @param string $name 	The name of the new file
		 * 
		 * @param int $cd	The directory in which the new file will be created
		 * 
		 * @param string [$data]   Optional. Data to be inserted into the file.
		 * 
		 * @return int	Returns the id of the file 
		 *
		 * @throws Exception Throws an Exception if filename is invalid or database query fails
		 */ 
		public function CreateFile(string $name, int $cd=0, string $data=null) : int {
			
			if(!Disc::ValidFilename($name)) {
				throw new Exception("Invalid name provided");
			}

			if($cd != 0) {
				$file = new File($cd, $this->permission_id);
				if(!$file->IsDir()) throw new Exception("Target is not a directory");
			}
			
			$kn = Security::GenerateKey($name);
			
			try {
				$stmt = $this->conn->prepare("INSERT INTO files(name, key_name, isDir, parent_id, binary_data) VALUES(:name, :keyname, 0, :parid, :bin_data)");
				$stmt->bindValue(":name", $name);
				$stmt->bindValue(":keyname", $kn);
				$stmt->bindValue(":parid", $cd);
				$stmt->bindValue(":bin_data", $data);
				$stmt->execute();
			}
			catch (PDOException $e) {
				throw new Exception($e->getMessage());
			}

			$fid = (int)$this->conn->lastInsertId();

			try {
				$stmt = $this->conn->prepare("INSERT INTO files_discs(disc_id, file_id) VALUES(:discid, :fid)");
				$stmt->bindValue(":discid", $this->discid);
				$stmt->bindValue(":fid", $fid);
				$stmt->execute();
			}
			catch (PDOException $e) {
				throw new Exception($e->getMessage());
			}

			return (int)$fid;
		}

		/**
		 * Uploads a file to the server. 
		 *
		 * @param array $file 	Contains the file as send through Form Data
		 * 
		 * @param int $target_dir	Id of the directory where the files will be uploaded
		 * 
		 * @return void
		 *
		 * @throws Exception Throws an Exception in case of error
		 */ 
		public function UploadFile(array $file, int $target_dir=0) : void {
			
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
			
			if($target_dir != 0) {
				$f = new File((int)$target_dir, $this->permission_id);
				if($f->IsDir() === false) throw new Exception("Target id doesn't point to a directory", 4);
			}
			
			$filename = $file['name'];
			$filesize = $file['size'];

			$kn = Security::GenerateKey($filename);
			$blobdata = file_get_contents($file['tmp_name']);
			
			try {
				$stmt = $this->conn->prepare("INSERT INTO files(name, key_name, size, parent_id, binary_data) VALUES(:name, :kn, :size, :pid, :bindat)");
				$stmt->bindValue(":name", $filename);
				$stmt->bindValue(":kn", $kn);
				$stmt->bindValue(":size", $filesize);
				$stmt->bindValue(":pid", $target_dir);
				$stmt->bindValue(":bindat", $blobdata);
				$stmt->execute();
				
			} 
			catch (PDOException $e) {
				throw new Exception("Inserting data into database has failed.",5);
			}

			$uploaded_fid = $this->conn->lastInsertId();
			
			try {
				$stmt = $this->conn->prepare("INSERT INTO files_discs(disc_id, file_id) VALUES(:did, :fid)");
				$stmt->bindValue(":did", $this->discid);
				$stmt->bindValue(":fid", $uploaded_fid);
				$stmt->execute();

			} 
			catch (PDOException $e) {
				throw new Exception("Inserting data into database has failed.",6);
			}
		}


		/**
		 * Reads a directory $id and returns an array of file objects. 
		 *
		 * @param array $id 	The id of the directory to read
		 * 
		 * @return array 
		 *
		 * @throws Exception Throws an Exception in case of error
		 */ 
		public function ReadDirectory(int $id) : array {
			try {
				$stmt = $this->conn->prepare("SELECT f.id, f.name, f.isDir FROM files f LEFT JOIN files_discs fd ON fd.file_id= f.id WHERE f.parent_id=:parid AND fd.disc_id=:discid ORDER BY f.isDir DESC");
				$stmt->bindValue(":parid", $id);
				$stmt->bindValue(":discid", $this->discid);
				$stmt->execute();
			} catch (PDOException $e) {
				throw new Exception("Failed to read the files.");
			}
			
			$f = $stmt->fetchAll(PDO::FETCH_ASSOC);

			//Create a dummy hidden file because this folder doesn't contain any files
			if(count($f) <= 0) {
				$f = array($this->GetDummyFile());
			};
			return $f;
		}
		

		/**
		 * Calculates the used storage space. 
		 * 
		 * @return int Returns -1 in case of error or how many bytes are used 
		 * 
		 */ 
		public function GetUsedSpace() {
			$sum = 0;
			$did = $this->discid;

			try {
				$stmt = $this->conn->prepare("
					SELECT f.size AS filesize 
					FROM files f 
					LEFT JOIN files_discs fd ON f.id = fd.file_id
					LEFT JOIN discs d ON fd.disc_id = d.id 
					WHERE d.id=:did");
				$stmt->bindValue(":did", $did);
				$stmt->execute();

			} catch(PDOException $e) {
				return -1;
			}
			
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($rows !== FALSE) {
				foreach($rows as $row) {
					$sum += (int)$row['filesize'];
				}
			} else {
				//No files on this disc.
				return 0;
			}

			return (int)$sum;
		}
		

		/**
		 * Calculates the free storage space. 
		 * 
		 * @return int Returns -1 in case of error or how many bytes are free.
		 * 
		 */ 
		public function GetFreeSpace() : int {
			$used = (int)$this->GetUsedSpace();
			if($used == -1) return -1;
			
			return $this->maxSpace - $used;
		}

		private function GetDummyFile() : array {
			return array(
				"id" => 0,
				"name" => "_dummy_",
				"isDir" => false,
				"key_name" => "null"
			);
		} 

		public function GetDiscId() : int {
			return $this->discid;
		}

		public function GetDateCreated() {
			return $this->dateCreated;
		}

		public function IsTemporary() : bool {
			return $this->temporary;
		}

		public function GetMaxSpace() : int {
			return $this->maxSpace;
		}
		

		public static function FormatBytes(float $bytes, int $prefferedUnit = 0) :array {
			if($bytes < KB) return array($bytes, "B");
			if($bytes < MB) return array(number_format($bytes/KB, 2), "KB");
			if($bytes < GB) return array(number_format($bytes/MB, 2), "MB");
			return array(number_format($bytes/GB, 2), "GB");
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
		

	}
?>