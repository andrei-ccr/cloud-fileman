<?php
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT'] . "/obj/Contact.php");
	require_once($_SERVER['DOCUMENT_ROOT'] . "/obj/UserSettings.php");
	require_once("../obj/DiskResource.php");
	
	$contact = new Contact();
	
	if(!$contact->IsLoggedIn()) {
		die();
	}

	$email = $contact->GetEmail();

	$diskres = new DiskResource($email);
	$uset = new UserSettings($email);
	
	if(isset($_POST['files_view'])) {
		if($_POST['files_view'] == 0) {
			//Tiles
			$uset->SetFilesView(FilesView::Tiles);
			
		} else if($_POST['files_view'] == 1) {
			//Icons
			$uset->SetFilesView(FilesView::Icons);
			
		} else if($_POST['files_view'] == 2) {
			//Big Thumbs
			$uset->SetFilesView(FilesView::BThumbs);
			
		}
	} else if(isset($_POST['fav'])) {
		if(isset($_POST['fid'])) {
			try {
				$diskres->LoadResource($_POST['fid']);
				$diskres->ToggleFav();
			} catch(Exception $e) {
				die("Error: failed to favorite.");
			}
			
		}
	} else if(isset($_POST['share'])) {
		if(isset($_POST['fid'])) {
			$diskres->LoadResource($_POST['fid']);
			//$diskres->Share(ShareOption::World);
		}
	}
?>