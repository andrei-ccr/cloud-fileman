<?php
	//Set current path to root
	$_SESSION['dir_list'] = array();
	$_SESSION['cdid'] = 0;
	
	//Calculate free space
	$maxspace = Disc::FormatBytes($disc->maxSpace);
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
	
	<div style="float:right;">
		<div id="profile-pic"> <span id="profile-pic-txt"><?php echo $profile_pic; ?></span> </div>
		<span id="profile-name"><?php echo $username; ?></span>
		<div id="line-separator"></div> 
		<i class="fas fa-upload"></i> 
		<div id="line-separator"></div> 
		<i class="fas fa-ellipsis-v"></i>
	</div>
	
	<div id="mobile-bar"> <i class="fas fa-ellipsis-v"></i> </div>
	
</div>

<div id="file-zone">
	<div class="listing-container <?= $fvclass; ?>" id="file-listing"><span class="msg">Se incarca...</span></div>
</div>

<div id="info-bar">
	<div class="col" id="gen">
		
		<?php if($disc->temporary == true): ?>
			<span id="guest" title="Click pentru a inchide" style="color:#1b1b1b; cursor:pointer; display: inline-block;"><i class="fas fa-exclamation-triangle"></i> Utilizatorii neinregistrati vor pierde fisierele in 30 de minute de la incarcare!</span>
		<?php else: ?>
			<i class="fas fa-hdd"></i>
			<span id="disc-name">Disc</span>
			<span id="memory"><span>Spatiu:</span><span id="n"><?php echo $freespace[0] . $freespace[1] . " / " . $maxspace[0] . $maxspace[1]; ?></span></span>
		<?php endif; ?>
		
		<span id="progress" style="display:none; vertical-align: baseline; "><span style="color: #005aff ;">Se incarca </span><span id="percent" style="vertical-align:middle; color: #005aff ;font-weight: 500;">...</span> </span>
		
		<span id="errors" title="Click pentru a inchide" style="color:red; font-weight: 500; cursor:pointer;"></span>
	</div>
	
	<div class="col" id="inf" style="display:none;">
		<span id="fileicon"></span>
		<span id="filename" style="display:inline-block;"></span>
		<span id="filetype" style="color:#222; display:inline-block;"></span>
		<span id="filesize" style="color:#5f5f5f; display:inline-block;"></span>
	</div>
</div>

<div style="display:none;" id="dinfo" data-hdl="<?php echo $handle; ?>" data-cd="<?php echo $_SESSION['cdid'];?>" data-did="<?php echo $discid;?>"></div>

<script type="module">
	import { Files } from '../js/files-api.js';
	(function() {
		Files.Read();
	})();
</script>