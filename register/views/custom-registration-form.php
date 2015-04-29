<?php if (!is_user_logged_in()): ?>
	<?php
	?>
	<form id="register-form" name="register-form" class="well form-horizontal" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" enctype="multipart/form-data">
		<?php foreach ($registration_field as $key => $value): ?>
			<?php
				if (isset($value['required'])) {
					$required['class'] = 'required';
					if (isset($value['required_text'])) {
						$required['text'] = $value['required_text'];
					}
				} else {
					$required['class'] = '';
					$required['text'] = '';
				}
			?>
			<div class="control-group">
				<label class="control-label" for="input01"><?php echo $value['title'] ?> <?php echo $required['text'] ?> </label>
				<div class="controls">
					<?php if ($value['type'] == 'text'): ?>
						<input type="<?php echo $value['type'] ?>" class="input-xlarge <?php echo $required['class'] ?>" id="<?php echo $key ?>" name="<?php echo $key ?>">
					<?php elseif ($value['type'] == 'select'): ?>
						<?php if (isset($value['option'])): ?>
							<?php
								if (!is_array($value['option'])) {
									$option = call_user_func_array($value['option'],array($current_user->ID));
								} else {
									$option = $value['option'];
								}
							?>
							<select name="<?php echo $key ?>">
								<?php foreach ($option as $option_value => $option_title): ?>
									<option value="<?php echo $option_value ?>" > <?php echo $option_title ?></option>
								<?php endforeach ?>
							</select>
						<?php else: ?>
							<?php _e( 'Missing options.','cell-user' ) ?>
						<?php endif ?>

					<?php elseif ($value['type'] == 'textarea'): ?>
						<textarea class="input-xlarge <?php echo $required['class'] ?>" id="<?php echo $key ?>" name="<?php echo $key ?>"></textarea>
					<?php else: ?>
						<input type="<?php echo $value['type'] ?>" class="input-xlarge <?php echo $required['class'] ?>" id="<?php echo $key ?>" name="<?php echo $key ?>">
					<?php endif ?>
					<?php if ($value['note']): ?>
						<p class="help-block"><?php echo $value['note'] ?></p>
					<?php endif ?>
				</div>
			</div>
		<?php endforeach ?>
		<?php if (isset($this->register_args['captcha'])) :?>
		<div class="control-group">
			<label class="control-label" for="input01"><?php _e( 'Retype this Letters', 'cell-user' ) ?></label>
			<div id="" class="controls">
				<p><img style=""src="<?php echo admin_url( 'admin-ajax.php') ?>?action=get_captcha_image "> <a href=""><?php _e( 'Reload Captcha','cell-user' ) ?></a></p>
				<input type="text" class="input-xlarge <?php echo $required['class'] ?>" id="captcha" name="captcha" />
			</div>
		</div>

		<?php endif	?>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary"><?php _e('Register', 'cell-user') ?></button>
			<?php wp_nonce_field('frontend_registration','registration_nonce'); ?>
			<input  name="action" value="frontend_registration" type="hidden">
		</div>
	</form>	
<?php endif ?>
