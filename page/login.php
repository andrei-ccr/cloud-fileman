	<div class="box-container">
		<?php if(isset($error)): ?>
			<p class="box-container-txt" style="color:red">Error: <?php echo $error;?></p>
		<?php endif; ?>
		<p class="box-container-txt">Introdu emailul si parola pentru a intra in cont. Daca nu ai cont, acesta va fi creat automat.</p>
		<input type="email" placeholder="Email" id="email" name="email">
		<input type="password" placeholder="Parola" id="pass" name="pass">
		<button id="btn-enter">Intra</button>
	</div>
	
	<div class="box-container">
		<p class="box-container-txt">Nu vrei cont? <a id="btn-no-acc" style="cursor: pointer;">Intra fara cont</a></p>
	</div>