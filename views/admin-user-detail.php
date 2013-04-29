		<h3 id="billing"><?php _e('Billing Address', 'cell-store') ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="telephone"><?php _e('Telephone Number', 'cell-store') ?></label></th>
				<td><input type="text" id="telephone" name="telephone" value="<?php echo get_user_meta($user->ID, 'telephone', true) ?>"></td>
			</tr>
			<tr>
				<th><label for="company"><?php _e('Company', 'cell-store') ?></label></th>
				<td><input type="text" id="company" name="company" value="<?php echo get_user_meta($user->ID, 'company', true) ?>"></td>
			</tr>
			<tr>
				<th><label for="address"><?php _e('Address', 'cell-store') ?></label></th>
				<td><textarea class="regular-text" id="address" name="address" ><?php echo get_user_meta($user->ID, 'address', true) ?></textarea></td>
			</tr>
			<tr>
				<th><label for="postcode"><?php _e('Postcode', 'cell-store') ?></label></th>
				<td>
					<input type="text" id="postcode" name="postcode" value="<?php echo get_user_meta($user->ID, 'postcode', true) ?>">
				</td>
			</tr>
			<tr>
				<?php $country = get_user_meta($user->ID, 'country', true) ?>
				<th><label for="country"><?php _e('Country', 'cell-store') ?></label></th>
				<td>
					<input type="text" id="country" name="country" value="<?php echo $country ?>">
					<?php if(is_numeric($country)) echo get_the_title($country); ?>
				</td>
			</tr>
			<tr>
				<?php $province = get_user_meta($user->ID, 'province', true) ?>
				<th><label for="province"><?php _e('State / Province', 'cell-store') ?></label></th>
				<td>
					<input type="text" id="province" name="province" value="<?php echo $province ?>">
					<?php if(is_numeric($province)) echo get_the_title($province); ?>
				</td>
			</tr>
			<tr>
				<?php $city = get_user_meta($user->ID, 'city', true) ?>
				<th><label for="city"><?php _e('City', 'cell-store') ?></label></th>
				<td>
					<input type="text" id="city" name="city" value="<?php echo $city ?>">
					<?php if(is_numeric($city)) echo get_the_title($city); ?>
				</td>
			</tr>
			<tr>
				<?php $district = get_user_meta($user->ID, 'district', true) ?>
				<th><label for="district"><?php _e('District', 'cell-store') ?></label></th>
				<td>
					<input type="text" id="district" name="district" value="<?php echo $district ?>">
					<?php if(is_numeric($district)) echo get_the_title($district); ?>
				</td>
			</tr>
		</table>
		<h3 id="shipping"><?php _e('Shipping Address', 'cell-store') ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="shipping-first-name"><?php _e('Name', 'cell-store') ?></label></th>
				<td>
				<input type="text" id="shipping-first-name" name="shipping-first-name" value="<?php echo get_user_meta($user->ID, 'shipping-first-name', true) ?>">
				<input type="text" id="shipping-last-name" name="shipping-last-name" value="<?php echo get_user_meta($user->ID, 'shipping-last-name', true) ?>">
				</td>
			</tr>
			<tr>
				<th><label for="shipping-telephone"><?php _e('Telephone Number', 'cell-store') ?></label></th>
				<td>
				<input type="text" id="shipping-telephone" name="shipping-telephone" value="<?php echo get_user_meta($user->ID, 'shipping-telephone', true) ?>">
				</td>
			</tr>
			<tr>
				<th><label for="shipping-company"><?php _e('Company', 'cell-store') ?></label></th>
				<td>
				<input type="text" id="shipping-company" name="shipping-company" value="<?php echo get_user_meta($user->ID, 'shipping-company', true) ?>">
				</td>
			</tr>
			<tr>
				<th><label for="shipping-address"><?php _e('Address', 'cell-store') ?></label></th>
				<td>
				<textarea class="regular-text" id="shipping-address" name="shipping-address" ><?php echo get_user_meta($user->ID, 'shipping-address', true) ?></textarea>
				</td>
			</tr>
			<tr>
				<th><label for="shipping-postcode"><?php _e('Postcode', 'cell-store') ?></label></th>
				<td>
				<input type="text" id="shipping-postcode" name="shipping-postcode" value="<?php echo get_user_meta($user->ID, 'shipping-postcode', true) ?>">
				</td>
			</tr>
			<tr>
				<?php $shipping_country = get_user_meta($user->ID, 'shipping-country', true) ?>
				<th><label for="shipping-country"><?php _e('Country', 'cell-store') ?></label></th>
				<td>
					<input type="text" id="shipping-country" name="shipping-country" value="<?php echo $shipping_country ?>">
					<?php if(is_numeric($shipping_country)) echo get_the_title($shipping_country); ?>
				</td>
			</tr>
			<tr>
				<?php $shipping_province = get_user_meta($user->ID, 'shipping-province', true) ?>
				<th><label for="shipping-province"><?php _e('State / Province', 'cell-store') ?></label></th>
				<td>
					<input type="text" id="shipping-province" name="shipping-province" value="<?php echo $shipping_province ?>">
					<?php if(is_numeric($shipping_province)) echo get_the_title($shipping_province); ?>
				</td>
			</tr>
			<tr>
				<?php $shipping_city = get_user_meta($user->ID, 'shipping-city', true) ?>
				<th><label for="shipping-city"><?php _e('City', 'cell-store') ?></label></th>
				<td>
					<input type="text" id="shipping-city" name="shipping-city" value="<?php echo $shipping_city ?>">
					<?php if(is_numeric($shipping_city)) echo get_the_title($shipping_city); ?>
				</td>
			</tr>
			<tr>
				<?php $shipping_district = get_user_meta($user->ID, 'shipping-district', true) ?>
				<th><label for="shipping-district"><?php _e('District', 'cell-store') ?></label></th>
				<td>
					<input type="text" id="shipping-district" name="shipping-district" value="<?php echo $shipping_district ?>">
					<?php if(is_numeric($shipping_district)) echo get_the_title($shipping_district); ?>
				</td>
			</tr>
		</table>