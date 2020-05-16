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

    if(!isset($_POST['filename'])) {
        http_response_code(400);
        exit;
    } else {
        $filename = $_POST['filename'];
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

    .modal {
        position: absolute; 
        top:0; left:0; 
        width:100%; 
        min-height:100%; 
        background:#FFFFFFDD;  
        z-index:100; 
        text-align:center;
    }

    .modal > .container {
        width:25%; height: auto; background: white; margin: 10px auto;
    }

    .modal > textarea{
        color:#333; width:85%; height:35vh; border: 1px solid #ddd; padding: 15px; font-family: 'Roboto',sans-serif;
    }
</style>
<div class="modal" data-fid="<?php echo $_POST['fid']?>" style="">
    <div class="container" style="color: #777; font-weight: 500; font-size: 14px;">
        <?php echo $filename; ?>
    </div>
    <div class="container">
        <button id="save" style="background-color:#0869ff;">Save</button>
        <button id="cancel" style="border: 1px solid gray; background:white; color:gray;">Cancel</button>
    </div>
    <textarea placeholder="Enter text here..."><?php echo $content; ?></textarea>

</div>