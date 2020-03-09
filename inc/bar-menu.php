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
		<?php if(true) : ?><li id="cm-to-settings"><i class="fas fa-cog"></i>  Settings</li><?php endif; ?>
		<?php if(true) : ?><li id="cm-to-help"><i class="fas fa-question-circle"></i>  Help</li><?php endif; ?>
		<li class="separator"></li>
		<?php if(isset($_COOKIE['guest'])): ?>
			<li id="cm-login"><i class="fas fa-sign-in-alt"></i> Login Page</li>
		<?php else: ?>
			<li id="cm-logout"><i class="fas fa-sign-out-alt"></i> Logout</li>
		<?php endif; ?>
		
	</ul>
</div>