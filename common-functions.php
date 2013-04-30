<?php
/* ajax : custom function to detect wheter a request is made by ajax or not
---------------------------------------------------------------
*/

// wp_die( 'loaded' );

function ajax_request(){
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		$result = json_encode($result);
		return true;
	}
	else {
		return false;
	}
}

/* ajax : custom function to create an ajax response or http location redirect
---------------------------------------------------------------
*/


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
}

/* ajax : handle multiple file upload array
---------------------------------------------------------------
*/
function rearrange( $arr ){
	foreach( $arr as $key => $all ){
		foreach( $all as $i => $val ){
			$new[$i][$key] = $val;    
		}    
	}
	return $new;
}

/* ajax : insert uploaded file as attachment
---------------------------------------------------------------
*/

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

/* wp-admin : Adding Custom Post Type and Custom Taxonomy to Right Now Admin Widget
--------------------------------------------------------------
*/
add_action( 'right_now_content_table_end' , 'ucc_right_now_content_table_end' );

function ucc_right_now_content_table_end() {
	$args = array(
		'public' => true ,
		'_builtin' => false
	);
	$output = 'object';
	$operator = 'and';
	
	$post_types = get_post_types( $args , $output , $operator );
	
	foreach( $post_types as $post_type ) {
		$num_posts = wp_count_posts( $post_type->name );
		$num = number_format_i18n( $num_posts->publish );
		$text = _n( $post_type->labels->singular_name, $post_type->labels->name , intval( $num_posts->publish ) );
		if ( current_user_can( 'edit_posts' ) ) {
			$num = "<a href='edit.php?post_type=$post_type->name'>$num</a>";
			$text = "<a href='edit.php?post_type=$post_type->name'>$text</a>";
		}
		echo '<tr><td class="first b b-' . $post_type->name . '">' . $num . '</td>';
		echo '<td class="t ' . $post_type->name . '">' . $text . '</td></tr>';
	}
	
	$taxonomies = get_taxonomies( $args , $output , $operator );
	
	foreach( $taxonomies as $taxonomy ) {
		$num_terms  = wp_count_terms( $taxonomy->name );
		$num = number_format_i18n( $num_terms );
		$text = _n( $taxonomy->labels->singular_name, $taxonomy->labels->name , intval( $num_terms ) );
		if ( current_user_can( 'manage_categories' ) ) {
			$num = "<a href='edit-tags.php?taxonomy=$taxonomy->name'>$num</a>";
			$text = "<a href='edit-tags.php?taxonomy=$taxonomy->name'>$text</a>";
		}
		echo '<tr><td class="first b b-' . $taxonomy->name . '">' . $num . '</td>';
		echo '<td class="t ' . $taxonomy->name . '">' . $text . '</td></tr>';
	}
}


/* comment : add ajax comment
---------------------------------------------------------------
*/
add_action('wp_ajax_add_comment', 'process_add_comment');

function process_add_comment() {
	global $current_user;
	if ( empty($_POST) || !wp_verify_nonce($_POST[$current_user->user_login],'add_comment') ) {
		echo 'You targeted the right function, but sorry, your nonce did not verify. 1';
		die();
	} else {

		$return = $_POST['_wp_http_referer'];
		$post_ID = $_POST['post_id'];
		$comment_author = $current_user->display_name;
		$comment_email = $current_user->user_email;
		$comment_author_id = $current_user->ID;
		$comment_author_url = get_author_posts_url( $current_user->ID );
		$comment_author_IP = $_SERVER['REMOTE_ADDR'];
		$comment_agent = $_SERVER['HTTP_USER_AGENT'];
		$comment_content = $_POST['comment-content'];

		$comment_data = array(
			'comment_post_ID' => $post_ID,
			'comment_author_url' => $comment_author_url,
			'comment_author' => $comment_author,
			'comment_author_email' => $comment_email,
			'comment_author_url' => $comment_author_url,
			'comment_content' => $comment_content,
			'comment_author_IP' => $comment_author_IP,
			'user_id' => $comment_author_id,
			'comment_agent' => $comment_agent,
			'comment_date' => date('Y-m-d H:i:s'),
			'comment_date_gmt' => date('Y-m-d H:i:s'),
			'comment_approved' => 1,
		);

		$comment_id = wp_insert_comment($comment_data);
		
		$result['type'] = 'success';
		$result['message'] = 'Komentar sudah ditambahkan.';
		ajax_response($result,$return);
	}
}

/* comment : edit comment
---------------------------------------------------------------
*/

add_action('wp_ajax_edit_comment', 'process_edit_comment');

function process_edit_comment() {
	global $current_user;
	if ( empty($_POST) || !wp_verify_nonce($_POST[$current_user->user_login],'edit_comment') ) {
		echo 'You targeted the right function, but sorry, your nonce did not verify. 1';
		die();
	} else {
		$return = $_POST['_wp_http_referer'];
		$comment_ID = $_POST['comment_id'];
		$comment_content = $_POST['comment'];
		$parent_ID = $_POST['parent_id'];
		$return = get_permalink($parent_ID).'#comment-'.$comment_ID;
		$comment_data = array(
			'comment_ID' => $comment_ID,
			'comment_content' => $comment_content
		);
		$comment_id = wp_update_comment($comment_data);

		$result['type'] = 'success';
		$result['message'] = 'Komentar sudah diedit.';
		ajax_response($result,$return);
	}
}

/* print global message  (errors, validation, redirects dll)
---------------------------------------------------------------
*/

function print_global_message(){
	if ($_SESSION['global_message']) {
		$format = '<div class="alert %s"=> %s</div>';
		echo sprintf($format, $_SESSION['global_message']['type'], $_SESSION['global_message']['message']);
	} else {
		return false;
	}
}

