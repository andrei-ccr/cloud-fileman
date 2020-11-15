	<div class="box-container">
		<h2 style="color: gray;">Preview Mode</h2>
		
		<p class="box-container-txt" style="text-align: justify;">Log in and get your free virtual storage space where you can store files of any kind. If you don't want to create an account, you can get a free temporary virtual storage space where you can upload files for a limited time. After 30 minutes, everything will be erased unless you log in or create an account.</p>
		<p class="box-container-txt" style="color: red; text-align: justify;">Note: this is a preview of the Cloudman File Storage application. Due to the limits of the hosting server, you can only <strong>upload files of very small sizes (less than 0.5mb). The virtual storage space is limited to 4mb and all of the files will be deleted in less than 1 hour. </strong></p>

	</div>
	
	<div class="box-container">
		<?php if(isset($error)): ?>
			<div id="error-container">
				<p class="box-container-txt" style="color:red"><?php echo $error;?></p>
			</div>
		<?php endif; ?>
		<p class="box-container-txt">Enter your email address and password to log in.</p>
		<input type="text" placeholder="Email" id="email" name="email">
		<input type="password" placeholder="Password" id="pass" name="pass">

		<div style="display:block">
			<input type="Checkbox" id="rememberme" name="rememberme" style="vertical-align: middle;"><label for="rememberme" style="font-size: 14px; color: #777;">Stay logged in</label>
			<button id="btn-enter" style="margin: 10px 20px;">Enter</button>
			
		</div>
	</div>

	<div class="box-container">
		<p class="box-container-txt">If you don't have an account, you can create one immediately.</p>
		<button id="btn-to-register" style="margin: 10px 20px;border: 1px solid #0f7dcc; background-color: white; color: #0f7dcc;">Create Account</button>
	</div>

	<div class="box-container">
		<p class="box-container-txt">You can enter without an account. Files will be deleted in 30 minutes unless you login. <a id="btn-no-acc" style="cursor: pointer;">Enter without account</a></p>
	</div>