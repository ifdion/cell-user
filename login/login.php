<?php

/**
 * Loginform
 *
 * @package default
 * @author Dion
 **/


include_once 'hooks.php';

class CellLogin {

	function __construct($args) {

		// get the login args
		$this->login_args = $args;

		// add a shortcode
		add_shortcode('cell-user-login', array( $this, 'shortcode_output'));

		// add a shortcode
		add_shortcode('cell-user-logout', array( $this, 'shortcode_output_logout'));

		// add a redirect on logout shortcode present
		add_action( 'template_redirect', array($this, 'custom_shortcode_logout'));

		// add a redirect for logged out user
		add_action('template_redirect', array( $this, 'redirect_user'));

		// add login ajax handler function
		add_action('wp_ajax_nopriv_frontend_login', array( $this, 'process_login'));

		// add forgot password ajax handler function
		add_action('wp_ajax_nopriv_frontend_forgot_password', array($this, 'process_forgot_password'));

		// add reset password ajax handler function
		add_action('wp_ajax_nopriv_frontend_reset_password', array($this, 'process_frontend_reset_password'));
		
	}
	

	/* required script 
	---------------------------------------------------------------
	*/

	function redirect_user(){
		global $current_user;
		if (isset($this->login_args['page']) && is_page($this->login_args['page']) && is_user_logged_in()){
			$result['type'] = 'warning';
			$result['message'] = __('You are logged in.', 'cell-user');
			if (isset($this->login_args['redirect-success'])) {
				if (is_page( $this->login_args['redirect-success'] )) {
					$return = get_permalink( get_page_by_path( $this->login_args['redirect-success'] ) );
				} else {
					$return = call_user_func( $this->login_args['redirect-success'] , $current_user->user_name);
				}
				$return = get_permalink( get_page_by_path( $this->login_args['redirect-success'] ) );
			} else{
				$return = get_bloginfo('url');
			}
			ajax_response($result,$return);
		}
	}

	function shortcode_output($atts){
		if(!is_user_logged_in()){

			// add addrees script
			wp_enqueue_script('login-script', plugins_url('cell-user/js/login.js'), array('jquery'), '1.0', true);
			wp_enqueue_style( 'cell-user-styles', plugins_url( 'cell-user/css/cell-user.css' ) );
			wp_localize_script( 'address', 'global', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );


			if (locate_template('/cell-user/custom-login-form.php')) {
				$template_login = get_template_directory().'/cell-user/custom-login-form.php';
			} else {
				$template_login = 'views/custom-login-form.php';
			}

			if (locate_template('/cell-user/custom-forgot-password-form.php')) {
				$template_forgot_password = get_template_directory().'/cell-user/custom-forgot-password-form.php';
			} else {
				$template_forgot_password = 'views/custom-forgot-password-form.php';
			}

			if (locate_template('/cell-user/custom-reset-password-form.php')) {
				$template_reset_password = get_template_directory().'/cell-user/custom-reset-password-form.php';
			} else {
				$template_reset_password = 'views/custom-reset-password-form.php';
			}

			ob_start();
				if (isset($_REQUEST['forgot-password']) && $_REQUEST['forgot-password']==1) {
					include($template_forgot_password);
				} elseif( isset($_REQUEST['reset-password']) && $_REQUEST['reset-password']==1) {
					include($template_reset_password);
				} else {
					include($template_login);
				}
				$login_form = ob_get_contents();
			ob_end_clean();

			return $login_form;
		} else {
			return false;
		}
	}

	function shortcode_output_logout(){
		if(is_user_logged_in()){
			return 'login';
		} else {
			return 'logout';
		}
	}

