<?php
    if(!isset($_GET['setting-show-context-menu'])) {
        http_response_code(400);
        exit;
    }
?>

<div>
    <h3>Change password</h3>
    <div class="edit-text-input">
        <label for="setting-pwd-current">Current Password</label>
        <input type="password" name="setting-pwd-current" id="setting-pwd-current">
    </div>
    <div class="edit-text-input">
        <label for="setting-pwd-new">New Password</label>
        <input type="password" name="setting-pwd-new" id="setting-pwd-new">
    </div>
    <div class="edit-text-input">
        <label for="setting-pwd-confirm">Confirm New Password</label>
        <input type="password" name="setting-pwd-confirm" id="setting-pwd-confirm">
    </div>

    <button id="save-password-change">Save new password</button>

    <h3>File Manager</h3>

    <div>
        <label class="checkbox-label" for="setting-show-context-menu">Show menu on right click</label>
        <label class="switch">
            <input type="checkbox" id="setting-show-context-menu" name="setting-show-context-menu" <?php echo ($_GET['setting-show-context-menu'] == "1")?"checked":""; ?> >
            <span class="slider"></span>
        </label>
    </div>

    <div>
        <label class="checkbox-label" for="setting-show-status-bar">Show status bar</label>
        <label class="switch">
            <input type="checkbox" id="setting-show-status-bar" name="setting-show-status-bar" <?php echo ($_GET['setting-show-context-menu'] == "1")?"checked":""; ?> >
            <span class="slider"></span>
        </label>
    </div>

    <h3>Delete Account</h3>
    <p style="font-weight:500;">Warning! Deleting your account will permanently erase all your files!</p>
    <button style="background:red;">Delete Account</button>
</div>