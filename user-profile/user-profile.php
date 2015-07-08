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

		// add a shortcode
		// add_shortcode('cell-user-archive', array( $this, 'shortcode_output_archive'));

		// add a redirect on logout shortcode present
		// add_action( 'template_redirect', array($this, 'custom_shortcode_user_archive'));

		// add login ajax handler function
		add_action('wp_ajax_frontend_profile', array( $this, 'process_frontend_profile_fields'));

		// admin profile field
		add_action( 'show_user_profile', array( $this, 'admin_extra_profile_fields' ));
		add_action( 'edit_user_profile', array( $this, 'admin_extra_profile_fields' ));

		// admin script
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		// saving admin profile field
		add_action( 'personal_options_update', array( $this,'save_admin_extra_profile_fields' ));
		add_action( 'edit_user_profile_update', array( $this,'save_admin_extra_profile_fields' ));

		// add rewrite
		// add_filter( 'generate_rewrite_rules', array( $this, 'user_archive_rewrite') );
		// add_action( 'init', array($this, 'add_gl_rewrite_tag') );
	}
	
	function redirect_user(){
		if (isset($this->profile_args['page']) && is_page($this->profile_args['page']) && !is_user_logged_in()){
			$result['type'] = 'error';
			$result['message'] = __('Please login.', 'cell-user');
			if (isset($this->profile_args['redirect-noaccess'])) {
				$return = get_permalink( get_page_by_path( $this->profile_args['redirect-noaccess'] ) );
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

			// add addrees script
			wp_enqueue_script('profile-script', plugins_url('cell-user/js/profile.js'), array('jquery'), '1.0', true);
			wp_enqueue_style( 'cell-user-styles', plugins_url( 'cell-user/css/cell-user.css' ) );
			wp_localize_script( 'address', 'global', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

			if (isset($this->profile_args['include-script'])) {
				if (is_array($this->profile_args['include-script'])) {
					foreach ($this->profile_args['include-script'] as $value) {
						wp_enqueue_script( $value );
					}
				} else {
					wp_enqueue_script( $this->profile_args['include-script'] );
				}
			}

			if (locate_template('/cell-user/custom-profile-form.php')) {
				$template = get_template_directory().'/cell-user/custom-profile-form.php';
			} else {
				$template = 'views/custom-profile-form.php';
			}

			ob_start();
				include($template);
				$register_form = ob_get_contents();
			ob_end_clean();

			return $register_form;		
		} else {
			return false;
		}
	}

	// function shortcode_output_archive($atts){

	// 	$atts = shortcode_atts(
	// 		array(
	// 			'meta_key' => null,
	// 			'meta_value' => null,
	// 			'number' => 10,
	// 			'template' => null,
	// 		), $atts, 'user-profile' );
	// 	$meta_key = $atts['meta_key'];
	// 	$meta_value = $atts['meta_value'];
	// 	$number = $atts['number'];
	// 	$custom_template = $atts['template'];
		
	// 	if (isset($_GET['user'])) {
	// 		$user = get_user_by('slug', $_GET['user'] );

	// 		if ($user) {
	// 			$template = 'views/user-public-profile.php';

	// 			// check for custom default template
	// 			if (locate_template('cell-user/user-public-profile.php') != '') {
	// 				$template = get_stylesheet_directory().'/cell-user/user-public-profile.php';
	// 			}

	// 			// check for custom template
	// 			if ($custom_template && locate_template($custom_template) != '') {
	// 				$template = get_stylesheet_directory().'/'.$custom_template;
	// 			}

	// 			ob_start();
	// 			include $template;
	// 			$output_part = ob_get_contents();
	// 			ob_end_clean();
				
	// 			return $output_part;
	// 		} else {
	// 			return '<p> User not found </p>';
	// 		}
	// 	} else {

	// 		if (isset($this->profile_args['page-archive'])) {
	// 			$archive_page = $this->profile_args['page-archive'];
	// 		} else {
	// 			$archive_page = $this->default_archive_page;
	// 		}

	// 		$user_page_url = get_permalink(get_page_by_path($archive_page));

	// 		$user_page = 1;
	// 		if (isset($wp_query->query_vars['page']) && $wp_query->query_vars['page'] != 0) {
	// 			$user_page = $wp_query->query_vars['page'];
	// 		};

	// 		// TEST
	// 		// $number = 2;

	// 		$user_query_args = array(
	// 			'number' => $number
	// 		);
	// 		if ($meta_key) {
	// 			$user_query_args['meta_key'] = $meta_key;
	// 		}
	// 		if ($meta_value) {
	// 			$user_query_args['meta_value'] = $meta_value;
	// 		}
	// 		if ($user_page > 1) {
	// 			$user_query_args['offset'] = ($user_page - 1) * $number;
	// 		}

	// 		$user_query = new WP_User_Query( $user_query_args );
	// 		$total_page = ceil($user_query->total_users / $number);
			
	// 		// echo '<pre>';
	// 		// print_r($user_query);
	// 		// echo '</pre>';

	// 		$template = 'views/user-grid.php';

	// 		// check for custom default template
	// 		if (locate_template('cell-user/user-grid.php') != '') {
	// 			$template = get_stylesheet_directory().'cell-user/user-grid.php';
	// 		}

	// 		// check for custom template
	// 		if ($custom_template && locate_template($custom_template) != '') {
	// 			$template = get_stylesheet_directory().'/'.$custom_template;
	// 		}

	// 		$pagination_template = 'views/pagination.php';

	// 		// check for custom default template
	// 		if (locate_template('cell-user/pagination.php') != '') {
	// 			$template = get_stylesheet_directory().'cell-user/pagination.php';
	// 		}

	// 		if ($user_query->total_users > 0) {
	// 			$output = '';
	// 			foreach ($user_query->results as $key => $value) {
	// 				ob_start();
	// 				include $template;
	// 				$output_part = ob_get_contents();
	// 				ob_end_clean();
	// 				$output .= $output_part;
	// 			}

	// 			if ($user_query->total_users > $number) {
	// 				ob_start();
	// 				include $pagination_template;
	// 				$pagination = ob_get_contents();
	// 				ob_end_clean();

	// 				$output .= $pagination;
	// 			}
	// 			return $output;

	// 		} else {
	// 			return '<p> User not found</p>';
	// 		}
	// 	}
	// }

	// function user_archive_rewrite($wp_rewrite){
	// 	if (isset($this->profile_args['page-archive'])) {
	// 		$archive_page = $this->profile_args['page-archive'];
	// 	} else {
	// 		$archive_page = $this->default_archive_page;
	// 	}
	// 	$tax_rules = array(
	// 		$archive_page.'/(.+)/?$' => 'index.php?pagename='.$archive_page.'&user_name='. $wp_rewrite->preg_index(1),
	// 	);

	// 	$wp_rewrite->rules = $tax_rules + $wp_rewrite->rules;

	// }

	// function add_gl_rewrite_tag() {
	// 	// user related
	// 	add_rewrite_tag("%user_name%", '(.+)');
	// }


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
			if (isset($this->profile_args['redirect-success'])) {
				$return = get_permalink( get_page_by_path($this->profile_args['redirect-success']));
			} else {
				$return = $_POST['_wp_http_referer'];	
			}

			// save new email & password, if exist
			if (isset($_POST['user_email']) && isset($_POST['user_email_old'])) {
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
			}

			if (isset($_POST['user_passwors']) && $_POST['user_password'] != '') {
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
				// special way to save for checkbox
				if ($field_detail['type'] == 'checkbox') {
					if (isset($_POST[$field_key])) {
						update_user_meta( $_POST['user_id'], $field_key, $_POST[$field_key] );
					} else {
						delete_user_meta( $_POST['user_id'], $field_key);
					}
				}
				if (isset($_POST[$field_key])) {
					update_user_meta( $_POST['user_id'], $field_key, $_POST[$field_key] );
				}
				if (isset($_FILES[$field_key]) && ($_FILES[$field_key]['size'][0] > 0)) {
					$new_file = $_FILES[$field_key];
					$attached_file = attach_uploads($new_file);
					if($attached_file){
						update_user_meta( $_POST['user_id'], $field_key, $attached_file );
					}
				}
			}

			do_action( 'after_ajax_frontend_profile', $_POST);


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
			// special way to delete checkbox value
			if (!isset($_POST[$field_key]) && $field_detail['type'] == 'checkbox') {
				delete_user_meta( $_POST['user_id'], $field_key);
			}
		}
	}

	function register_admin_scripts() {

		$screen = get_current_screen();
		if ( $screen->base == 'profile' ) {
			wp_enqueue_script('profile-script', plugins_url('cell-user/js/profile.js'), array('jquery'), '1.0', true);
			wp_enqueue_style( 'cell-user-styles', plugins_url( 'cell-user/css/cell-user.css' ) );
			wp_localize_script( 'address', 'global', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		}
		if (isset($this->profile_args['include-script'])) {
			if (is_array($this->profile_args['include-script'])) {
				foreach ($this->profile_args['include-script'] as $value) {
					wp_enqueue_script( $value );
				}
			} else {
				wp_enqueue_script( $this->profile_args['include-script'] );
			}
		}
	}
}