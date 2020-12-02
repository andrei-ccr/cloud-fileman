	<div class="box-container">
		<?php if(isset($error)): ?>
			<div id="error-container">
				<p class="box-container-txt" style="color:red"><?php echo $error;?></p>
			</div>
		<?php endif; ?>
		
		<p class="box-container-txt" style="text-align: justify; font-size: 2rem; line-height: 35px; width: 320px; margin: 15px auto; color: #333;">Log in to access your free virtual storage space where you can store files of any kind.</p>
		
		<input type="text" placeholder="Email" id="email" name="email" style="width:300px;">
		<div style="display:block; margin:10px;"></div>
		
		<input type="password" placeholder="Password" id="pass" name="pass" style="width:300px;">

		<div style="display:block"></div>
		
		<button id="btn-enter" style="margin: 10px 20px; width:320px;">Log In</button>
		
		<div style="display:block"></div>
		
		<input type="Checkbox" id="rememberme" name="rememberme" style="vertical-align: middle;"><label for="rememberme" style="font-size: 14px; color: #777;">Stay Logged In</label>
		
		<div style="display:block; margin:20px;"></div>
		
		<button id="btn-to-register" style="margin: 10px 20px; width:320px;border: 1px solid #0f7dcc; background-color: white; color: #0f7dcc;">Create Account</button>
		
		<div style="display:block; margin:10px;"></div>
	</div>
	
	<!--<div class="box-container">

		<p class="box-container-txt" style="color: red; text-align: justify; font-size:12px;">Note: this is a preview of the Cloudman File Storage application. Due to the limits of the hosting server, you can only <strong>upload files of very small sizes (less than 0.5mb). The virtual storage space is limited to 4mb.</strong></p>
		<p class="box-container-txt" style="text-align: justify; color:red; font-size:12px;">Want a better preview? Contact me <a href="https://andreiccr.github.io">https://andreiccr.github.io</a></p>

	</div>-->

	<!--<div class="box-container">
		<p class="box-container-txt">You can enter without an account. Files will be deleted in 30 minutes unless you login. <a id="btn-no-acc" style="cursor: pointer;">Enter without account</a></p>
	</div>-->
	
	<footer style="display: block; margin: auto; text-align: center; color: #888; font-size: 12px;">&copy; Cloudman File 2020</footer> 
	
	<script>
	/*particlesJS.load('body', '/js/particles.json', function() {
	  console.log('callback - particles.js config loaded');
	});*/
	</script>