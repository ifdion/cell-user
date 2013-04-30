<?php

/* Options Page 
---------------------------------------------------------------
*/
add_action( 'admin_menu', 'cell_user_example_theme_menu' );
function cell_user_example_theme_menu() {

	add_users_page(
		__('Front End User', 'cell-user'), 			// The title to be displayed in the browser window for this page.
		__('Front End User', 'cell-user'),			// The text to be displayed for this menu item
		'administrator',					// Which type of users can see this menu item
		'cell_user_options',				// The unique ID - that is, the slug - for this menu item
		'cell_user_theme_display'			// The name of the function to call when rendering this menu's page
	);
}

/* Options Page, with tabs deactivated for now 
---------------------------------------------------------------
*/
function cell_user_theme_display( $active_tab = '' ) {
?>
	<div class="wrap">
	
		<div id="icon-themes" class="icon32"></div>
		<h2><?php _e('Front End User Settings', 'cell-user') ?></h2>
		<?php settings_errors(); ?>
		
		<?php
			if( isset( $_GET[ 'tab' ] ) ) {
				$active_tab = $_GET[ 'tab' ];
			} else if( $active_tab == 'input_fields' ) {
				$active_tab = 'input_fields';
			} else {
				$active_tab = 'user_pages';
			}
		?>
		
		<h2 class="nav-tab-wrapper">
			<a href="?page=cell_user_options&tab=user_pages" class="nav-tab <?php echo $active_tab == 'user_pages' ? 'nav-tab-active' : ''; ?>">User Pages</a> 
			<a href="?page=cell_user_options&tab=input_fields" class="nav-tab <?php echo $active_tab == 'input_fields' ? 'nav-tab-active' : ''; ?>">Input Fields</a>
		</h2>
		
		<form method="post" action="options.php">
			<?php
				if( $active_tab == 'user_pages' ) {
					settings_fields( 'cell_user_user_pages' );
					do_settings_sections( 'cell_user_user_pages' );
				} elseif( $active_tab == 'input_fields' ) {
					// settings_fields( 'input_cell_user_theme_fields' );
					// do_settings_sections( 'input_cell_user_theme_fields' );
				} else {
					// settings_fields( 'cell_user_theme_user_pages' );
					// do_settings_sections( 'cell_user_theme_user_pages' );
				}
				
				submit_button();
			?>
		</form>
	</div>
<?php
}


function cell_user_initialize_theme_options() {

	if( false == get_option( 'cell_user_user_pages' ) ) {	
		add_option( 'cell_user_user_pages' );
	}

	add_settings_section(
		'email_identity_section',
		__('Email Identity', 'cell-user'),
		'cell_user_identity_callback',
		'cell_user_user_pages'
	);

	add_settings_field(	
		'from_name',
		__('From Name', 'cell-user'),
		'cell_user_from_name_callback',
		'cell_user_user_pages',
		'email_identity_section',
		array(
			__('This will be used as the email sender name', 'cell-user')
		)
	);

	add_settings_field(	
		'from_email',
		__('From Email Address', 'cell-user'),
		'cell_user_from_email_callback',
		'cell_user_user_pages',
		'email_identity_section',
		array(
			__('This will be used as the sender email', 'cell-user')
		)
	);

	add_settings_section(
		'email_design_section',
		__('Email Design','cell-user'),
		'cell_user_design_callback',
		'cell_user_user_pages'
	);

	add_settings_field(
		'email_header',
		__( 'Email Header', 'cell-user' ),
		'email_header_callback',
		'cell_user_user_pages',
		'email_design_section'
	);

	add_settings_field(
		'email_header_preview',
		__( 'Email Header Preview', 'cell-user' ),
		'email_header_preview_callback',
		'cell_user_user_pages',
		'email_design_section'
	);

	add_settings_field(
		'email_notification_test',
		__( 'Email Preview', 'cell-user' ),
		'email_notification_test_callback',
		'cell_user_user_pages',
		'email_design_section'
	);

	register_setting(
		'cell_user_user_pages',
		'cell_user_user_pages',
		'input_array_validation'
	);
	
}
add_action( 'admin_init', 'cell_user_initialize_theme_options' );

/* Section Callbacks 
---------------------------------------------------------------
*/

function cell_user_identity_callback() {
	echo __('<p>Basic email corespondance identity.</p>', 'cell-email');
}

function cell_user_design_callback() {
	echo __('<p>Basic email design options.</p>', 'cell-email');
}

function cell_user_preview_callback() {
	echo __('<p>Send a test email. </p>', 'cell-email');
}

/* Field Callbacks 
---------------------------------------------------------------
*/

function cell_user_from_name_callback($args) {
	$options = get_option( 'cell_user_base_options' );
	$html =  '<input type="text" id="from_name" name="cell_user_base_options[from_name]" value="' . $options['from_name'] . '" />';
	$html .= '<label for="cell_user_base_options[from_name]">&nbsp;'  . $args[0] . '</label>'; 
	echo $html;	
}

function cell_user_from_email_callback($args) {
	$options = get_option( 'cell_user_base_options' );
	$html =  '<input type="text" id="from_email" name="cell_user_base_options[from_email]" value="' . $options['from_email'] . '" />';
	$html .= '<label for="cell_user_base_options[from_email]">&nbsp;'  . $args[0] . '</label>'; 
	echo $html;	
}

function email_header_callback() {  
	$options = get_option( 'cell_user_base_options' );  
	?>  
		<input type="text" id="logo_url" name="cell_user_base_options[email_header]" value="<?php echo esc_url( $options['email_header'] ); ?>" />  
		<input id="upload_logo_button" type="button" class="button" value="<?php _e( 'Upload Banner', 'cell-email' ); ?>" />  
		<span class="description"><?php _e('Upload an image for the banner.', 'cell-email' ); ?></span>  
	<?php  
} 

function email_header_preview_callback() {  
	$options = get_option( 'cell_user_base_options' );  ?>  
	<div id="upload_logo_preview" style="min-height: 100px;">  
		<img style="max-width:100%;" src="<?php echo esc_url( $options['email_header'] ); ?>" />  
	</div>  
	<?php  
}

function email_notification_test_callback() {  
	?>  
	<div id="email-test" class="hidden">
		<a href="#" id="send-notification-test" class="button button-secondary"><?php echo __('Notification Email', 'cell-email') ?></a>
		<a href="#" id="send-reset-password-test" class="button button-secondary"><?php echo __('Reset Password Email', 'cell-email') ?></a>
	</div>  
	<?php  
} 

/* Basic Validation 
---------------------------------------------------------------
*/

function input_array_validation( $input ) {
	$output = array();
	foreach( $input as $key => $value ) {
		if( isset( $input[$key] ) ) {
			$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );
		}
	}
	return apply_filters( 'input_array_validattion', $output, $input );
}