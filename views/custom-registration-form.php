<?php if (!is_user_logged_in()): ?>
	<?php
		global $registration_field;
	?>
	<form id="register-form" name="register-form" class="well form-horizontal" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" enctype="multipart/form-data">
		<?php foreach ($registration_field as $key => $value): ?>
			<?php
				if ($value['required']) {
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
					<?php if ($value['type'] != 'textarea'): ?>
						<input type="<?php echo $value['type'] ?>" class="input-xlarge <?php echo $required['class'] ?>" id="<?php echo $key ?>" name="<?php echo $key ?>">
					<?php else: ?>
						<textarea class="input-xlarge <?php echo $required['class'] ?>" id="<?php echo $key ?>" name="<?php echo $key ?>"></textarea>
					<?php endif ?>
					<?php if ($value['note']): ?>
						<p class="help-block"><?php echo $value['note'] ?></p>
					<?php endif ?>
				</div>
			</div>
		<?php endforeach ?>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary"><?php _e('Register', 'cell-store') ?></button>
			<?php wp_nonce_field('frontend_registration','registration_nonce'); ?>
			<input  name="action" value="frontend_registration" type="hidden">
		</div>
	</form>	
<?php endif ?>
