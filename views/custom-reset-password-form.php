<?php

?>

<form id="reset-password-form" name="login-form" class="well form-horizontal" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" enctype="multipart/form-data">
	<div class="control-group">
		<label class="control-label" for="password1"><?php _e('Password', 'cell-store') ?></label>
		<div class="controls">
			<input type="password" class="input-xlarge " id="password1" name="password1">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="password2"><?php _e('Retype Password', 'cell-store') ?></label>
		<div class="controls">
			<input type="password" class="input-xlarge " id="password2" name="password2">
		</div>
	</div>
	<div class="form-actions">
		<button type="submit" class="btn btn-primary"><?php _e('Reset Password', 'cell-store') ?> <i class="icon icon-chevron-right icon-white"></i></button>
		<?php wp_nonce_field('frontend_reset_password','reset_password_nonce'); ?>
		<input name="action" value="frontend_reset_password" type="hidden">
		<input name="key" value="<?php echo $_REQUEST['key'] ?>" type="hidden">
		<input name="login" value="<?php echo $_REQUEST['login'] ?>" type="hidden">
	</div>
</form>