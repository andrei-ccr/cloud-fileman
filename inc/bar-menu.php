<?php
	if(!isset($_POST['status'])) {
		http_response_code(400);
		exit;
	}

	$status = json_decode($_POST['status']);

	$fs = !is_null($status->targetFile); //True if a file is selected
?>


<div class="m-context-menu cm mcm">
	<ul>
		<?php if(true) : ?><li class="cm-upload"><i class="fas fa-upload"></i> Upload File(s)</li><?php endif; ?>
		<?php if($fs ) : ?><li class="cm-download"><i class="fas fa-download"></i> Download</li><?php endif; ?>
		<li class="separator"></li>
		<?php if(isset($_COOKIE['guest'])): ?>
			<li class="cm-logout-guest"><i class="fas fa-sign-in-alt"></i> Erase Disc</li>
		<?php else: ?>
			<li class="cm-logout"><i class="fas fa-sign-out-alt"></i> Logout</li>
		<?php endif; ?>
		
	</ul>
</div>