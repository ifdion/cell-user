<?php
	if (isset($_GET['user_id'])) {
		$current_user_id = $_GET['user_id'];
	} else {
		global $current_user;
		$current_user_id = $current_user->ID;
	}
	
	$user_meta = get_user_meta($current_user_id);
?>
<?php foreach ($profile_field as $key => $value): ?>
	<h3 id="<?php echo $key ?>"><?php echo $value['title'] ?></h3>
	<table class="form-table">
		<?php foreach ($value['fields'] as $field_key => $field_value): ?>
			<?php if (!in_array($field_key, $this->admin_preset_meta)): ?>
				<?php if ($field_value['type'] == 'text'): ?>
					<tr>
						<th><label for="<?php echo $field_key ?>"><?php echo $field_value['title'] ?></label></th>
						<td><input type="text" id="<?php echo $field_key ?>" name="<?php echo $field_key ?>" value="<?php echo $user_meta[$field_key][0] ?>"></td>
					</tr>
				<?php elseif ($field_value['type'] == 'checkbox'): ?>
					<tr>
						<th><label for="<?php echo $field_key ?>"><?php echo $field_value['title'] ?></label></th>
						<td><input type="checkbox" id="<?php echo $field_key ?>" name="<?php echo $field_key ?>" value="1" <?php checked($user_meta[$field_key][0], 1) ?>></td>
					</tr>
				<?php elseif ($field_value['type'] == 'textarea'): ?>
					<tr>
						<th><label for="<?php echo $field_key ?>"><?php echo $field_value['title'] ?></label></th>
						<td>
							<textarea class="regular-text" id="<?php echo $field_key ?>" name="<?php echo $field_key ?>" ><?php echo $user_meta[$field_key][0] ?></textarea>
						</td>
					</tr>
				<?php endif ?>
			<?php endif ?>
		<?php endforeach ?>
	</table>
<?php endforeach ?>