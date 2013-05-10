<?php

/**
 * Register
 *
 * @package default
 * @author Dion
 **/

class CellRegister {

	function __construct($args) {
		// get the registration args
		$this->register_args = $args;
		$this->default_fields = array(
			'username' => array( // the key will be used in the label for attribute and the input name
				'title' => __('Username', 'cell-user'), // the label text
				'type' => 'text', // the input type or textarea
				'required' => 1, // is it required? 1 or 0
				'required_text' => __('(required)', 'cell-user'),
				'note' =>__('Use 3 - 15 character lowercase, numbers and \'- \' only', 'cell-user') // does it need a helper note, use inline html tags only
			),
			'email' => array(
				'title' => __('Email', 'cell-user'),
				'type' => 'text',
				'required' => 1,
				'note' => ''
			),
			'password' => array(
				'title' => __('Password', 'cell-user'),
				'type' => 'password',
				'required' => 1,
				'note' => ''
			)
		);

		if (isset($this->register_args['create-blog']) && $this->register_args['create-blog'] == 1) {
			// create blog
		}

		// add a shortcode
		add_shortcode('cell-user-register', array( $this, 'shortcode_output'));

		// add a redirect for logged out user
		add_action('template_redirect', array( $this, 'redirect_user'));

		// add login ajax handler function
		add_action('wp_ajax_nopriv_frontend_registration', array( $this, 'process_frontend_registration'));

		// register script
		add_action('init', array($this, 'cell_enqueue'));

		// print script
		add_action('wp_footer', array($this, 'print_register_script'));
	}

	function redirect_user(){
		if (isset($this->register_args['page']) && is_page($this->register_args['page']) && is_user_logged_in()){
			$result['type'] = 'notice';
			$result['message'] = __('You are logged in.', 'cell-user');
			if (isset($this->register_args['page-redirect'])) {
				$return = get_permalink( get_page_by_path( $this->register_args['page-redirect'] ) );
			} else{
				$return = get_bloginfo('url');
			}
			ajax_response($result,$return);
		}
	}

	function cell_enqueue() {
		wp_register_script('register-script', plugins_url('cell-user/js/register.js'), array('jquery'), '1.0', true);
		wp_enqueue_style( 'cell-user-styles', plugins_url( 'cell-user/css/cell-user.css' ) );
		wp_localize_script( 'address', 'global', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}


	function print_register_script() {
		global $enqueue;
		if ( ! $enqueue ){
			return;		
		}
		wp_print_scripts('register-script');
	}

	function shortcode_output(){

		if (isset($this->register_args['fields'])) {
			$registration_field = $this->register_args['fields'];
		} else {
			$registration_field = $this->default_fields;
		}

		if(!is_user_logged_in()){

			// add addrees script
			global $enqueue;
			$enqueue = true;

			ob_start();
			include('views/custom-registration-form.php');
			$register_form = ob_get_contents();
			ob_end_clean();

			return $register_form;
		} else {
			return false;
		}
	}

	function process_frontend_registration() {
		if ( empty($_POST) || !wp_verify_nonce($_POST['registration_nonce'],'frontend_registration') ) {
			echo 'Sorry, your nonce did not verify.';
			die();
		} else {
			// validate data
			$username = $_POST['username'];
			$email = $_POST['email'];
			$password = $_POST['password'];
			$return = $_POST['_wp_http_referer'];

			if(preg_match('/^[a-z0-9_-]{3,15}$/i', $username) == 0){
				$error['type'] = 'error';
				$error['message'] = __('Username not valid.', 'cell-user');
				ajax_response($error,$return);

			} elseif(!is_email($email))	{
				$error['type'] = 'error';
				$error['message'] = __('Email not valid.', 'cell-user');
				ajax_response($error,$return);

			} elseif($password == "") {
				$error['type'] = 'error';
				$error['message'] = __('Password empty.', 'cell-user');
				ajax_response($error,$return);

			} elseif( username_exists($username) || email_exists($email) ){
				$error['type'] = 'error';
				$error['message'] = __('Username or email already registered.', 'cell-user');
				ajax_response($error,$return);

			} else {
				$user_registration_data = array(
					'user_login' => $username,
					'user_pass' => $password,
					'user_email' => $email,
					'role' => get_option('default_role')
				);
				$user_id = wp_insert_user( $user_registration_data );
				$notifcation = wp_new_user_notification($user_id, $password);
				$login = wp_signon( array( 'user_login' => $username, 'user_password' => $password, 'remember' => false ), false );

				$return = get_bloginfo('url');

				// registration result
				$success['type'] = 'success';
				$success['message'] = __('Registration Success.', 'cell-user');
				ajax_response($success,$return);
				
			}		
			die();
		}
	}

}