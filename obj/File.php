<?php
	declare(strict_types=1);
	if(session_status() == PHP_SESSION_NONE) session_start();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/obj/Connection.php");
	require_once("Disc.php");
	

	class File extends Connection {
		protected $fid; //File id
		
		private $filename = null; //Original filename
		private $keyname = null; //Name of stored file on server
		private $isDir = false; //True if file is a directory
		private $filesize = 0; //Size of the file or total size of directory

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
				$stmt = $this->conn->prepare("SELECT key_name, name, isDir, size FROM files WHERE id=:id");
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
		
	}

?>