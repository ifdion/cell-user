<?php

/**
 * Register
 *
 * @package default
 * @author Dion
 **/

class CellProfile {

	function __construct($args) {

		// get the profile args
		$this->profile_args = $args;
		$this->default_meta = array(
			'base' => array(
				'title' => __('Base Profile', 'cell-user'),
				'class' => 'base-profile fieldset',
				'fields' => array(
					'first_name' => array(
						'title' => __('First Name', 'cell-user'),
						'type' => 'text',
					),
					'last_name' => array(
						'title' => __('Last Name', 'cell-user'),
						'type' => 'text',
					),
					'description' => array(
						'title' => __('Description', 'cell-user'),
						'type' => 'text',
					),
				),
			),
		);
		$this->admin_preset_meta = array('first_name','last_name','nickname','description','rich_editing','comment_shortcuts','admin_color','use_ssl','show_admin_bar_front','aim','yim','jabber');

		// add a shortcode
		add_shortcode('cell-user-profile', array( $this, 'shortcode_output'));

		// add a redirect for logged out user
		add_action('template_redirect', array( $this, 'redirect_user'));

		// add login ajax handler function
		add_action('wp_ajax_frontend_profile', array( $this, 'process_frontend_profile_fields'));

		// admin profile field
		add_action( 'show_user_profile', array( $this, 'admin_extra_profile_fields' ));
		add_action( 'edit_user_profile', array( $this, 'admin_extra_profile_fields' ));

		// saving admin profile field
		add_action( 'personal_options_update', array( $this,'save_admin_extra_profile_fields' ));
		add_action( 'edit_user_profile_update', array( $this,'save_admin_extra_profile_fields' ));
	}
	
	function redirect_user(){
		if (isset($this->profile_args['page']) && is_page($this->profile_args['page']) && !is_user_logged_in()){
			$result['type'] = 'error';
			$result['message'] = __('Please login.', 'cell-user');
			if (isset($this->profile_args['page-redirect'])) {
				$return = get_permalink( get_page_by_path( $this->profile_args['page-redirect'] ) );
			} else{
				$return = get_bloginfo('url');
			}
			ajax_response($result,$return);
		}
	}

	function shortcode_output(){

		if (isset($this->profile_args['fieldset'])) {
			$profile_field = $this->profile_args['fieldset'];
		} else {
			$profile_field = $this->default_meta;
		}

		if(is_user_logged_in()){
			ob_start();
				include('views/custom-profile-form.php');
				$register_form = ob_get_contents();
			ob_end_clean();

			return $register_form;		
		} else {
			return false;
		}
	}

	function process_frontend_profile_fields() {

		if (isset($this->profile_args['fieldset'])) {
			$profile_field = $this->profile_args['fieldset'];
		} else {
			return false;
		}

		if ( empty($_POST) || !wp_verify_nonce($_POST['profile_nonce'],'frontend_profile') ) {
			echo 'Sorry, your nonce did not verify.';
			die();
		} else {

			// set return value
			$return = $_POST['_wp_http_referer'];

			// save new email & password, if exist
			if ($_POST['user_email'] != $_POST['user_email_old'] && is_email( $_POST['user_email'] )) {
				if (email_exists( $_POST['user_email'] )) {
					$result['type'] = 'error';
					$result['message'] = __('Email already used.', 'cell-user');
					ajax_response($result,$return);
				}
				$userdata['user_email'] = $_POST['user_email'];
				$userdata['ID'] = $_POST['user_id'];
				$update_user = true;
			}
			if ($_POST['user_password'] != '') {
				if ($_POST['user_password'] != $_POST['user_password_retype']) {
					$result['type'] = 'error';
					$result['message'] = __('Password did not match.', 'cell-user');
					ajax_response($result,$return);
				} else{
					$userdata['user_pass'] = $_POST['user_password'];
					$userdata['ID'] = $_POST['user_id'];
					$update_user = true;
				}
			}
			if (isset($update_user)) {
				wp_update_user( $userdata );
			}


			// merge fieldset's fields
			$user_fields = array();
			foreach ($profile_field as $key => $value){
				$user_fields = array_merge($user_fields, $value['fields']);
			}
			// save each field
			foreach ($user_fields as $field_key => $field_detail) {
				if (isset($_POST[$field_key])) {
					update_user_meta( $_POST['user_id'], $field_key, $_POST[$field_key] );
				}
			}


			$result['type'] = 'success';
			$result['message'] = __('Profile updated.', 'cell-user');
			ajax_response($result,$return);
		}
	}

	function admin_extra_profile_fields(){

		if (isset($this->profile_args['fieldset'])) {
			$profile_field = $this->profile_args['fieldset'];
		} else {
			return false;
		}

		include('views/admin-user-detail.php');
	}

	function save_admin_extra_profile_fields( $user_id ) {
		if ( !current_user_can( 'edit_user', $user_id ) ){
			return false;	
		}

		// get user fields
		if (isset($this->profile_args['fieldset'])) {
			$profile_field = $this->profile_args['fieldset'];
		} else {
			$profile_field = $this->default_meta;
		}

		// merge fieldset's fields
		$user_fields = array();
		foreach ($profile_field as $key => $value){
			$user_fields = array_merge($user_fields, $value['fields']);
		}

		// save each field
		foreach ($user_fields as $field_key => $field_detail) {
			if (isset($_POST[$field_key])) {
				update_user_meta( $_POST['user_id'], $field_key, $_POST[$field_key] );
			}
		}
	}
}