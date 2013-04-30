<?php

/**
 * Loginform
 *
 * @package default
 * @author Dion
 **/


class CellRegister {

	function __construct($args) {
		// get the login page
		$login_page = $args['page'];

		// include style and scrip
		add_action( 'template_redirect', array( $this, 'cell_profile_script' ));

		// add a shortcode
		add_shortcode('cell-user-login', array( $this, 'shortcode_output'));

		// add login ajax handler function
		add_action('wp_ajax_nopriv_frontend_login', array( $this, 'process_login'));

		// add forgot password ajax handler function
		add_action('wp_ajax_nopriv_frontend_forgot_password', array($this, 'process_forgot_password'));

		// add reset password ajax handler function
		add_action('wp_ajax_nopriv_frontend_reset_password', array($this, 'process_frontend_reset_password'));
	}
	
	public function cell_profile_script(){
		if (is_page($login_page)){
			wp_enqueue_script('login-script', plugins_url().'/cell-user/js/login.js', array('jquery'), '0.1', true);
			wp_enqueue_style( 'cell-user-styles', plugins_url( 'cell-user/css/cell-user.css' ) );
			wp_localize_script( 'ajaxurl', 'global', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		}	
	}

	public function shortcode_output(){
		if(!is_user_logged_in()){

			ob_start();
				if (isset($_REQUEST['forgot-password']) && $_REQUEST['forgot-password']==1) {
					include('views/custom-forgot-password-form.php');
				} elseif( isset($_REQUEST['reset-password']) && $_REQUEST['reset-password']==1) {
					include('views/custom-reset-password-form.php');
				} else {
					include('views/custom-login-form.php');
				}
				$login_form = ob_get_contents();
			ob_end_clean();

			return $login_form;
		} else {
			return false;
		}
	}

	public function process_login() {
		if ( empty($_POST) || !wp_verify_nonce($_POST['login_nonce'],'frontend_login') ) {
			echo 'Sorry, your nonce did not verify.';
			die();
		} else {
			// validate data
			$username = $_POST['username'];
			$password = $_POST['password'];
			$return = $_POST['_wp_http_referer'];

			if ($username == "" || $password == "") {
				$result['type'] = 'error';
				$result['message'] = __('Field empty.', 'cell-user');
				ajax_response($result,$return);

			} elseif (email_exists($username)) {
				$user = get_user_by('email', $username);
				$login = wp_signon( array( 'user_login' => $user->user_login, 'user_password' => $password, 'remember' => false ), false );
				if (is_wp_error($login)) {
					$result['type'] = 'error';
					$result['message'] = __('Invalid Password.', 'cell-user');
					ajax_response($result,$return);
				} else {
					$success['type'] = 'success';
					$success['message'] = __('Login Success.', 'cell-user');
					ajax_response($success,$return);
				}
			} elseif (username_exists($username)) {
				$login = wp_signon( array( 'user_login' => $username, 'user_password' => $password, 'remember' => false ), false );
				if (is_wp_error($login)) {
					$result['type'] = 'error';
					$result['message'] = __('Invalid Password.', 'cell-user');
					ajax_response($result,$return);
				} else {
					$success['type'] = 'success';
					$success['message'] = __('Login Success.', 'cell-user');
					ajax_response($success,$return);
				}
			} else {
				$success['type'] = 'error';
				$success['message'] = __('Username or Email does not exist.', 'cell-user');
				ajax_response($success,$return);

			}
			die();
		}
	}

	public function process_forgot_password() {
		if ( empty($_POST) || !wp_verify_nonce($_POST['forgot_password_nonce'],'frontend_forgot_password') ) {
			echo 'Sorry, your nonce did not verify.';
			die();
		} else {
			// validate data
			$username = $_POST['username'];
			$return = $_POST['_wp_http_referer'];
			if (!$username) {
				$result['type'] = 'error';
				$result['message'] = __('Field empty.', 'cell-user');
				ajax_response($result,$return);
				die();
			} else {
				if (username_exists($username)) {
					$user = get_user_by('login', $username);
				} elseif(email_exists($username)) {
					$user = get_user_by('email', $username);
				} else {
					$result['type'] = 'error';
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

	public function process_frontend_reset_password() {
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
				$result['type'] = 'error';
				$result['message'] = __('User not found.', 'cell-user');
				ajax_response($result,$return);
			} elseif(!$reset_key) {
				$result['type'] = 'error';
				$result['message'] = __('Activation key not found.', 'cell-user');
				ajax_response($result,$return);
			} else {
				if ($password1 &&($password1 != $password2)) {
					$result['type'] = 'error';
					$result['message'] = __('Input password is incorrect.', 'cell-user');
					ajax_response($result,$return);
				} else {
					wp_set_password($password1, $user_data->ID);

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