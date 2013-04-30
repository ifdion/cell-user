<?php

/**
 * Register
 *
 * @package default
 * @author Dion
 **/

class CellRegister {

	function __construct($args) {
		// get the registratiob page
		$register_page = $args['page'];

		// include style and scrip
		add_action( 'template_redirect', array( $this, 'registration_script'));

		// add_action( $tag, $function_to_add, $priority = 10, $accepted_args = 1 );

		// add a shortcode
		add_shortcode('cell-user-register', array( $this, 'shortcode_output'));

		// add login ajax handler function
		add_action('wp_ajax_nopriv_frontend_login', array( $this, 'process_login'));

		// add forgot password ajax handler function
		add_action('wp_ajax_nopriv_frontend_forgot_password', array($this, 'process_forgot_password'));

		// add reset password ajax handler function
		add_action('wp_ajax_nopriv_frontend_reset_password', array($this, 'process_frontend_reset_password'));
	}
	
	public function registration_script($register_page){
		if (is_page($register_page)){
			wp_enqueue_script('register-script', plugins_url().'/cell-user/js/register.js', array('jquery'), '0.1', true);
			wp_enqueue_style( 'cell-user-styles', plugins_url( 'cell-user/css/cell-user.css' ) );
			wp_localize_script( 'ajaxurl', 'global', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		}	
	}

	public function shortcode_output(){
		if(!is_user_logged_in()){
			ob_start();
			include('views/custom-registration-form.php');
			$register_form = ob_get_contents();
			ob_end_clean();

			echo $register_form;		
		} else {
			return false;
		}
	}

}