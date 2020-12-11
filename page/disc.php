<?php
	//Set current path to root
	$_SESSION['dir_list'] = array();
	$_SESSION['cdid'] = 0;
	
	//Calculate free space
	$maxspace = Disc::FormatBytes($disc->GetMaxSpace());
	$freespace = Disc::FormatBytes($disc->GetFreeSpace());

	$fvclass = "list-view";
	//$fvclass = "icons-view";
	
?>

<script type="text/javascript">
	var UploadProgressTrackId = "<?php echo ini_get("session.upload_progress.name"); ?>";
</script>

<form method="post" id="file-up-form" style="width:0; position:absolute;" enctype="multipart/form-data"><input type="file" style="width:0;" id="file-up" multiple></form>

<div id="bar">
	<div id="logo" style="display: flex; align-items: center; cursor: pointer;">
	<img src="/logo.png" style="width: 40px; margin-left: 10px;">
</div>
	
	
	<div class="search-area">
		<i class="fas fa-search"></i>
		<input type="text" placeholder="Search Files" id="search-bar" style="background: #fbfbfb;">
	</div>
	

</div>
<div id="second-bar">
	<a class="sbar-btn cm-upload"><i class="fas fa-upload"></i> Upload File(s)</a>
	<a class="sbar-btn cm-new-folder"><i class="fas fa-folder"></i> New Folder</a>
	<a class="sbar-btn cm-new-file"><i class="fas fa-file"></i> New File</a>
</div>

<div id="main">
	
	<div class="side-bar">
		<div class="profile-area">
			<i class="fas fa-user"></i>
			<span id="profile-name"><?php echo $username; ?></span>
			<a class="cm-logout" style="display: inline-block;
font-size: 12px;
margin: 0px 5px;
padding: 5px; cursor: pointer"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
			<!--<div class="fa-ellipsis-v" id="profile-pic"></div>-->
		</div>

		<p style="font-size: 1.4rem;color: #777;padding: 1rem 15px;margin-bottom: 3px;">Storage space:</p>
		<p class="storage-space" style="font-size: 1.5rem;padding: 2px 15px;color: #555;margin: 3px 0px;"><?php echo $freespace[0] . $freespace[1]; ?> free</p>
		
		<div class="side-bar-menu">
			<a class="nav-btn selected cm-nav-files"><i class="fas fa-hdd"></i> Files</a>
			<a class="nav-btn cm-nav-starred"><i class="far fa-star"></i> Starred</a>
			<a class="nav-btn cm-nav-shared"><i class="fas fa-user-friends"></i> Shared</a>
			<a class="nav-btn cm-nav-recent"><i class="fas fa-history"></i> Recent</a>
			<a class="nav-btn cm-nav-deleted"><i class="fas fa-trash"></i> Deleted</a>
		</div>
	</div>
	
	<div class="file-container <?= $fvclass; ?>" id="file-listing">
		<div id="path-bar">
			<span id="root-location"><!--<i class="fas fa-cloud"></i>-->Home</span>
		</div>
		
		<div id="details-header"><span>Filename</span><span>Last modified</span><span>Size</span></div>
		<span class="msg">Loading files...</span>
	</div>
</div>


<!--<div id="info-bar">

	<div class="disc-info" style="margin-left: 10px; padding: 5px;">
		<i class="fas fa-hdd" style="color: #5f5f5f;
    vertical-align: middle;"></i>
		<div id="memory" style="display:inline-block; vertical-align:middle;">
			<span id="n"><?php echo "Free: " . $freespace[0] ." ". $freespace[1] . " of " . $maxspace[0] ." ". $maxspace[1]; ?></span>
		</div>
	</div>
				
	<div class="file-info noselect" >
		<span id="fileicon"></span>
		<span id="filename" class="noselect" style="display:inline-block;"></span>
		<span id="filetype" class="noselect" style="color: #464646; display: inline-block; font-size: 13px;"></span>
		<span id="filesize" class="noselect" style="color:#5f5f5f; display:inline-block;"></span>
	</div>
	
</div>-->



<div style="display:none;" id="dinfo" data-hdl="<?php echo $handle; ?>" data-cd="<?php echo $_SESSION['cdid'];?>" data-did="<?php echo $disc->GetDiscId();?>"></div>

<script type="module">
	import { ReadCurrentDirectory } from '../js/modules/files.js';
	(function() {
		ReadCurrentDirectory();
	})();
</script>