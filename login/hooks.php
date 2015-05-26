<?php

/**
 * output default user data
 *
 * @return void
 * @author 
 **/

if (!function_exists('cell_user_default_login_data')) {
	function cell_user_default_login_data($user){
		$default_data = array('ID', 'user_login', 'user_nicename', 'user_email', 'user_url', 'display_name');
		$user_object = new stdClass();

		$user_object->ID = $user->ID;
		$user_object->data = [];

		foreach ($user->data as $data_key => $data_value) {
			if (in_array($data_key, $default_data)) {
				$user_object->data[$data_key] = $data_value;
			}
		}
		return $user_object;
	}
}

add_filter( 'cell-user-login-data', 'cell_user_default_login_data' );

/**
 * output default user meta
 *
 * @return void
 * @author 
 **/

if (!function_exists('cell_user_default_login_meta')) {
	function cell_user_default_login_meta($meta){
		$default_meta = array('nickname', 'first_name', 'last_name', 'description');
		$user_meta = [];

		foreach ($meta as $meta_key => $meta_value) {
			if (in_array($meta_key, $default_meta)) {
				$user_meta[$meta_key] = $meta_value;
			}
		}
		return $user_meta;
	}
}

add_filter( 'cell-user-login-meta', 'cell_user_default_login_meta' );






?>