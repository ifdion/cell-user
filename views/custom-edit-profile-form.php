<?php
	global $current_user;
	$user_data = get_user_meta($current_user->ID);
	if ($user_data['first_name'][0]) {
		$first_name = $user_data['first_name'][0] ;
	} else {
		$first_name = $current_user->display_name;
	}
?>
<form id="edit-profile" name="edit-profile" class="well form-horizontal" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" enctype="multipart/form-data">
	<fieldset id="billing-field">
		<legend><h4>Billing</h4></legend>
		<div class="control-group">
			<label class="control-label" for="first-name"><?php _e('First Name', 'cell-store') ?></label>
			<div class="controls">
				<input type="text" class="input-xlarge " id="first-name" name="first-name" value="<?php echo $first_name ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="last-name"><?php _e('Last Name', 'cell-store') ?></label>
			<div class="controls">
				<input type="text" class="input-xlarge " id="last-name" name="last-name" value="<?php echo $user_data['last_name'][0] ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="email"><?php _e('Email', 'cell-store') ?></label>
			<div class="controls">
				<input type="text" class="input-xlarge " id="email" name="email" value="<?php echo $current_user->user_email ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="password1"><?php _e('Password', 'cell-store') ?></label>
			<div class="controls">
				<input type="password" class="input-xlarge " id="password1" name="password1" >
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="password2"><?php _e('Retype Password', 'cell-store') ?></label>
			<div class="controls">
				<input type="password" class="input-xlarge " id="password2" name="password2" >
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="telephone"><?php _e('Telephone', 'cell-store') ?></label>
			<div class="controls">
				<input type="text" class="input-xlarge " id="telephone" name="telephone" value="<?php echo $user_data['telephone'][0] ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="company"><?php _e('Company', 'cell-store') ?></label>
			<div class="controls">
				<input type="text" class="input-xlarge " id="company" name="company" value="<?php echo $user_data['company'][0] ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="address"><?php _e('Address', 'cell-store') ?></label>
			<div class="controls">
				<textarea class="input-xlarge " id="address" name="address" ><?php echo $user_data['address'][0] ?></textarea>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="postcode"><?php _e('Postcode', 'cell-store') ?></label>
			<div class="controls">
				<input type="text" class="input-xlarge " id="postcode" name="postcode" value="<?php echo $user_data['postcode'][0] ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="country"><?php _e('Country', 'cell-store') ?></label>
			<div class="controls">
				<?php
					$args = array(
						'post_type' => 'shipping-destination',
						'post_parent' => 0,
						'nopaging' => true
					);
					$countries = new WP_Query($args);
					$current_shipping_country =  $user_data['country'][0];
					if (is_numeric($current_shipping_country)) {
						$selected_country = $current_shipping_country;
					}
				?>
				<?php if (isset($selected_country) || !$current_shipping_country): ?>
					<select id="country" name="country" class="select-address" data-target="province">
						<?php if ( $countries->have_posts() ) : ?>
							<?php while ( $countries->have_posts() ) : $countries->the_post(); ?>
								<option value="<?php the_ID() ?>" <?php selected($selected_country, get_the_ID()) ?>><?php the_title() ?></option>
							<?php endwhile; ?>
							<option value="other"><?php _e('Other', 'cell-store') ?></option>
						<?php endif; ?>
					</select>
				<?php else: ?>
					<input type="text" class="input-xlarge select-address" id="country" name="country" value="<?php echo $current_shipping_country ?>" data-target="province">
				<?php endif ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="shipping-province"><?php _e('Province / State / County', 'cell-store') ?></label>
			<div class="controls">
				<?php
					$current_shipping_province =  $user_data['province'][0];
					
					$disabled = 'disabled="disabled"';
					if (is_numeric($current_shipping_province)) {
						$selected_province = $current_shipping_province;
					}
					if (isset($selected_country)) {
						$args = array(
							'post_type' => 'shipping-destination',
							'post_parent' => $selected_country,
							'nopaging' => true
						);
						$provinces = new WP_Query($args);
						$disabled = '';
					}
				?>
				<?php if (isset($selected_province) || !$current_shipping_province): ?>
					<select id="province" name="province" <?php echo $disabled; ?> class="select-address" data-target="city">
						<option value="intro"><?php _e('Please select - leave empty if not applicalble', 'cell-store') ?></option>
						<?php if ( $provinces && $provinces->have_posts() ) : ?>
							<?php while ( $provinces->have_posts() ) : $provinces->the_post(); ?>
								<option value="<?php the_ID() ?>" <?php selected($selected_province, get_the_ID()) ?>><?php the_title() ?></option>
							<?php endwhile; ?>
						<?php endif; ?>
						<option value="other"><?php _e('Other', 'cell-store') ?></option>
					</select>
				<?php else: ?>
					<input type="text" class="input-xlarge select-address" id="province" name="province" value="<?php echo $current_shipping_province ?>" data-target="city">
				<?php endif ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="city"><?php _e('City', 'cell-store') ?></label>
			<div class="controls">
				<?php
					$current_shipping_city =  $user_data['city'][0];
					
					$disabled = 'disabled="disabled"';
					if (is_numeric($current_shipping_city)) {
						$selected_city = $current_shipping_city;
					}
					if (isset($selected_province)) {
						$args = array(
							'post_type' => 'shipping-destination',
							'post_parent' => $selected_province,
							'nopaging' => true
						);
						$cities = new WP_Query($args);
						$disabled = '';
					}
				?>
				<?php if (isset($selected_city) || !$current_shipping_city): ?>
					<select id="city" name="city" <?php echo $disabled; ?> class="select-address" data-target="district">
						<option value="intro"><?php _e('Please select - leave empty if not applicalble', 'cell-store') ?></option>
						<?php if ( $cities && $cities->have_posts() ) : ?>
							<?php while ( $cities->have_posts() ) : $cities->the_post(); ?>
								<option value="<?php the_ID() ?>" <?php selected($selected_city, get_the_ID()) ?>><?php the_title() ?></option>
							<?php endwhile; ?>
						<?php endif; ?>
						<option value="other"><?php _e('Other', 'cell-store') ?></option>
					</select>
				<?php else: ?>
					<input type="text" class="input-xlarge select-address" id="city" name="city" value="<?php echo $current_shipping_city ?>" data-target="district" >
				<?php endif ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="district"><?php _e('District', 'cell-store') ?></label>
			<div class="controls">
				<?php
					$current_shipping_district =  $user_data['district'][0];
					
					$disabled = 'disabled="disabled"';
					if (is_numeric($current_shipping_district)) {
						$selected_district = $current_shipping_district;
					}
					if (isset($selected_city)) {
						$args = array(
							'post_type' => 'shipping-destination',
							'post_parent' => $selected_city,
							'nopaging' => true
						);
						$districts = new WP_Query($args);
						$disabled = '';
					}
				?>
				<?php if (isset($selected_district) || !$current_shipping_district): ?>
					<select id="district" name="district" <?php echo $disabled; ?> class="select-address">
						<option value="intro"><?php _e('Please select - leave empty if not applicalble', 'cell-store') ?></option>
						<?php if ( $districts && $districts->have_posts() ) : ?>
							<?php while ( $districts->have_posts() ) : $districts->the_post(); ?>
								<option value="<?php the_ID() ?>" <?php selected($selected_district, get_the_ID()) ?>><?php the_title() ?></option>
							<?php endwhile; ?>
						<?php endif; ?>
						<option value="other"><?php _e('Other', 'cell-store') ?></option>
					</select>
				<?php else: ?>
					<input type="text" class="input-xlarge select-address" id="district" name="district" value="<?php echo $current_shipping_district ?>">
				<?php endif ?>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<label class="control-label" for="have-shipping">
					<input type="checkbox" class="input-xlarge " id="have-shipping" name="have-shipping" value="1" <?php checked($user_data['have-shipping'][0], 1) ?>>
					<?php _e('Use another shipping address', 'cell-store') ?>
				</label>
			</div>
		</div>	
	</fieldset>
	<fieldset id="shipping-field">
		<legend><h4>Shipping</h4></legend>
		<div class="control-group">
			<label class="control-label" for="shipping-first-name"><?php _e('First Name', 'cell-store') ?></label>
			<div class="controls">
				<input type="text" class="input-xlarge " id="shipping-first-name" name="shipping-first-name" value="<?php echo $user_data['shipping-first-name'][0] ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="shipping-last-name"><?php _e('Last Name', 'cell-store') ?></label>
			<div class="controls">
				<input type="text" class="input-xlarge " id="shipping-last-name" name="shipping-last-name" value="<?php echo $user_data['shipping-last-name'][0] ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="shipping-email"><?php _e('Email', 'cell-store') ?></label>
			<div class="controls">
				<input type="text" class="input-xlarge " id="shipping-email" name="shipping-email" value="<?php echo $user_data['shipping-email'][0] ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="shipping-telephone"><?php _e('Telephone', 'cell-store') ?></label>
			<div class="controls">
				<input type="text" class="input-xlarge " id="shipping-telephone" name="shipping-telephone" value="<?php echo $user_data['shipping-telephone'][0] ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="shipping-company"><?php _e('Company', 'cell-store') ?></label>
			<div class="controls">
				<input type="text" class="input-xlarge " id="shipping-company" name="shipping-company" value="<?php echo $user_data['shipping-company'][0] ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="shipping-address"><?php _e('Address', 'cell-store') ?></label>
			<div class="controls">
				<textarea class="input-xlarge " id="shipping-address" name="shipping-address" ><?php echo $user_data['shipping-address'][0] ?></textarea>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="shipping-postcode"><?php _e('Postcode', 'cell-store') ?></label>
			<div class="controls">
				<input type="text" class="input-xlarge " id="shipping-postcode" name="shipping-postcode" value="<?php echo $user_data['shipping-postcode'][0] ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="shipping-country"><?php _e('Country', 'cell-store') ?></label>
			<div class="controls">
				<?php
					$args = array(
						'post_type' => 'shipping-destination',
						'post_parent' => 0,
						'nopaging' => true
					);
					$countries = new WP_Query($args);
					$current_shipping_country =  $user_data['shipping-country'][0];
					if (is_numeric($current_shipping_country)) {
						$selected_shipping_country = $current_shipping_country;
					}
				?>
				<?php if ($selected_shipping_country || !$current_shipping_country): ?>
					<select id="shipping-country" name="shipping-country" class="select-address" data-target="province">
						<?php if ( $countries->have_posts() ) : ?>
							<option value="intro"><?php _e('Please select - leave empty if not applicalble', 'cell-store') ?></option>
							<?php while ( $countries->have_posts() ) : $countries->the_post(); ?>
								<option value="<?php the_ID() ?>" <?php selected($selected_shipping_country, get_the_ID()) ?>><?php the_title() ?></option>
							<?php endwhile; ?>
							<option value="other"><?php _e('Other', 'cell-store') ?></option>
						<?php endif; ?>
					</select>
				<?php else: ?>
					<input type="text" class="input-xlarge select-address" id="shipping-country" name="shipping-country" value="<?php echo $current_shipping_country ?>" data-target="province">
				<?php endif ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="shipping-province"><?php _e('Province / State / County', 'cell-store') ?></label>
			<div class="controls">
				<?php
					$current_shipping_province =  $user_data['shipping-province'][0];
					
					$disabled = 'disabled="disabled"';
					if (is_numeric($current_shipping_province)) {
						$selected_shipping_province = $current_shipping_province;
					}
					if ($selected_shipping_country) {
						$args = array(
							'post_type' => 'shipping-destination',
							'post_parent' => $selected_shipping_country,
							'nopaging' => true
						);
						$provinces = new WP_Query($args);
						$disabled = '';
					}
				?>
				<?php if ($selected_shipping_province || !$current_shipping_province): ?>
					<select id="shipping-province" name="shipping-province" <?php echo $disabled; ?> class="select-address" data-target="city">
						<option value="intro"><?php _e('Please select - leave empty if not applicalble', 'cell-store') ?></option>
						<?php if ( $provinces && $provinces->have_posts() ) : ?>
							<?php while ( $provinces->have_posts() ) : $provinces->the_post(); ?>
								<option value="<?php the_ID() ?>" <?php selected($selected_shipping_province, get_the_ID()) ?>><?php the_title() ?></option>
							<?php endwhile; ?>
						<?php endif; ?>
						<option value="other"><?php _e('Other', 'cell-store') ?></option>
					</select>
				<?php else: ?>
					<input type="text" class="input-xlarge select-address" id="shipping-province" name="shipping-province" value="<?php echo $current_shipping_province ?>" data-target="city">
				<?php endif ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="shipping-city"><?php _e('City', 'cell-store') ?></label>
			<div class="controls">
				<?php
					$current_shipping_city =  $user_data['shipping-city'][0];
					
					$disabled = 'disabled="disabled"';
					if (is_numeric($current_shipping_city)) {
						$selected_shipping_city = $current_shipping_city;
					}
					if ($selected_shipping_province) {
						$args = array(
							'post_type' => 'shipping-destination',
							'post_parent' => $selected_shipping_province,
							'nopaging' => true
						);
						$cities = new WP_Query($args);
						$disabled = '';
					}
				?>
				<?php if ($selected_shipping_city || !$current_shipping_city): ?>
					<select id="shipping-city" name="shipping-city" <?php echo $disabled; ?> class="select-address" data-target="district">
						<option value="intro"><?php _e('Please select - leave empty if not applicalble', 'cell-store') ?></option>
						<?php if ( $cities && $cities->have_posts() ) : ?>
							<?php while ( $cities->have_posts() ) : $cities->the_post(); ?>
								<option value="<?php the_ID() ?>" <?php selected($selected_shipping_city, get_the_ID()) ?>><?php the_title() ?></option>
							<?php endwhile; ?>
						<?php endif; ?>
						<option value="other"><?php _e('Other', 'cell-store') ?></option>
					</select>
				<?php else: ?>
					<input type="text" class="input-xlarge select-address" id="shipping-city" name="shipping-city" value="<?php echo $current_shipping_city ?>" data-target="district" >
				<?php endif ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="shipping-district"><?php _e('District', 'cell-store') ?></label>
			<div class="controls">
				<?php
					$current_shipping_district =  $user_data['shipping-district'][0];
					
					$disabled = 'disabled="disabled"';
					if (is_numeric($current_shipping_district)) {
						$selected_shipping_district = $current_shipping_district;
					}
					if ($selected_shipping_city) {
						$args = array(
							'post_type' => 'shipping-destination',
							'post_parent' => $selected_shipping_city,
							'nopaging' => true
						);
						$districts = new WP_Query($args);
						$disabled = '';
					}
				?>
				<?php if (isset($selected_shipping_district) || !$current_shipping_district): ?>
					<select id="shipping-district" name="shipping-district" <?php echo $disabled; ?> class="select-address">
						<option value="intro"><?php _e('Please select - leave empty if not applicalble', 'cell-store') ?></option>
						<?php if ( $districts && $districts->have_posts() ) : ?>
							<?php while ( $districts->have_posts() ) : $districts->the_post(); ?>
								<option value="<?php the_ID() ?>" <?php selected($selected_shipping_district, get_the_ID()) ?>><?php the_title() ?></option>
							<?php endwhile; ?>
						<?php endif; ?>
						<option value="other"><?php _e('Other', 'cell-store') ?></option>
					</select>
				<?php else: ?>
					<input type="text" class="input-xlarge select-address" id="shipping-district" name="shipping-district" value="<?php echo $current_shipping_district ?>">
				<?php endif ?>
			</div>
		</div>
	</fieldset>
	<div class="form-actions">
		<button type="submit" class="btn btn-primary"><?php _e('Save', 'cell-store') ?></button>
		<?php wp_nonce_field('edit-author','edit-author_nonce'); ?>
		<input name="action" value="edit-author" type="hidden">
	</div>
</form>