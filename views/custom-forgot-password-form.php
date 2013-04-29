<?php
	$request_url = $_SERVER['REQUEST_URI'];
	$return_url = str_replace('?forgot-password=1', '', $request_url);
	$return_url = str_replace('&forgot-password=1', '', $return_url);
?>

<form id="login-form" name="login-form" class="well form-horizontal" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" enctype="multipart/form-data">
	<div class="control-group">
		<label class="control-label" for="username"><?php _e('Username or Email', 'cell-store') ?></label>
		<div class="controls">
			<input type="text" class="input-xlarge " id="username" name="username">
		</div>
	</div>
	<div class="form-actions">
		<button type="submit" class="btn btn-primary"><?php _e('Send Key', 'cell-store') ?></button>
		<a href="<?php echo $return_url ?>"><?php _e('Login', 'cell-store') ?></button>
		<?php wp_nonce_field('frontend_forgot_password','forgot_password_nonce'); ?>
		<input  name="action" value="frontend_forgot_password" type="hidden">
	</div>
</form>