<?php

/* Options Page 
---------------------------------------------------------------
*/
add_action( 'admin_menu', 'cell_user_example_theme_menu' );
function cell_user_example_theme_menu() {

	add_users_page(
		__('Email', 'cell-user'), 			// The title to be displayed in the browser window for this page.
		__('Email', 'cell-user'),			// The text to be displayed for this menu item
		'administrator',					// Which type of users can see this menu item
		'cell_user_options',				// The unique ID - that is, the slug - for this menu item
		'cell_user_theme_display'			// The name of the function to call when rendering this menu's page
	);
}

/* Options Page, with tabs deactivated for now 
---------------------------------------------------------------
*/
function cell_email_theme_display( $active_tab = '' ) {
?>
	<div class="wrap">
	
		<div id="icon-themes" class="icon32"></div>
		<h2><?php _e('Email Options', 'cell-user') ?></h2>
		<?php settings_errors(); ?>
		
		<?php
			if( isset( $_GET[ 'tab' ] ) ) {
				$active_tab = $_GET[ 'tab' ];
			} else if( $active_tab == 'social_options' ) {
				$active_tab = 'social_options';
			} else if( $active_tab == 'input_examples' ) {
				$active_tab = 'input_examples';
			} else {
				$active_tab = 'base_options';
			}
		?>
		
		<!-- <h2 class="nav-tab-wrapper">
			<a href="?page=cell_email_options&tab=base_options" class="nav-tab <?php //echo $active_tab == 'base_options' ? 'nav-tab-active' : ''; ?>">Base Options</a>
			<a href="?page=cell_email_options&tab=social_options" class="nav-tab <?php //echo $active_tab == 'social_options' ? 'nav-tab-active' : ''; ?>">Social Options</a>
			<a href="?page=cell_email_options&tab=input_examples" class="nav-tab <?php //echo $active_tab == 'input_examples' ? 'nav-tab-active' : ''; ?>">Input Examples</a> 
		</h2> -->
		
		<form method="post" action="options.php">
			<?php
				if( $active_tab == 'base_options' ) {
					settings_fields( 'cell_email_base_options' );
					do_settings_sections( 'cell_email_base_options' );
				} elseif( $active_tab == 'social_options' ) {
					// settings_fields( 'cell_email_theme_social_options' );
					// do_settings_sections( 'cell_email_theme_social_options' );
				} else {
					// settings_fields( 'cell_email_theme_input_examples' );
					// do_settings_sections( 'cell_email_theme_input_examples' );
				}
				
				submit_button();
			?>
		</form>
	</div>
<?php
}


function cell_email_initialize_theme_options() {

	if( false == get_option( 'cell_email_base_options' ) ) {	
		add_option( 'cell_email_base_options' );
	}

	add_settings_section(
		'email_identity_section',
		__('Email Identity', 'cell-user'),
		'cell_email_identity_callback',
		'cell_email_base_options'
	);

	add_settings_field(	
		'from_name',
		__('From Name', 'cell-user'),
		'cell_email_from_name_callback',
		'cell_email_base_options',
		'email_identity_section',
		array(
			__('This will be used as the email sender name', 'cell-user')
		)
	);

	add_settings_field(	
		'from_email',
		__('From Email Address', 'cell-user'),
		'cell_email_from_email_callback',
		'cell_email_base_options',
		'email_identity_section',
		array(
			__('This will be used as the sender email', 'cell-user')
		)
	);

	add_settings_section(
		'email_design_section',
		__('Email Design','cell-user'),
		'cell_email_design_callback',
		'cell_email_base_options'
	);

	add_settings_field(
		'email_header',
		__( 'Email Header', 'cell-user' ),
		'email_header_callback',
		'cell_email_base_options',
		'email_design_section'
	);

	add_settings_field(
		'email_header_preview',
		__( 'Email Header Preview', 'cell-user' ),
		'email_header_preview_callback',
		'cell_email_base_options',
		'email_design_section'
	);

	add_settings_field(
		'email_notification_test',
		__( 'Email Preview', 'cell-user' ),
		'email_notification_test_callback',
		'cell_email_base_options',
		'email_design_section'
	);

	register_setting(
		'cell_email_base_options',
		'cell_email_base_options',
		'input_array_validation'
	);
	
}
add_action( 'admin_init', 'cell_email_initialize_theme_options' );