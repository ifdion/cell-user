<?php
	global $current_user;
	$user_data = get_user_meta($current_user->ID);
?>
<form id="edit-profile" name="edit-profile" class="well form-horizontal" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" enctype="multipart/form-data">
	<fieldset id="user-account">
		<legend><h4>User Account</h4></legend>
		<div class="control-group">
			<label class="control-label" for="user_email"><?php _e( 'User Email','cell-user' ) ?></label>
			<div class="controls">
				<input type="text" class="input-xlarge " id="user_email" name="user_email" value="<?php echo $current_user->data->user_email ?>">
				<input type="hidden" class="input-xlarge " id="user_email_old" name="user_email_old" value="<?php echo $current_user->data->user_email ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="user_password"><?php _e( 'Password','cell-user' ) ?></label>
			<div class="controls">
				<input type="password" class="input-xlarge " id="user_password" name="user_password" value="">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="user_password_retype"><?php _e( 'Retype Password','cell-user' ) ?></label>
			<div class="controls">
				<input type="password" class="input-xlarge " id="user_password_retype" name="user_password_retype" value="">
			</div>
		</div>

	</fieldset>
	<?php foreach ($profile_field as $key => $value): ?>
		<?php if ($value['public'] == true): ?>
			<fieldset id="<?php echo $key ?>">
				<legend><h4><?php echo $value['title'] ?></h4></legend>
				<?php foreach ($value['fields'] as $field_key => $field_value): ?>
					<?php
						switch ($field_value['type']) {
							case 'text':
								?>
									<div class="control-group">
										<label class="control-label" for="<?php echo $field_key ?>"><?php echo $field_value['title'] ?></label>
										<div class="controls">
											<input type="text" class="input-xlarge " id="<?php echo $field_key ?>" name="<?php echo $field_key ?>" value="<?php echo $user_data[$field_key][0] ?>">
										</div>
									</div>
								<?php
							break;
							case 'textarea':
								?>
									<div class="control-group">
										<label class="control-label" for="<?php echo $field_key ?>"><?php echo $field_value['title'] ?></label>
										<div class="controls">
											<textarea type="checkbox" class="input-xlarge " id="<?php echo $field_key ?>" name="<?php echo $field_key ?>"><?php echo $user_data[$field_key][0] ?></textarea>
										</div>
									</div>
								<?php
							break;
							case 'checkbox':
								?>
									<div class="control-group">
										<div class="controls">
											<label class="checkbox" for="<?php echo $field_key ?>">
												<input type="checkbox" class="input-xlarge " id="<?php echo $field_key ?>" name="<?php echo $field_key ?>" value="1" <?php checked($user_data[$field_key][0], 1) ?>>
												<?php echo $field_value['title'] ?>
											</label>
										</div>
									</div>
								<?php
							break;
							case 'radio':
								?>
									<div class="control-group">
										<label class="control-label"><?php echo $field_value['title'] ?></label>
										<div class="controls">
											<?php foreach ($field_value['option'] as $option_value => $option_title): ?>
												<label class="radio inline">
													<input type="radio" id="inlineCheckbox1" value="<?php echo $option_value ?>" name="<?php echo $field_key ?>" <?php checked($user_data[$field_key][0], $option_value) ?> > <?php echo $option_title ?>
												</label>
											<?php endforeach ?>
										</div>
									</div>
								<?php
							break;
							
							default:
								# code...
								break;
						}
					?>
				<?php endforeach ?>
			</fieldset>
		<?php endif ?>
	<?php endforeach ?>
	<div class="form-actions">
		<button type="submit" class="btn btn-primary"><?php _e('Save', 'cell-user') ?></button>
		<?php wp_nonce_field('frontend_profile','profile_nonce'); ?>
		<input name="action" value="frontend_profile" type="hidden">
		<input name="user_id" value="<?php echo $current_user->ID ?>" type="hidden">
	</div>
</form>