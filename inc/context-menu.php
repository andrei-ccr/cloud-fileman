<?php
	if(!isset($_POST['status']) && !isset($_POST['clipboard'])) {
		http_response_code(400);
		exit;
	}

	$status = json_decode($_POST['status']);
	$clipboard = json_decode($_POST['clipboard']);

	$fs = !is_null($status->targetFile); //True if a file is selected
	$f_in_clip = !is_null($clipboard->file); //True if a file is in clipboard

	//$fld = isset($_POST['fld'])?$_POST['fld']:false; //True if selected element is a folder

?>


<div class="context-menu cm">
	<ul>
		<?php if(!$fs && false) : ?><li class="cm-views">View <i class="fas fa-caret-right"></i>
										<ul id="cm-views-subm">
											<li class="cm-view-tiles">Tiles</li>
											<li class="cm-view-icons">Icons</li>
											<li class="cm-view-bthumbs">Big Thumbnails</li>
										</ul>
									</li> <?php endif; ?>
		<?php /*if($fs && ($loc_disk || $loc_fav)) : ?><li id="cm-add-to-fav"><i class="fas fa-star"></i> Mark</li><?php endif;*/ ?>

		<?php if($fs ) :?><li class="cm-edit">Open/Edit</li><?php endif;?>

		<?php if(!$fs ) : ?><li class="cm-refresh"><i class="fas fa-redo"></i> Refresh</li><?php endif; ?>
		<?php if(!$fs ) : ?><li class="cm-new-folder"><i class="fas fa-folder"></i> New Folder</li><?php endif; ?>
		<?php if(!$fs ) : ?><li class="cm-new-file"><i class="fas fa-file"></i> New File</li><?php endif; ?>
		<?php if(!$fs ) : ?><li class="cm-paste <?php echo ($f_in_clip)?"":"cm-disabled";?> "><i class="fas fa-paste"></i> Paste</li><?php endif; ?>

		<?php if($fs ) :?><li class="cm-cut"><i class="fas fa-cut"></i> Cut</li><?php endif;?>
		<?php if($fs ) : ?><li class="cm-copy"><i class="far fa-copy"></i> Copy</li><?php endif; ?>

		<?php if($fs ) :?><li class="cm-rename"><i class="fas fa-edit"></i> Rename</li><?php endif;?>
		<?php if($fs ) : ?><li class="cm-download"><i class="fas fa-download"></i> Download</li><?php endif; ?>
		<?php if($fs) :?><li class="cm-delete"><i class="fas fa-trash"></i> Delete</li><?php endif; ?>
		
	</ul>
</div>