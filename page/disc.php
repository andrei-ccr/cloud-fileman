<?php
	//Set current path to root
	$_SESSION['dir_list'] = array();
	$_SESSION['cdid'] = 0;
	
	//Calculate free space
	$maxspace = Disc::FormatBytes($disc->GetMaxSpace());
	$freespace = Disc::FormatBytes($disc->GetFreeSpace());

	$fvclass = "icons-view";
	
	
?>

<script type="text/javascript">
	var UploadProgressTrackId = "<?php echo ini_get("session.upload_progress.name"); ?>";
</script>

<form method="post" id="file-up-form" style="width:0; position:absolute;" enctype="multipart/form-data"><input type="file" style="width:0;" id="file-up" multiple></form>

<div id="bar">
	<div id="logo" style="display: flex; align-items: center; cursor: pointer;"><span style="color: black; font-size:16px; font-weight:500;">LOGO</span></div>
	
	
	<div class="search-area">
		<i class="fas fa-search"></i>
		<input type="text" placeholder="Search Files" id="search-bar" style="background: #fbfbfb;">
	</div>
	

</div>

<div id="file-zone">
	
	
	<div class="side-bar" style="position: relative; top: 5.6rem; border-right: 1px solid #ddd; background-color: #fefefe;">
		<div class="profile-area" style="margin: 1rem 15px;">
			<i class="fas fa-user"></i>
			<span id="profile-name"><?php echo $username; ?></span>
			<!--<div class="fa-ellipsis-v" id="profile-pic"></div>-->
			
		</div>
		<p style="font-size: 1.4rem;color: #777;padding: 1rem 15px;margin-bottom: 3px;">Storage space:</p>
		<p style="font-size: 1.5rem;padding: 2px 15px;color: #555;margin: 3px 0px;"><?php echo $freespace[0] . $freespace[1]; ?> free</p>
		<div style="text-align: center; margin-top: 15px;border-top: 1px solid #ccc;padding: 15px;">
			<button class="cm-upload"><i class="fas fa-upload"></i> Upload File(s)</button>
			<a class="sbar-btn cm-new-folder"><i class="fas fa-folder"></i> New Folder</a>
			<a class="sbar-btn cm-new-file"><i class="fas fa-file"></i> New File</a>
			<a class="sbar-btn cm-logout"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
		</div>
	</div>
	
	<div class="listing-container <?= $fvclass; ?>" id="file-listing">
	<div id="path-bar">
		<span id="root-location"><!--<i class="fas fa-cloud"></i>-->Home</span>
	</div>
	<span class="msg">Loading files...</span></div>
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

<div class="modal">
	<div class="container">
		<div style="margin-bottom: 5rem;"><svg class='svg-icon' viewBox='0 0 20 20' style='width: 3rem; height: 3rem;'><path d='M17.927,5.828h-4.41l-1.929-1.961c-0.078-0.079-0.186-0.125-0.297-0.125H4.159c-0.229,0-0.417,0.188-0.417,0.417v1.669H2.073c-0.229,0-0.417,0.188-0.417,0.417v9.596c0,0.229,0.188,0.417,0.417,0.417h15.854c0.229,0,0.417-0.188,0.417-0.417V6.245C18.344,6.016,18.156,5.828,17.927,5.828 M4.577,4.577h6.539l1.231,1.251h-7.77V4.577z M17.51,15.424H2.491V6.663H17.51V15.424z' style='fill:#555;'></path></svg><h2 style="font-size: 1.7rem;
font-weight: 500; margin: 0;">File name</h2></div>
		
		<div style="display: grid; grid-template-columns: 1fr 1fr;">
			<span class="prop-title">Size: </span><span class="prop-value">0.00 bytes</span>
			<span class="prop-title">Uploaded/created: </span><span class="prop-value">02.12.2020 20:16</span>
			<span class="prop-title">Last modified: </span><span class="prop-value">02.12.2020 20:16</span>
			<span class="prop-title">Last accessed: </span><span class="prop-value">02.12.2020 20:16</span>
			<span class="prop-title">Shared: </span><span class="prop-value">No</span>
			<span class="prop-title">Stared: </span><span class="prop-value">No</span>
		</div>
		
		<button class="close-modal" style="margin-top:2rem; background-color:white; color:#0f7dcc; font-size: 1.5rem; border:1px solid #0f7dcc;">Close</button>
	</div>
</div>

<div style="display:none;" id="dinfo" data-hdl="<?php echo $handle; ?>" data-cd="<?php echo $_SESSION['cdid'];?>" data-did="<?php echo $disc->GetDiscId();?>"></div>

<script type="module">
	import { ReadCurrentDirectory } from '../js/modules/files.js';
	(function() {
		ReadCurrentDirectory();
	})();
</script>