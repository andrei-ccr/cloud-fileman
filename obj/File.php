<?php
	declare(strict_types=1);
	if(session_status() == PHP_SESSION_NONE) session_start();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/obj/Connection.php");
	require_once("Disc.php");
	

	class File extends Connection {
		protected $fid; //File id
		//protected $email;
		
		private $filename = null; //Original filename
		private $keyname = null; //Name of stored file on server
		private $isDir = false; //True if file is a directory
		private $filesize = 0; //Size of the file or total size of directory
		
		//private $infav = false;
		//private $parent = null;
		//private $type = null;

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
		
		
		public function RestoreResource() : void {
			if($this->fid <= 0) throw new Exception("No resource loaded");
			
			//Note: For directories, there's no need to restore subfolders and subfiles as they are not marked as trash in the first place. 
			$stmt = $this->conn->prepare("UPDATE metadata SET intrash=0 WHERE keyname=:kn");
			$stmt->bindParam(":kn", $this->keyname);
			$result = $stmt->execute();
			if(!$result) {
				throw new Exception($stmt->error);
			}
			
		}
		
		
		public function Delete() : bool {
			if($this->fid <= 0) throw new Exception("No resource loaded");
			
			//Remove entry from database
			$stmt = $this->conn->prepare("DELETE FROM files WHERE key_name=:kn");
			$stmt->bindParam(":kn", $this->keyname);
			$result = $stmt->execute();
			if(!$result) {
				throw new Exception($stmt->error);
				return false;
			}
			
			//TODO: Handle directory deletion 
			
			
			//Delete physical file from server (if it's not a directory)
			if($this->isDir == false) {
				$result = unlink($_SERVER['DOCUMENT_ROOT'] ."/disk/uploads/" . $this->keyname);
				if($result == false) {
					//Physical file deletion failed
					return false;
				}
				return true;
				
			} else {
				//File deleted succesfully
				return true;
			}
		}
		public function SendToTrash($trashid = 0) : void {
			/*if($this->fid <= 0) throw new Exception("No resource loaded");
			
			//Note: For directories, don't send every child folder/file in the trash. 
			$stmt = $this->conn->prepare("DELETE FROM files WHERE key_name=:kn");
			$stmt->bindParam(":kn", $this->keyname);
			$result = $stmt->execute();
			if(!$result) {
				throw new Exception($stmt->error);
			}
			
			$result = unlink($_SERVER['DOCUMENT_ROOT'] ."/disk/uploads/" . $this->keyname);*/
		}
		
		public function DeleteResource() : void {
			/*if($this->fid <= 0) throw new Exception("No resource loaded");

			$fserver = new FileServerGoogle(BUCKET_NAME);

			if($this->IsFile()) {
				if(DiskResource::IsLocal($this->keyname)) {
					$res = unlink($_SERVER['DOCUMENT_ROOT'] ."/disk/uploads/" . $this->keyname);
					if(!$res) {
						throw new Exception("Unlink failed");
					}
				} else {
					$res = $fserver->Delete($this->keyname);
					if(!$res) {
						throw new Exception("Unlinking on file server failed");
					}
				}

				$stmt = $this->conn->prepare("DELETE FROM files WHERE keyname=:kn");
				$stmt->bindParam(":kn", $this->keyname);
				$res = $stmt->execute();
				if(!$res) {
					throw new Exception("Dropping keyname from table failed : " . $stmt->error);
				}

			} else if($this->IsDir()) {
				
				//Get all files inside the directory. Any subdirectories will be also inspected for files
				$childfiles = array();
				$inspectlist = array($this->fid);
				
				while(count($inspectlist)) {
					$nextid = array_pop($inspectlist);
					try {
						$stmt = $this->conn->prepare("SELECT f.id AS fid, m.type AS tp, f.keyname AS kn FROM files AS f, metadata AS m WHERE f.parent=:id AND f.email=:email AND m.keyname=f.keyname");
						$stmt->bindParam(":email", $this->email);
						$stmt->bindParam(":id", $nextid);
						$stmt->execute();
						$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
					} catch (Exception $e) {
						throw new Exception("Error retrieving subfile from table : " . $e->GetError());
					}
					
					foreach($res as $f) {
						if($f['tp'] == ResourceType::File) {
							array_push($childfiles, $f['kn']);
						} else {
							array_unshift($inspectlist, $f['fid']);
							array_push($childfiles, $f['kn']);
						}
					}
				}

				//Remove the subfiles & subfolders
				if(count($childfiles)>0) {

					//Unlink subfiles & subfolders
					foreach($childfiles AS $cf) {
						if(DiskResource::IsLocal($cf)) {
							$res = unlink($_SERVER['DOCUMENT_ROOT'] ."/disk/uploads/" . $cf);
							if(!$res) {
								throw new Exception("Unlink failed");
							}
						} else {
							$res = $fserver->Delete($cf);
							if(!$res) {
								throw new Exception("Unlinking on file server failed");
							}
						}
					}

					//Remove them from the db
					$childfilesstr = implode(",", $childfiles);
					$stmt = $this->conn->prepare("DELETE FROM files WHERE keyname IN(".$childfilesstr.")");
					$result = $stmt->execute();
					if(!$result) {
						throw new Exception("Failed to drop subfile from table : " . $stmt->error);
					}
				}

				//Remove this directory as well
				$stmt = $this->conn->prepare("DELETE FROM files WHERE keyname=:kn");
				$stmt->bindParam(":kn", $this->keyname);
				$res = $stmt->execute();
				if(!$res) throw new Exception("Failed to drop directory from table : " . $stmt->error);

			}*/
		}

		/*public static function IsLocalS(string $keyname) : bool {
			return ($keyname[strlen($keyname)-1] == "!");
		}

		public function IsLocal() : bool {
			if($this->fid <= 0) throw new Exception("No resource loaded");
			return ($this->keyname[strlen($this->keyname)-1] == "!");
		}*/


		//Getters
		
		/*public function GetParent() : ?int {
			if($this->fid <= 0) throw new Exception("No resource loaded");
		
			return $this->parent;
		}*/

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


<?php

		/*public function GetType() : ?string {
			return $this->type;
		}*/

		/*public function GetFileBody() : string {
			$fserver = new FileServerGoogle(BUCKET_NAME);
			$filebodyres = $fserver->Download($this->keyname);
			if(is_null($filebodyres)) {
				throw new Exception($fserver->GetError());
			}
			return $filebodyres;
		}*/
		
		/*public function ToggleFav() : void {
			if($this->fid <= 0) throw new Exception("No resource loaded");

			try {
				$stmt = $this->conn->prepare("UPDATE metadata SET infav=IF(infav=1, 0, 1) WHERE keyname=:kn");
				$stmt->bindParam(":kn", $this->keyname);
				$stmt->execute();
			} catch(Exception $e) {
				throw new Exception($stmt->errorInfo[2]);
			}
			
		}*/

		/*public function Share(int $option) {
			if($this->fid <= 0) throw new Exception("No resource loaded");

			return true;
		}*/
?>