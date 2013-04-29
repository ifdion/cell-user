<?php
	$base_link = $_SERVER['REQUEST_URI'];
	if (stripos($base_link, '?')) {
		$forgot_password = '&forgot-password=1';
	} else {
		$forgot_password = '?forgot-password=1';
	}
	$forgot_password_link = $base_link.$forgot_password;
?>

<form id="login-form" name="login-form" class="well form-horizontal" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" enctype="multipart/form-data">
	<div class="control-group">
		<label class="control-label" for="username"><?php _e('Username or Email', 'cell-store') ?></label>
		<div class="controls">
			<input type="text" class="input-xlarge " id="username" name="username">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="password"><?php _e('Password', 'cell-store') ?></label>
		<div class="controls">
			<input type="password" class="input-xlarge " id="password" name="password">
		</div>
		<p class="help-block"><a href="<?php echo $forgot_password_link ?>"><?php _e('Forgot Password ?', 'cell-store') ?></a></p>
	</div>
	<div class="form-actions">
		<button type="submit" class="btn btn-primary"><?php _e('Login', 'cell-store') ?></button>
		<?php wp_nonce_field('frontend_login','login_nonce'); ?>
		<input  name="action" value="frontend_login" type="hidden">
	</div>
</form>