/* get post object from lot-term 
---------------------------------------------------------------
*/

function get_post_object($term_id,$taxonomy) {

	$key = 'lot-term-id-'.$term_id;
	$group = 'lot-object';

	$object = wp_cache_get( $key, $group);

	if (!$object) {

		global $wpdb;
		$key = $taxonomy.'-id';

		$object_id = $wpdb->get_var("SELECT post_id FROM wp_postmeta WHERE meta_key = '$key' AND meta_value = '$term_id' ");
		$object = get_post( $object_id );

		wp_cache_set( $key, $object, $group);

	}

	return $object;
	
}

/* save the file name as attachment title 
---------------------------------------------------------------
*/
add_action('add_attachment', 'set_title');

function set_title($attachment_id){
	$attachment_post = get_post($attachment_id);
	if ($attachment_post->guid) {
		$attachment_explode = explode('/', $attachment_post->guid);
		$attachment_name = str_replace('-', ' ', end($attachment_explode));
		$attachment_name = str_replace('.jpg', '', $attachment_name);

		$post_args = array(
			'ID' => $attachment_id,
			'post_title' => $attachment_name,
		);

		wp_update_post($post_args);
	}
}

/* update post meta, if new
---------------------------------------------------------------
*/
function update_post_meta_if_new($post_id, $meta_key, $new_value, $old_value, $search_id = FALSE, $validation = FALSE){
	if ($search_id !== FALSE ) {
		if (strpos($new_value, '(ID:')) {
			$startsAt = strpos($new_value, "(ID:") + strlen("(ID:");
			$endsAt = strpos($new_value, ")", $startsAt);
			$result = substr($new_value, $startsAt, $endsAt - $startsAt);
			$new_value = $result;
		} else {
			$new_value = '';
		}
	}

	if (isset($old_value) && $old_value != '') {
		// ada nilai di database
		if ($new_value && $new_value != '') {
			// ada nilai yang dipost
			if ($old_value == $new_value ) {
				// nilainya sama

				call_user_func($callback);

				return TRUE;
			} else {
				// nilainya beda - save nilai baru
				$change = update_post_meta($post_id, $meta_key, $new_value);
				if ($change) {
					return TRUE;
				}
			}
		} else {
			return FALSE;
		}
	} else {
		// tidak ada nilai di database
		if ($new_value && $new_value != '') {
			// ada nilai yang dipost
			$change = update_post_meta($post_id, $meta_key, $new_value);
			if ($change) {
				return TRUE;
			}
		} else {
			// tidak ada nilai yang dipost - epic fail
			return FALSE;
		}
	}

}

/* update post meta from file, if new
---------------------------------------------------------------
*/

function update_postmeta_from_file($post_id, $meta_key, $file, $old_value = FALSE, $post_thumbnail = FALSE){
	if ( isset($file) && ($file['size'][0] > 0)) {
		// ada file
		$attached_file = attach_uploads($file,$post_id);
		if($attached_file){
			if ($post_thumbnail) {
				$change = set_post_thumbnail( $post_id, $attached_file[0] );
				return TRUE;
			} else {
				$change = update_post_meta($post_id, $meta_key, $attached_file[0]);
				return TRUE;
			}
		}
	} elseif ($old_value) {
		// tidak ada file pengganti & required
		return TRUE;
	} else {
		// tidak ada aksi apa apa
		return FALSE;
	}
}

/* set object term if new 
---------------------------------------------------------------
*/

function set_object_term_if_new($post_id, $taxonomy, $new_value, $old_value, $add = FALSE){
	if (isset($old_value) && $old_value != '') {
		// ada nilai di database
		if ($new_value && $new_value != '') {
			// ada nilai yang dipost
			if ($old_value == $new_value ) {
				// nilainya sama
				return TRUE;
			} else {
				// nilainya beda - ambil term objectnya
				$new_term = get_term_by( 'name', $new_value, $taxonomy);
				if ($new_term) {
					// nilainya tervalidasi - ambil id nya
					$new_term_id = intval($new_term->term_id);
				} elseif (!$new_term && $add == TRUE) {
					// nilai baru - ambil id nya
					$new_term = wp_insert_term( $new_value, $taxonomy);
					$new_term_id = intval($new_term['term_id']);
				} else {
					// nilai tidak valid - fail
					return FALSE;
				}
				$new_relation = wp_set_object_terms( $post_id, $new_term_id, $taxonomy);
				if (!is_wp_error($new_relation)) {
					return TRUE;
				} else {
					return FALSE;
				}
			}
		}
	} else {
		// tidak ada nilai di database
		if ($new_value && $new_value != '') {
			// ada nilai yang dipost			
			$new_term = get_term_by( 'name', $new_value, $taxonomy);
			if ($new_term) {
				// nilainya tervalidasi - ambil id nya
				$new_term_id = intval($new_term->term_id);

			} elseif (!$new_term && $add == TRUE) {
				// nilai baru - ambil id nya
				$new_term = wp_insert_term( $new_value, $taxonomy);
				$new_term_id = intval($new_term['term_id']);

			} else {
				// nilai tidak valid - fail
				return FALSE;
			}

			$new_relation = wp_set_object_terms( $post_id, $new_term_id, $taxonomy);
				if (!is_wp_error($new_relation)) {
					return TRUE;
				} else {
					return FALSE;
				}
		} else {
			// tidak ada nilai yang dipost - epic fail
			return FALSE;
		}
	}

}


/* validasi callback 
---------------------------------------------------------------
*/

function larger_than_10($value){
	# code...
}

?>