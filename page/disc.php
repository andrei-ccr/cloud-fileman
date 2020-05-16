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
	<div id="path-bar" style="display:inline-block;">
		<span id="root-location"><i class="fas fa-cloud"></i></span>
	</div>
	
	<div>
		<input type="text" placeholder="Search..." id="search-bar" style="position: relative;
top: -13px;
left: -13px;
font-size: 12px;">
		<div id="profile-pic">  </div>
		<span id="profile-name"><?php echo $username; ?></span>
		<div id="line-separator"></div> 
		<i class="fas fa-ellipsis-v"></i>
	</div>
	
	<div id="mobile-bar"> <i class="fas fa-ellipsis-v"></i> </div>
	
</div>

<div id="file-zone">
	<div class="listing-container <?= $fvclass; ?>" id="file-listing"><span class="msg">Loading files...</span></div>
</div>

<div id="info-bar">

	<div class="disc-info" style="margin-left: 10px; padding: 5px;">
		<i class="fas fa-hdd" style="color: #5f5f5f;
    vertical-align: middle;"></i>
		<div id="memory" style="display:inline-block; vertical-align:middle;">
			<span id="n"><?php echo "Free: " . $freespace[0] ." ". $freespace[1] . " of " . $maxspace[0] ." ". $maxspace[1]; ?></span>
		</div>
		<?php if($disc->IsTemporary() == true): ?>
			<span id="guest" title="Click to dismiss" style="color:#1b1b1b; cursor:pointer; display: inline-block;"><i class="fas fa-exclamation-triangle"></i> All files will be deleted in 30:00. Login to save.</span>
		<?php endif; ?>
	</div>
				
	<div class="file-info noselect" >
		<span id="fileicon"></span>
		<span id="filename" class="noselect" style="display:inline-block;"></span>
		<span id="filetype" class="noselect" style="color: #464646; display: inline-block; font-size: 13px;"></span>
		<span id="filesize" class="noselect" style="color:#5f5f5f; display:inline-block;"></span>
	</div>
	
	<!-- TODO: Move this as modal -->
	<span id="errors" title="Click to dismiss" style="color:red; font-weight: 500; cursor:pointer; display:inherit;"></span>
</div>

<div style="display:none;" id="dinfo" data-hdl="<?php echo $handle; ?>" data-cd="<?php echo $_SESSION['cdid'];?>" data-did="<?php echo $disc->GetDiscId();?>"></div>

<script type="module">
	import { ReadCurrentDirectory } from '../js/modules/files.js';
	(function() {
		ReadCurrentDirectory();
	})();
</script>