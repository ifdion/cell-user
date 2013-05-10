<?php
/* ajax : custom function to detect wheter a request is made by ajax or not
---------------------------------------------------------------
*/

if (!function_exists('ajax_request')) {
	function ajax_request(){
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return true;
		} else {
			return false;
		}
	}
}

/* ajax : custom function to create an ajax response or http location redirect
---------------------------------------------------------------
*/

if (!function_exists('ajax_response')) {
	function ajax_response($data,$redirect = false){
		if(ajax_request()){
			$data_json = json_encode($data);
			echo $data_json;			
		} else {
			$_SESSION['global_message'][] = $data;
		}
		if ($redirect) {
			wp_redirect( $redirect );
			exit;
			die();
		}
	}	
}

/* global message 
---------------------------------------------------------------
*/

add_action( 'init', 'setup_global_message');

if (!function_exists('setup_global_message')) {
	function setup_global_message(){
		global $global_message;
		if ( isset( $_SESSION['global_message'] ) ){
			$global_message = $_SESSION['global_message'];
			unset( $_SESSION['global_message'] );
		}
	}
}

if (!function_exists('the_global_message')) {
	function the_global_message(){
		global $global_message;
		if ($global_message != '' && (count($global_message) > 0)) {
			foreach ($global_message as $message){
				?>
					<div id="" class="alert alert-<?php echo $message['type'] ?>">
						<a href="" class="delete">✕</a> <span><?php echo $message['message'] ?></span>
					</div>
				<?php
			}
		}
		$global_message = false;
	}	
}

/* ajax : handle multiple file upload array
---------------------------------------------------------------
*/
if (!function_exists('rearrange')) {
function rearrange( $arr ){
	foreach( $arr as $key => $all ){
		foreach( $all as $i => $val ){
			$new[$i][$key] = $val;
		}
	}
	return $new;
}	
}


/* ajax : insert uploaded file as attachment
---------------------------------------------------------------
*/
if (!function_exists('attach_uploads')) {
	function attach_uploads($uploads,$post_id = 0,$attachment_meta = 0){
		$files = rearrange($uploads);
		if($files[0]['name']==''){
			return false;	
		}
		foreach($files as $file){
			$upload_file = wp_handle_upload( $file, array('test_form' => false) );
			$attachment = array(
			'post_mime_type' => $upload_file['type'],
			'post_title' => preg_replace('/\.[^.]+$/', '', basename($upload_file['file'])),
			'post_content' => '',
			'post_status' => 'inherit'
			);
			$attach_id = wp_insert_attachment( $attachment, $upload_file['file'], $post_id );
			$attach_array[] = $attach_id;
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			$attach_data = wp_generate_attachment_metadata( $attach_id, $upload_file['file'] );
			wp_update_attachment_metadata( $attach_id, $attach_data );

			if (is_array($attachment_meta)) {
				foreach ($attachment_meta as $key => $value) {
					$post_meta = add_post_meta( $attach_id, $key, $value);
				}
			}
		}
		return $attach_array;
	}	
}

/* wp-admin : disable non administrator to access wp-admin
---------------------------------------------------------------
*/
function my_admin_init(){
	if( !defined('DOING_AJAX') && !current_user_can('administrator') ){
		wp_redirect( home_url() );
		exit();
	}
}
add_action('admin_init','my_admin_init');


/* wp-admin : add script in wp-admin 
---------------------------------------------------------------
*/
add_action('admin_print_scripts', 'add_script'); //dion
function add_script() { //dion
	wp_enqueue_script( 'suggest' );
	wp_enqueue_script('admin-cellscript', plugins_url() . '/twitgreen/js/twitgreen-admin-script.js', array('jquery'),'1.0',true);

}
