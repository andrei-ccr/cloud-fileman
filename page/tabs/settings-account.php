<?php
    if(!isset($_GET['setting-show-context-menu'])) {
        http_response_code(400);
        exit;
    }
?>

<h2>Account</h2>
<input type="checkbox" id="setting-show-context-menu" name="setting-show-context-menu" <?php echo ($_GET['setting-show-context-menu'] == "1")?"checked":""; ?> >
<label for="setting-show-context-menu">Show menu on right click</label>