<?php
	declare(strict_types=1);
	
	require_once("Connection.php");
	require_once("Disc.php");
	
	class File extends Connection {

		private int $fid; //File id
		private ?string $filename = null; //Original filename
		private ?string $keyname = null; //Unique identifier for file
		private bool $isDir = false; //True if file is a directory
		private int $filesize = 0; //Size of the file in bytes
		private int $discid = 0; //The disc id where this file is located on
		private string $permission_id = "";

		public function __construct($identifier, string $permission_id) {

			Connection::__construct();

			if(gettype($identifier) == "integer") {

				if($identifier <= 0) {
					throw new Exception("Invalid negative file id");
				}

				$res = $this->LoadResourceFromId((int)$identifier, $permission_id);

			} 
			else if(gettype($identifier) == "string") {
				$res = $this->LoadResourceFromKey((string)$identifier, $permission_id);
			} 
			else {
				throw new Exception("Invalid identifier type");
			}

			$this->fid = (int)$res['id'];
			$this->keyname = $res['key_name'];
			$this->filename = $res['filename'];
			$this->isDir = (bool)$res['isDir'];
			$this->filesize = (int)$res['size'];
			$this->discid = (int)$res['disc_id'];
			$this->permission_id = $permission_id;

		}
		
		private function LoadResourceFromId(int $fid, string $permission_id) : array {

			try {
				$stmt = $this->conn->prepare("SELECT *, f.name AS filename FROM files f LEFT JOIN files_discs fd ON fd.file_id=f.id LEFT JOIN discs d ON d.id = fd.disc_id WHERE f.id=:id AND d.permission_id=:permid");
				$stmt->bindValue(":id", $fid);
				$stmt->bindValue(":permid", $permission_id);
				$stmt->execute();
			} 
			catch (PDOException $e) {
				throw new Exception("Couldn't load file from database: " . $e->getMessage(), 0);
			}
			
			$res = $stmt->fetch(PDO::FETCH_ASSOC);

			if($res === FALSE) {
				throw new Exception("No resource with this id exists", 1);
			} 

			return $res;
		}

		private function LoadResourceFromKey(string $key_name, string $permission_id) : array {

			try {
				$stmt = $this->conn->prepare("SELECT * FROM files f LEFT JOIN files_discs fd ON fd.file_id=f.id LEFT JOIN discs d ON d.id = fd.disc_id WHERE f.key_name=:kn AND d.permission_id=:permid");
				$stmt->bindValue(":kn", $key_name);
				$stmt->bindValue(":permid", $permission_id);
				$stmt->execute();
			} 
			catch (PDOException $e) {
				throw new Exception("Couldn't load file from database: " . $e->getMessage(), 2);
			}
			
			$res = $stmt->fetch(PDO::FETCH_ASSOC);

			if($res === FALSE) {
				throw new Exception("No resource with this key exists", 3);
			} 

			return $res;
		}


		public function Rename(string $newname) : void {

			if(!Disc::ValidFilename($newname)) {
				throw new Exception("Invalid filename provided");
			}

			try {
				$stmt = $this->conn->prepare("UPDATE files SET name=:fn WHERE key_name=:kn");
				$stmt->bindValue(":fn", $newname);
				$stmt->bindValue(":kn", $this->keyname);
				$stmt->execute();
				
				$this->filename = $newname;
			} 
			catch(PDOException $e) {
				throw new Exception("Couldn't replace name in database: " . $e->getMessage());
			}

		}
		
		public function Delete() : void {	

			if($this->isDir == true) {
				$query = "
					DELETE FROM files WHERE id IN ( 
						WITH RECURSIVE parents AS ( 
							SELECT * FROM files 
							WHERE key_name=:kn 
							UNION 
							SELECT f.* FROM files AS f, parents AS p 
							WHERE f.parent_id=p.id 
						) SELECT id FROM parents 
					);";
			} 
			else {
				$query = "DELETE FROM files WHERE key_name=:kn";
			}

			try {
				$stmt = $this->conn->prepare($query);
				$stmt->bindValue(":kn", $this->keyname);
				$stmt->execute();
			} 
			catch (PDOException $e) {
				throw new Exception("Couldn't delete from database: " . $e->getMessage());
			}
			
		}

		/**
		 *  
		 * Recursive function called by Copy()
		 *  
		 * @param int $s_fid Id of the source file or folder
		 * 
		 * @param int $d_fid Id of the destination directory
		 * 
		 * @throws 
		 */
		private function _Copy(int $s_fid, int $d_fid) : void {

			try {
				$stmt = $this->conn->prepare("SELECT * FROM files f LEFT JOIN files_discs fd ON f.id = fd.file_id WHERE f.id=:fid");
				$stmt->bindValue(":fid", $s_fid);
				$stmt->execute();
			} 
			catch (PDOException $e) {
				throw new Exception($e->getMessage());
			}

			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if($row === FALSE) throw new Exception("No source file with this id");

			//TODO:New Disc object created every recursive call. Get this once as parameter.
			$d = new Disc((int)$row['disc_id'], $this->permission_id); 

			if((bool)$row['isDir'] === true) {
				$new_folder_id = $d->CreateDirectory($row['name'], $d_fid);
	
				//Get all files from inside this directory
				try {
					$stmt = $this->conn->prepare("select * from files where parent_id=:pid");
					$stmt->bindValue(":pid", $s_fid);
					$stmt->execute();
				}
				catch (PDOException $e) {
					throw new Exception($e->getMessage());
				}

				$fileRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

				//Copy each file from this directory to the new directory
				foreach($fileRows as $fileRow) {
					$this->_Copy((int)$fileRow['id'], $new_folder_id);
				}

			} else {
				$d->CreateFile($row['name'], $d_fid, $row['binary_data']);
			}
			
		}

		/**
		 * Creates a new copy of the file or folder.
		 * 
		 * @param int $folder_id	Destination folder where the file will be copied
		 * 
		 * @throws
		 */
		public function Copy(int $folder_id) : void {

			if(!File::HasFilePermission((int)$folder_id, $this->permission_id) && ($folder_id!=0)) {
				throw new Exception("Target directory is not on the same disc");
			}

			try {
				$stmt = $this->conn->prepare("SELECT * FROM files WHERE key_name=:kn");
				$stmt->bindValue(":kn", $this->keyname);
				$stmt->execute();
			} catch (PDOException $e) {
				throw new Exception($e->getMessage());
			}

			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if($row === FALSE) throw new Exception("Source file not found");

			$d = new Disc((int)$this->discid, $this->permission_id);

			//Copy location is the same as current location. Prepend a "Copy -" to filename
			$filename = ( ($folder_id == $row['parent_id'] ) ?"Copy - ":"") . $this->filename;

			//Copy the file to the new directory
			if((bool)$this->isDir) {

				//Copy directory with its contents to new location
				$new_folder_id = $d->CreateDirectory($filename, $folder_id);
				
				try {
					$stmt = $this->conn->prepare("select * from files where parent_id=:pid");
					$stmt->bindValue(":pid", $this->fid);
					$stmt->execute();
				}
				catch (PDOException $e) {
					throw new Exception($e->getMessage());
				}
				

				$fileRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				foreach($fileRows as $fileRow) {
					$this->_Copy((int)$fileRow['id'], $new_folder_id);
				}
				
			}
			else {
				//Copy file to new location
				$d->CreateFile($filename, $folder_id, $this->ReadBinaryData());
			}
				
		}


		public function Move(int $folder_id) : void {

			if(!File::HasFilePermission((int)$folder_id, $this->permission_id) && ($folder_id!=0)) {
				throw new Exception("Target directory is not on the same disc");
			}

			try {
				$stmt = $this->conn->prepare("SELECT * FROM files WHERE key_name=:kn");
				$stmt->bindParam(":kn", $this->keyname);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);

				//Move location is the same as current location. Don't do anything
				if($folder_id == (int)$row['parent_id']) {
					return;
				} 

				//Move the file to the new directory
				$stmt = $this->conn->prepare("UPDATE files SET parent_id=:newparent WHERE key_name=:kn");
				$stmt->bindParam(":kn", $this->keyname);
				$stmt->bindParam(":newparent", $folder_id);
				$stmt->execute();

			} catch (PDOException $e) {
				throw new Exception($e->getMessage());
			}
		}

		
		public function ItemsCount() : int {
			if($this->isDir == false) throw new Exception("Can't return items count for non-directories");

			try {
				$stmt = $this->conn->prepare("SELECT COUNT(*) AS itemsc FROM files WHERE parent_id=:fid");
				$stmt->bindParam(":fid", $this->fid);
				$stmt->execute();
			} 
			catch (PDOException $e) {
				throw new Exception($e->getMessage());
			}

			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if(count($row) > 0) {
				return (int)$row['itemsc'];
			} else {
				return 0;
			}
			
		}

		public function ReadBinaryData() {

			try {
				$stmt = $this->conn->prepare("SELECT binary_data FROM files WHERE key_name=:kn");
				$stmt->bindValue(":kn", $this->keyname);
				$stmt->execute();
			} 
			catch (PDOException $e) {
				throw new Exception("Couldn't query the database: " . $e->getMessage());
			}

			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if($row === FALSE) throw new Exception("No file to read data from");

			return $row["binary_data"];
			
		}

		public function WriteBinaryData($data) {

			try {
				$stmt = $this->conn->prepare("UPDATE files SET binary_data=:bd WHERE key_name=:kn");
				$stmt->bindParam(":kn", $this->keyname);
				$stmt->bindParam(":bd", $data);
				$stmt->execute();
				
			} catch (PDOException $e) {
				throw new Exception("Couldn't write new data to file in database: " . $e->getMessage());
			}
			
		}

		public function IsFile() : bool {
			return $this->isDir == false;
		}
		
		public function IsDir() : bool {
			return $this->isDir == true;
		}

		public function GetFilename() : ?string {
			return $this->filename;
		}

		public function GetKeyname() : ?string {
			return $this->keyname;
		}

		public function GetSize() : int {
			if($this->isDir) {
				return 0; //Don't calculate size for directories
			}

			return $this->filesize;
		}

		public function GetDiscId() : int {
			return $this->discid;
		}

		public function GetId() : int {
			return $this->id;
		}


		public static function HasFilePermission(int $id, string $permission_id) : bool {
			try {
				$f = new File($id , $permission_id);
			}
			catch(Exception $e) {
				return false;
			}

			return true;
			
		}
		
	}

?>