<?php
    if(!isset($_POST['fid'])) {
        http_response_code(400);
        exit;
    }

    if(!isset($_POST['content'])) {
        $content = "";
    } else {
        $content = $_POST['content'];
    }
?>

<style>
    .modal button {
        background: #0869ff;
		border: 0px solid #d4d4d4;
		padding: 9px 12px;
		border-radius: 5px;
		color: white;
		font-size: 12px;
		font-weight: 500;
		font-family: 'Roboto', sans-serif;
		cursor: pointer;
		margin: 15px;
    }
</style>
<div class="modal" data-fid="<?php echo $_POST['fid']?>" style="position: absolute; top:0; left:0; width:100%; height:100%; background:#33333333;  z-index:100; text-align:center;">
    <div style="width:640px; height: 480px; background: white; margin: 20px auto;">
        <textarea style="width:100%; height:100%; border:0; font-family: 'Roboto',sans-serif;"><?php echo $content; ?></textarea>
    </div>
    <div style="width:640px; height: auto; background: white; margin: 10px auto;">
        <button id="save" style="background-color:green;">Save</button>
        <button id="cancel" style="border: 1px solid gray; background:white; color:gray;">Cancel</button>
    </div>

</div>