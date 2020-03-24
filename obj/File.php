<?php
	declare(strict_types=1);
	if(session_status() == PHP_SESSION_NONE) session_start();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/obj/Connection.php");
	require_once("Disc.php");
	

	class File extends Connection {
		protected $fid; //File id
		
		private $filename = null; //Original filename
		private $keyname = null; //Unique identifier for file
		private $isDir = false; //True if file is a directory
		private $filesize = 0; //Size of the file or total size of directory
		private int $disc = 0; //The disc where this file is located on

		public function __construct(int $fid) {
			Connection::__construct(DEBUG_SERVER);

			if($fid <= 0) {
				throw new Exception("Invalid resource id.");
			}
			else {
				$this->LoadResource($fid);
			}
		}
		
		private function LoadResource(int $fid) : void {
			try {
				$stmt = $this->conn->prepare("SELECT * FROM files f LEFT JOIN files_discs fd ON fd.file_id=f.id WHERE f.id=:id");
				$stmt->bindParam(":id", $fid);
				$stmt->execute();

			} catch (Exception $e) {
				throw new Exception($e->GetError());
			}
			
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			if(count($res)>0) {
				$this->fid = $fid;
				$this->keyname = $res['key_name'];
				$this->filename = $res['name'];
				$this->isDir = $res['isDir'];
				$this->size = $res['size'];
				$this->disc = (int)$res['disc_id'];
			} else {
				throw new Exception("No resource with this id exists.");
			}

		}

		public function IsFile() : bool {
			return $this->isDir == false;
		}
		
		public function IsDir() : bool {
			return $this->isDir == true;
		}

		public function Rename(string $newname) : void {
			if(!Disc::ValidFilename($newname)) {
				throw new Exception("Invalid filename provided.");
			}

			$stmt = $this->conn->prepare("UPDATE files SET name=:fn WHERE key_name=:kn");
			$stmt->bindParam(":fn", $newname);
			$stmt->bindParam(":kn", $this->keyname);
			$res = $stmt->execute();
			
			if(!$res) {
				throw new Exception($stmt->error);
			}

			$this->filename = $newname;
		}

		
		public function Delete() : void {
			if($this->fid <= 0) throw new Exception("No resource loaded");
			
			try {
				if($this->isDir == true) {
					$stmt = $this->conn->prepare("
					delete from files where id IN ( 
						with recursive parents as ( 
							select * from files 
							where key_name=:kn 
							union 
							select f.* from files as f, parents as p 
							where f.parent_id=p.id 
						) select id from parents 
					);");

					$stmt->bindParam(":kn", $this->keyname);
					$stmt->execute();
				} else {
					$stmt = $this->conn->prepare("DELETE FROM files WHERE key_name=:kn");
					$stmt->bindParam(":kn", $this->keyname);
					$stmt->execute();
				}
			} catch (Exception $e) {
				throw new Exception($e->getMessage());
			}
			
		}

		public function GetFilename() : ?string {
			if($this->fid <= 0) throw new Exception("No file is loaded.");
			
			return $this->filename;
		}

		public function GetKeyname() : ?string {
			if($this->fid <= 0) throw new Exception("No file is loaded.");
			return $this->keyname;
		}

		public function GetSize() : int {
			if($this->isDir) {
				return 0; //Don't calculate size for directories
			}

			return $this->filesize;
		}

		public function GetDiscId() : int {
			return $this->disc;
		}

		public function GetBinaryData() {
			try {
				$stmt = $this->conn->prepare("SELECT binary_data FROM files WHERE key_name=:kn");
				$stmt->bindParam(":kn", $this->keyname);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				return $row["binary_data"];
			} catch (Exception $e) {
				throw new Exception($e->getMessage());
			}
			
		}

		private function _Copy(int $s_fid, int $d_fid) {

			try {
				$stmt = $this->conn->prepare("SELECT * FROM files f LEFT JOIN files_discs fd ON f.id = fd.file_id WHERE f.id=:fid");
				$stmt->bindParam(":fid", $s_fid);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
				$d = new Disc((int)$row['disc_id']);

				if((bool)$row['isDir'] === true) {
					$new_folder_id = $d->CreateDirectory($row['name'], $d_fid);
	
					//Get all files from inside this directory
					$stmt = $this->conn->prepare("
						select * from files
						where parent_id=:pid"
					);
					$stmt->bindParam(":pid", $s_fid);
					$stmt->execute();
					$fileRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
					//Copy each file from this directory to the new directory
					foreach($fileRows as $fileRow) {
						$this->_Copy((int)$fileRow['id'], $new_folder_id);
					}

				} else {
					$d->CreateFile($row['name'], $d_fid, $row['binary_data']);
				}
			} catch (Exception $e) {
				throw new Exception($e->getMessage());
			}
			
		}

		public function Copy(int $folder_id) : void {
			try {
				$stmt = $this->conn->prepare("SELECT * FROM files WHERE key_name=:kn");
				$stmt->bindParam(":kn", $this->keyname);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);

				$d = new Disc($this->disc);

				//Copy location is the same as current location. Prepend a "Copy -" to filename
				$filename = ( ($folder_id == $row['parent_id'] ) ?"Copy - ":"") . $this->filename;

				//Copy the file to the new directory
				if($this->isDir) {

					//Copy directory with its contents to new location
					$new_folder_id = $d->CreateDirectory($filename, $folder_id);
					
					$stmt = $this->conn->prepare("
						select * from files
						where parent_id=:pid"
					);
					$stmt->bindParam(":pid", $this->fid);
					$stmt->execute();
					$fileRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					foreach($fileRows as $fileRow) {
						$this->_Copy((int)$fileRow['id'], $new_folder_id);
					}
					
				}
				else {
					//Copy file to new location
					$d->CreateFile($filename, $folder_id, $this->GetBinaryData());
				}
				

			} catch (Exception $e) {
				throw new Exception($e->getMessage());
			}
		}


		public function Move(int $folder_id) : void {
			try {
				$stmt = $this->conn->prepare("SELECT * FROM files WHERE key_name=:kn");
				$stmt->bindParam(":kn", $this->keyname);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);

				//Move location is the same as current location. Don't do anything
				if($folder_id == (int)$row['parent_id']) {
					return;
				} 

				//Check if target folder is on the same disc as source file
				$stmt = $this->conn->prepare("SELECT fd.disc_id AS did FROM files f LEFT JOIN files_discs fd ON fd.file_id=f.id WHERE f.id=:id");
				$stmt->bindParam(":id", $folder_id);
				$stmt->execute();
				$row_discid = $stmt->fetch(PDO::FETCH_ASSOC);
				if((int)$row_discid["did"] != (int)$this->disc) {
					throw new Exception("Target is not on the same disc");
				}

				//Move the file to the new directory
				$stmt = $this->conn->prepare("UPDATE files SET parent_id=:newparent WHERE key_name=:kn");
				$stmt->bindParam(":kn", $this->keyname);
				$stmt->bindParam(":newparent", $folder_id);
				$stmt->execute();

			} catch (Exception $e) {
				throw new Exception($e->getMessage());
			}
		}
		
	}

?>