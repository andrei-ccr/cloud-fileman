	<div class="box-container">
		<?php if(isset($error)): ?>
			<p class="box-container-txt" style="color:red">Error: <?php echo $error;?></p>
		<?php endif; ?>
		<p class="box-container-txt">Introdu emailul si parola pentru a intra in cont. Daca nu ai cont, acesta va fi creat automat.</p>
		<input type="email" placeholder="Email" id="email" name="email">
		<input type="password" placeholder="Parola" id="pass" name="pass">

		<div style="display:block">
			<input type="Checkbox" id="rememberme" name="rememberme" style="vertical-align: middle;"><label for="rememberme" style="font-size: 14px; color: #777;">Ramai in cont</label>
			<button id="btn-enter" style="margin: 10px 20px;">Intra</button>
		</div>
	</div>
	
	<div class="box-container">
		<p class="box-container-txt">Nu vrei cont? <a id="btn-no-acc" style="cursor: pointer;">Intra fara cont</a></p>
	</div>