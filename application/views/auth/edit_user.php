<h1>
    <?php echo lang('edit_user_heading');?>
</h1>
<p>
    <?php echo lang('edit_user_subheading');?>
</p>
<div class="alert alert-warning" id="infoMessage">
    <?php echo $message;?>
</div>

<?php echo form_open(uri_string());?>

<p>
    <?php echo lang('create_user_fname_label', 'first_name');?> <br />
    <?php echo form_input($first_name);?>
</p>

<p>
    <?php echo lang('create_user_lname_label', 'last_name');?> <br />
    <?php echo form_input($last_name);?>
</p>





<p>
    <?php echo lang('create_user_email_label', 'email');?> <br />
    <?php echo form_input($email);?>
</p>

<p>
    <?php echo lang('create_user_birthday_label', 'email');?> <br />
    <?php echo form_input($birthday);?>
</p>

<p>
    <?php echo lang('edit_user_password_label', 'password_confirm');?><br />
    <?php echo form_input($password);?>
</p>

<p>
    <?php echo lang('edit_user_password_confirm_label', 'password_confirm');?><br />
    <?php echo form_input($password_confirm);?>
</p>

<p>sfsd
    <?= form_input($img) ?>
</p>
<p>
    <?php echo lang('edit_user_password_confirm_label', 'password_confirm');?><br />
    <?php echo form_input($password_confirm);?>
</p>

<?php echo form_hidden($csrf); ?>

<p>
    <?php echo form_submit('submit', lang('edit_user_submit_btn') ,["class"=>"form-control"]);?>
</p>

<?php echo form_close();?>
