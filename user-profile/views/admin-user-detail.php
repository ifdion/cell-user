<?php
	/**
	 * Default wp-admin additional user profile form
	 * 
	 * TODO :
	 * input option from tax_*
	 * input option from cpt_*
	 * input option from function
	 * input type : textsuggest
	 * input type : multiple select
	 * input type : checkbox group
	 * input type : file
	 * input type : datepicker
	 * 
	 * 
	 **/

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
				<?php
					if (isset($user_meta[$field_key][0])) {
						$current_value = $user_meta[$field_key][0];
					} else {
						$current_value = '';
					}
				?>
				<?php switch ($field_value['type']) {
					case 'text':
						?>
							<tr>
								<th><label for="<?php echo $field_key ?>"><?php echo $field_value['title'] ?></label></th>
								<td><input type="text" id="<?php echo $field_key ?>" name="<?php echo $field_key ?>" value="<?php echo $current_value ?>"></td>
							</tr>
						<?php
					break;
					case 'textarea':
						?>
							<tr>
								<th><label for="<?php echo $field_key ?>"><?php echo $field_value['title'] ?></label></th>
								<td>
									<textarea class="regular-text" id="<?php echo $field_key ?>" name="<?php echo $field_key ?>" ><?php echo $current_value ?></textarea>
								</td>
							</tr>
						<?php
					break;
					case 'checkbox':
						?>
							<tr>
								<th><label for="<?php echo $field_key ?>"><?php echo $field_value['title'] ?></label></th>
								<td><input type="checkbox" id="<?php echo $field_key ?>" name="<?php echo $field_key ?>" value="1" <?php checked($current_value, 1) ?>></td>
							</tr>
						<?php
					break;
					case 'radio':
						?>
							<tr>
								<th><label for="<?php echo $field_key ?>"><?php echo $field_value['title'] ?></label></th>
								<td>
									<?php if (isset($field_value['option'])): ?>
										<?php foreach ($field_value['option'] as $option_value => $option_title): ?>
											<label class="radio inline">
												<input type="radio" id="<?php echo $field_key.'-'.$field_value['option'] ?>" value="<?php echo $option_value ?>" name="<?php echo $field_key ?>" <?php checked($current_value, $option_value) ?> > <?php echo $option_title ?>
											</label> &nbsp;
										<?php endforeach ?>
									<?php else: ?>
										<?php _e( 'Missing options.','cell-user' ) ?>
									<?php endif ?>
								</td>
							</tr>
						<?php
					break;
					case 'select':
						?>
							<tr>
								<th><label for="<?php echo $field_key ?>"><?php echo $field_value['title'] ?></label></th>
								<td>
									<?php if (isset($field_value['option'])): ?>
										<select name="<?php echo $field_key ?>">
											<?php foreach ($field_value['option'] as $option_value => $option_title): ?>
												<option value="<?php echo $option_value ?>"  <?php selected($current_value, $option_value) ?> > <?php echo $option_title ?></option>
											<?php endforeach ?>
										</select>
									<?php else: ?>
										<?php _e( 'Missing options.','cell-user' ) ?>
									<?php endif ?>

								</td>
							</tr>
						<?php
					break;
					
					default:

					break;
				} ?>
			<?php endif ?>
		<?php endforeach ?>
	</table>
<?php endforeach ?>