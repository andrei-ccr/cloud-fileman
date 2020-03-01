<?php
	if(!isset($_POST['s'])) {
		die();
	}

	$setting = $_POST['s'];
	if(strpos($setting, "_change-view_") !== false) {
		$s = 1;
	} else if(strpos($setting,"_about_") !== false) {
		$s = 2;
	} else {
		die();
	}

	include("version.php");
	global $version;

	switch($s) {
		case 1:
			$title = "Change View";
			$body = '<label for="setting-files-view">Files view:</label>
						<select name="setting-files-view" id="setting-files-view">
							<option value="fv-tiles">Tiles</option>
							<option value="fv-icons">Icons</option>
						</select>
					<button id="setting-save">Save</button>';
			break;
		case 2:
			$title = "About Disk";
			$body = '<span>Version '. $version . '</span>
					<span>Copyright &copy; Miracle Ability 2018</span>';
			break;
		default:
			die();
	}

?>


<div class="modal-background">
	<div class="modal">
		<h3><?= $title; ?></h3>
		<div class="body">
			<?= $body; ?>
		</div>
	</div>
</div>