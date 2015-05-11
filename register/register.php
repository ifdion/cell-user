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


		// add a shortcode
		add_shortcode('cell-user-register', array( $this, 'shortcode_output'));

		// add a redirect for logged out user
		add_action('template_redirect', array( $this, 'redirect_user'));

		// add login ajax handler function
		add_action('wp_ajax_nopriv_frontend_registration', array( $this, 'process_frontend_registration'));

		// add login ajax handler function
		add_action('wp_ajax_nopriv_confirm_registration', array( $this, 'process_confirm_registration'));

		// if this 
		if (isset($this->register_args['captcha'])){
			add_action('wp_ajax_nopriv_get_captcha_image', array( $this, 'get_captcha_image'));	
		}		

		// flush rewrite on registration
		add_action( 'wp_loaded', array($this, 'registration_flush_rewrite'));

	}

	function redirect_user(){
		if (isset($this->register_args['page']) && is_page($this->register_args['page']) && is_user_logged_in()){
			$result['type'] = 'warning';
			$result['message'] = __('You are logged in.', 'cell-user');
			if (isset($this->register_args['redirect-noaccess'])) {
				$return = get_permalink( get_page_by_path( $this->register_args['redirect-noaccess'] ) );
			} else{
				$return = get_bloginfo('url');
			}
			ajax_response($result,$return);
		}
	}

	function shortcode_output(){

		if (isset($this->register_args['fields'])) {
			$registration_field = $this->register_args['fields'];
		} else {
			$registration_field = $this->default_fields;
		}

		if(!is_user_logged_in()){

			// add addrees script
			wp_enqueue_script('register-script', plugins_url('cell-user/js/register.js'), array('jquery'), '1.0', true);
			wp_enqueue_style( 'cell-user-styles', plugins_url( 'cell-user/css/cell-user.css' ) );
			wp_localize_script( 'address', 'global', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

			if (locate_template('/cell-user/custom-registration-form.php')) {
				$template = get_template_directory().'/cell-user/custom-registration-form.php';
			} else {
				$template = 'views/custom-registration-form.php';
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

	function process_frontend_registration() {

		if ( empty($_POST) || !wp_verify_nonce($_POST['registration_nonce'],'frontend_registration') ) {
			echo 'Sorry, your nonce did not verify.';
			die();
		} else {

			$registration_data = $_POST;

			// validate data
			if (isset($registration_data['username'])) {
				$registration_data['username'] = $registration_data['username'];
			} else {
				$registration_data['username'] = $registration_data['email'];
			}
			$registration_data['email'] = $registration_data['email'];
			$registration_data['password'] = $registration_data['password'];
			$registration_data['_wp_http_referer'] = $registration_data['_wp_http_referer'];

			if (isset($this->register_args['captcha'])) {
				$captcha = $registration_data['captcha'];
				if ($captcha == $_SESSION['cap_code']) {
					
				}  else {
					$error['type'] = 'error';
					$error['message'] = __('Invalid captcha.', 'cell-user');
					ajax_response($error,$registration_data['_wp_http_referer']);
				}
			}

			// if(preg_match('/^[.@a-z0-9_-]{3,25}$/i', $registration_data['username']) == 0){
			// if(preg_match('/^[.@a-z0-9_-]$/i', $registration_data['username']) == 0){
			// 	$error['type'] = 'error';
			// 	$error['message'] = __('Username not valid.', 'cell-user');
			// 	ajax_response($error,$registration_data['_wp_http_referer']);

			// } elseif(!is_email($registration_data['email']))	{
			if(!is_email($registration_data['email']))	{
				$error['type'] = 'error';
				$error['message'] = __('Email not valid.', 'cell-user');
				ajax_response($error,$registration_data['_wp_http_referer']);

			} elseif($registration_data['password'] == "") {
				$error['type'] = 'error';
				$error['message'] = __('Password empty.', 'cell-user');
				ajax_response($error,$registration_data['_wp_http_referer']);

			} elseif( username_exists($registration_data['username']) || email_exists($registration_data['email']) ){
				$error['type'] = 'error';
				$error['message'] = __('Username or email already registered.', 'cell-user');
				ajax_response($error,$registration_data['_wp_http_referer']);

			} else {

				// check if registration-confirmation is true

				if (isset($this->register_args['registration-confirmation']) && $this->register_args['registration-confirmation'] == TRUE) {
				
					$registration = $this->process_pre_registration($registration_data);

				} else {

					$registration = $this->process_registration($registration_data);
				}				
			}
			die();
		}
	}

	function process_pre_registration($registration_data){
		$registration_email = $registration_data['email'];
		$registration_username = $registration_data['username'];
		$registration_nonce = $registration_data['registration_nonce'];
		$blog_title = get_bloginfo( 'name' );
		
		$pre_registration_code = 'cell-pre-reg_'.$registration_email.'_'.$registration_nonce;
		$pre_registration_option = add_option( $pre_registration_code, $registration_data, '', 'no' );

		$confirmation_link = admin_url('admin-ajax.php').'?action=confirm_registration&email='.$registration_email.'&iregistration_nonce='.$registration_nonce;

		// echo $pre_registration_code;

		// echo 'send email to '.$registration_email.' with the code '.$registration_nonce; 

		$email_title = sprintf(__('Please Confirm Your Registration to %1$s ', 'cell-user'), $blog_title);

		$email_message = sprintf(__('Dear <strong>%1$s</strong>, <br/> Please Confirm Your Registration to %2$s by <a href="%3$s"> clicking this link  </a> <br><br> or visit the URL below in your browser <br/> %3$s', 'cell-user'), $registration_username, $blog_title, $confirmation_link);

		if (function_exists('cell_email')) {
			cell_email($registration_email,$email_title,$email_message);
		} else{
			mail($registration_email, $email_title, $email_message);
		}

		// registration result
		$success['type'] = 'success';
		$success['message'] = __('Confirmation email sent. Please check your inbox or spam folder to confirm registration. ', 'cell-user');
		ajax_response($success,get_bloginfo('url' ));

	}

	function process_registration($registration_data){
		$user_registration_data = array(
			'user_login' => sanitize_user($registration_data['username']),
			'user_pass' => $registration_data['password'],
			'user_email' => sanitize_email( $registration_data['email'] ),
			'role' => get_option('default_role'),
			'display_name' => $registration_data['username']
		);

		// check if user_url is submitted
		if (isset($registration_data['user_url']) && $registration_data['user_url'] != '') {
			$user_registration_data['user_url'] = $registration_data['user_url'];
		}

		$user_id = wp_insert_user( $user_registration_data );		

		if (isset($this->register_args['redirect-success'])) {
			$page = get_page_by_path($this->register_args['redirect-success']);
			if ($page) {
				$return = get_permalink( get_page_by_path( $this->register_args['redirect-success'] ) );
			} else {
				$return = call_user_func_array($this->register_args['redirect-success'], $user_registration_data);
			}
		} else {
			$return = add_query_arg(array('new'=>1), get_bloginfo('url'));
		}

		// create blog
		if (isset($this->register_args['create-blog']) && $this->register_args['create-blog'] == TRUE) {

			$domain = home_url();
			$domain = str_replace('https://', '', $domain);
			$domain = str_replace('http://', '', $domain);
			$path = '/'.$registration_data['username'].'/';

			$blog_option = $this->register_args['blog-options'];

			// create blog
			$blog_id = wpmu_create_blog( $domain, $path, $registration_data['username'], $user_id,$blog_option,1);

			// add user to main blog
			$main_blog = add_user_to_blog( 1, $user_id, 'subscriber' );

			// switch blog
			switch_to_blog( $blog_id );

			// register blog hook
			do_action( 'cell-blog-register', $registration_data);

			if (isset($this->register_args['redirect-success'])) {
				$return = get_permalink( get_page_by_path( $this->register_args['redirect-success'] ) );
				$return = add_query_arg(array('new'=>'1'), $return);
			} else {
				$return = add_query_arg(array('new'=>'1'), get_bloginfo('url'));
			}
		}

		// save other attributes as usermeta
		$default_field_key = array('username','email','password','registration_nonce','_wp_http_referer', 'action');
		foreach ($registration_data as $field_key => $field_value) {
			if (!in_array($field_key, $default_field_key)) {
				add_user_meta( $user_id, $field_key, $registration_data[$field_key], TRUE );
			}
		}

		// notification
		$notification = wp_new_user_notification($user_id, $registration_data['password']);
		$login = wp_signon( array( 'user_login' => $registration_data['username'], 'user_password' => $registration_data['password'], 'remember' => false ), false );

		// register hook
		do_action( 'cell-register', $registration_data );

		// registration result
		$success['type'] = 'success';
		$success['message'] = __('Registration Success.', 'cell-user');
		ajax_response($success,$return);
	}

	function process_confirm_registration(){
		$registration_email = $_REQUEST['email'];
		$registration_nonce = $_REQUEST['iregistration_nonce'];
		$pre_registration_code = 'cell-pre-reg_'.$registration_email.'_'.$registration_nonce;

		// echo('registration code :'.$pre_registration_code);

		$registration_data = get_option($pre_registration_code );

		if ($registration_data) {
			$registration = $this->process_registration($registration_data);
		} else {
			$error['type'] = 'error';
			$error['message'] = __('Registration invalid. [code:01]', 'cell-user');
			ajax_response($error,$registration_data['_wp_http_referer']);
		}

		// echo '<pre>';
		// print_r($registration_data);
		// echo '</pre>';
		// wp_die('die' );
	}

	function get_captcha_image() {
		$random_string = wp_generate_password( 6, FALSE, FALSE);
		$_SESSION['cap_code'] = $random_string;

		if (isset($this->register_args['captcha-image'])) {
			$image_bg = $this->register_args['captcha-image'];
		} else {
			$image_bg = CELL_USER_PATH .'img/cap_bg.jpg';
		}

		$newImage = imagecreatefromjpeg( $image_bg );
		$txtColor = imagecolorallocate($newImage, 255, 255, 255);
		$txtColor = imagecolorallocate($newImage, 0, 0, 0);
		imagestring($newImage, 5, 5, 5, $random_string, $txtColor);
		header("Content-type: image/jpeg");
		imagejpeg($newImage);

		die();
	}

	function registration_flush_rewrite() {
		if (isset($_REQUEST['new'])) {
			flush_rewrite_rules();
		}
	}

}