<?php
	/**
	 * Default wp-admin additional user profile form
	 * 
	 * TODO :
	 * input option from tax_*
	 * input option from cpt_*
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
	<?php
		if (isset($value['show-on'])){
			$data_show_on = 'data-show-on="'. $value['show-on'] .'"';
		} else {
			$data_show_on = '';
		}
	?>
	<div id="" class="<?php echo $profile_field ?>" <?php echo $data_show_on ?>>
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
						// set additional class
						$added_class = ' ';
						if (isset($field_value['attr']['class'])) {
							$added_class = ' '.$field_value['attr']['class'];
							unset($field_value['attr']['class']);
						}
						// set additional attributes
						$additional_attr = '';
						if (isset($field_value['attr'])) {
							foreach ($field_value['attr'] as $attr_key => $attr_value) {
								$additional_attr .= ' '.$attr_key.'="'.$attr_value.'"';
							}
						}
					?>
					<?php switch ($field_value['type']) {
						case 'text':
							?>
								<tr>
									<th><label for="<?php echo $field_key ?>"><?php echo $field_value['title'] ?></label></th>
									<td><input class="<?php echo $added_class ?>" type="text" id="<?php echo $field_key ?>" name="<?php echo $field_key ?>" value="<?php echo $current_value ?>" <?php echo $additional_attr ?>></td>
								</tr>
							<?php
						break;
						case 'textarea':
							?>
								<tr>
									<th><label for="<?php echo $field_key ?>"><?php echo $field_value['title'] ?></label></th>
									<td>
										<textarea class="regular-text <?php echo $added_class ?>" id="<?php echo $field_key ?>" name="<?php echo $field_key ?>" <?php echo $additional_attr ?>><?php echo $current_value ?></textarea>
									</td>
								</tr>
							<?php
						break;
						case 'checkbox':
							?>
								<tr>
									<th><label for="<?php echo $field_key ?>"><?php echo $field_value['title'] ?></label></th>
									<td><input class="<?php echo $added_class ?>" type="checkbox" id="<?php echo $field_key ?>" name="<?php echo $field_key ?>"  value="1" <?php checked($current_value, '1') ?> <?php echo $additional_attr ?>> <?php echo $current_value ?></td>
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
													<input class="<?php echo $added_class ?>" type="radio" id="<?php echo $field_key.'-'.$field_value['option'] ?>" value="<?php echo $option_value ?>" name="<?php echo $field_key ?>" <?php checked($current_value, $option_value) ?> <?php echo $additional_attr ?> > <?php echo $option_title ?>
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
											<?php
												if (!is_array($field_value['option'])) {
													$option = call_user_func_array($field_value['option'],array($current_user->ID));
												} else {
													$option = $field_value['option'];
												}
											?>
											<select name="<?php echo $field_key ?>" class="<?php echo $added_class ?>" <?php echo $additional_attr ?>>
												<?php foreach ($option as $option_value => $option_title): ?>
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
	</div>
<?php endforeach ?>