<h1>
    <?php echo lang('create_user_heading');?>
</h1>
<p>
    <?php echo lang('create_user_subheading');?>
</p>

<div id="infoMessage">
    <?php echo $message;?>
</div>

<?php echo form_open("auth/create_user");?>

<p>
    <?php echo lang('create_user_fname_label', 'first_name');?> <br />
    <?php echo form_input($first_name);?>
</p>

<p>
    <?php echo lang('create_user_lname_label', 'last_name');?> <br />
    <?php echo form_input($last_name);?>
</p>

<?php
if ($identity_column !== 'email') {
    echo '<p>';
    echo lang('create_user_identity_label', 'identity');
    echo '<br />';
    echo form_error('identity');
    echo form_input($identity);
    echo '</p>';
}
?>



<p>
    <?php echo lang('create_user_email_label', 'email');?> <br />
    <?php echo form_input($email);?>
</p>

<p>
    <?php echo lang('create_user_birthday_label', 'email');?> <br />
    <?php echo form_input($birthday);?>
</p>
<p>
    Upload Img
    
  
</p>



<p>

    <?= form_input($img) ?>
    <?= form_input($img_upload) ?>
</p>


<img id="res"  height="150" src="<?= (isset($_POST['img']))? $_POST['img'] : NULL;  ?>" />

<p>
    <?php echo lang('create_user_password_label', 'password');?> <br />
    <?php echo form_input($password);?>
</p>

<p>
    <?php echo lang('create_user_password_confirm_label', 'password_confirm');?> <br />
    <?php echo form_input($password_confirm);?>
</p>

<p>
    about <br />
    <?= form_textarea($about) ?>
</p>


<p>
    <?php echo form_submit('submit', lang('create_user_submit_btn'),["class"=>"form-control"]);?>
</p>

<?php echo form_close();?>

<script src="<?= base_url('js/upload.js')?>">
</script>