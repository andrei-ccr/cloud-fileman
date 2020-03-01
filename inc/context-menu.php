<?php
	if(!isset($_POST['status'])) die("ded");
	$status = json_decode($_POST['status']);

	$fs = !is_null($status->targetFile); //True if a file is selected

	//$fld = isset($_POST['fld'])?$_POST['fld']:false; //True if selected element is a folder

	//<li class="cm-disabled" id="cm-paste">Paste</li>
	//<li id="cm-open">Open</li>
	//<li class="cm-disabled" id="cm-cut">Cut</li>
	//<li class="cm-disabled" id="cm-copy">Copy</li>
	//<li class="cm-disabled" id="cm-properties">Properties</li>

?>


<div class="context-menu cm">
	<ul>
		<?php if(!$fs) : ?><li id="cm-views">View <i class="fas fa-caret-right"></i>
										<ul id="cm-views-subm">
											<li id="cm-view-tiles">Tiles</li>
											<li id="cm-view-icons">Icons</li>
											<li id="cm-view-bthumbs">Big Thumbnails</li>
										</ul>
									</li> <?php endif; ?>
		<?php /*if($fs && ($loc_disk || $loc_fav)) : ?><li id="cm-add-to-fav"><i class="fas fa-star"></i> Mark</li><?php endif;*/ ?>
		<?php if(!$fs ) : ?><li id="cm-refresh"><i class="fas fa-redo"></i> Refresh</li><?php endif; ?>
		<?php if(!$fs ) : ?><li id="cm-new-folder"><i class="fas fa-folder"></i> New Folder</li><?php endif; ?>
		<?php if($fs ) :?><li id="cm-rename"><i class="fas fa-edit"></i> Rename</li><?php endif;?>
		<?php if($fs ) : ?><li id="cm-download"><i class="fas fa-download"></i> Download</li><?php endif; ?>
		<?php if($fs) :?><li id="cm-delete"><i class="fas fa-trash"></i> Delete</li><?php endif; ?>
		<?php /*if($fs ) :?><li id="cm-permadelete"><i class="fas fa-trash"></i> Delete</li><?php endif;*/?>
		<?php /*if(!$fs ) :?><li id="cm-empty-trash">Empty Trash</li><?php endif; */?>
		
	</ul>
</div>