	function custom_shortcode_logout() {
		global $post;
		if( (is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'cell-user-logout'))) {
			wp_logout();
			$return = home_url();

			$result['type'] = 'success';
			$result['message'] = __('Logout Success.', 'cell-user');
			ajax_response($result,$return);
		}
	}

	function process_login() {

		// validate data
		$return_error = get_bloginfo('url');
		$return = get_bloginfo('url');
		if (isset($_REQUEST['_wp_http_referer'])) {
			$return_error = $_REQUEST['_wp_http_referer'];
		}

		// detect empty field
		if (!isset($_REQUEST['username']) || !isset($_REQUEST['password']) || $_REQUEST['username'] == "" || $_REQUEST['password'] == "") {
			$result['type'] = 'danger';
			$result['message'] = __('Field empty.', 'cell-user');
			ajax_response($result,$return_error);
		}

		if (isset($_REQUEST['return_success'])) {
			$return = get_permalink(get_page_by_path($_REQUEST['return_success'] ));
		}

		// login data
		$username = $_REQUEST['username'];
		$password = $_REQUEST['password'];
		
		// check
		if (email_exists($username)) {
			$user = get_user_by('email', $username);
		} elseif (username_exists($username)) {
			$user = get_user_by('login', $username);
		}

		if (isset($user)) {
			$user_meta = get_user_meta( $user->ID );
			// get return from user data
			if (isset($this->login_args['redirect-success'])) {
				if (is_page( $this->login_args['redirect-success'] )) {
					$return = get_permalink( get_page_by_path( $this->login_args['redirect-success'] ) );
				} else {
					$return = call_user_func_array( $this->login_args['redirect-success'] , array($user->ID));
				}
			}

			$login = wp_signon( array( 'user_login' => $user->user_login, 'user_password' => $password, 'remember' => false ), false );
			if (is_wp_error($login)) {
				$result['type'] = 'danger';
				$result['message'] = __('Login error, please check your username and password.', 'cell-user');
				ajax_response($result,$return_error);
			} else {
				$result['type'] = 'success';
				$result['message'] = __('Login Success.', 'cell-user');
				$result['content']['user_data'] = apply_filters( 'cell-user-login-data', $user );
				$result['content']['user_meta'] = apply_filters('cell-user-login-meta', $user_meta);
				ajax_response($result,$return);
			}

		} else {
			$result['type'] = 'danger';
			$result['message'] = __('Login error, please check your username and password.', 'cell-user');
			ajax_response($result,$return_error);
		}
	}

	function process_forgot_password() {
		if ( empty($_POST) || !wp_verify_nonce($_POST['forgot_password_nonce'],'frontend_forgot_password') ) {
			echo 'Sorry, your nonce did not verify.';
			die();
		} else {
			// validate data
			$username = $_POST['username'];
			$return = $_POST['_wp_http_referer'];
			if (!$username) {
				$result['type'] = 'danger';
				$result['message'] = __('Field empty.', 'cell-user');
				ajax_response($result,$return);
				die();
			} else {
				if (username_exists($username)) {
					$user = get_user_by('login', $username);
				} elseif(email_exists($username)) {
					$user = get_user_by('email', $username);
				} else {
					$result['type'] = 'danger';
					$result['message'] = __('Username or Email does not exist.', 'cell-user');
					ajax_response($result,$return);
					die();
				}

				if ($user) {
					$user_login = $user->user_login;
					$user_email = $user->user_email;
					global $wpdb;

					$key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
					if(empty($key)) {
						$key = wp_generate_password(20, false);
						$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));

					}

					$return_array = explode('?', $return);
					$base_url = $return_array[0];
					$activation_link = get_bloginfo('url').$base_url.'?reset-password=1&key='.$key.'&login='.$user_login;

					$mail_title = sprintf(__('Reset Password Activation Key at %s', 'cell-user'), get_bloginfo('name'));

					$message = sprintf(__('
						It likes like you (hopefully) want to reset your password for your %1$s account.
						To reset your password, visit the following address, otherwise just ignore this email and nothing will happen.
						%2$s
						Have a nice day', 'cell-user'), get_bloginfo('name'), $activation_link );

					if (function_exists('cell_email')) {
						cell_email($user_email, $mail_title, wpautop($message));
					} else {
						wp_mail($user_email, $mail_title, $message);
					}

					$result['type'] = 'success';
					$result['message'] = __('Activation email sent', 'cell-user');
					ajax_response($result,$return);
					die();
				}
			}
		}
	}

	function process_frontend_reset_password() {
		if ( empty($_POST) || !wp_verify_nonce($_POST['reset_password_nonce'],'frontend_reset_password') ) {
			echo 'Sorry, your nonce did not verify.';
			die();
		} else {
			// validate data
			$return = $_POST['_wp_http_referer'];
			$reset_key = $_POST['key'];
			$user_login = $_POST['login'];
			$password1 = $_POST['password1'];
			$password2 = $_POST['password2'];

			global $wpdb;
			$user_data = $wpdb->get_row($wpdb->prepare("SELECT ID, user_login, user_email FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $reset_key, $user_login));

			if(!$user_data){
				$result['type'] = 'danger';
				$result['message'] = __('User not found.', 'cell-user');
				ajax_response($result,$return);
			} elseif(!$reset_key) {
				$result['type'] = 'danger';
				$result['message'] = __('Activation key not found.', 'cell-user');
				ajax_response($result,$return);
			} else {
				if ($password1 &&($password1 != $password2)) {
					$result['type'] = 'danger';
					$result['message'] = __('Input password is incorrect.', 'cell-user');
					ajax_response($result,$return);
				} else {
					wp_set_password($password1, $user_data->ID);

					// remove activation key
					$wpdb->update( $wpdb->users, array("user_activation_key" => ""), array("ID" => $user_data->ID));

					$return_array = explode('?', $return);
					$base_url = $return_array[0];
					
					$result['type'] = 'success';
					$result['message'] = __('Password reset.', 'cell-user');
					ajax_response($result,$base_url);
				}
			}
			die();
		}
	}
}