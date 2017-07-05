cell-user
=========

Note : this plugin will not do anything on a fresh install. You have to initialize it either from another plugin or from current active theme's `functions.php`.

This was made for other developer to use as a part of their WordPress setup, not end user.

## Initiate the plugin
    add_action( 'init','iniate_cell_login' );
    function iniate_cell_login(){

## Front End Login
Use the short code `[cell-user-login]` anywhere to show a basic login form.

    	$login_args = array(
    		'page' => $login_page,
    		'page-redirect' => $profile_page,
    	);
    
    	if (class_exists('CellLogin')) {
    		$login_form = new CellLogin( $login_args);
    	}

## Front End Registration
Use the short code `[cell-user-register]` on any page, post or custom post type to show your registration form. You can set the form fields in the configuration array.

    $register_form_args = array(
    		'page' => $register_page,
    		'page-redirect' => $profile_page,
    		'fields' =>  array(
    			'username' => array( // the key will be used in the label for attribute and the input name
    				'title' => __('User Name', 'cell-user'), // the label text
    				'type' => 'text', // the input type or textarea
    				'required_text' => __('(required)', 'cell-user'),
    				'note' =>__('Use 3 - 15 character lowercase, numbers and \'- \' only', 'cell-user') // does it need a helper note, use inline html tags only
    			),
    			'email' => array(
    				'title' => __('Email', 'cell-user'),
    				'type' => 'text',
    				'note' => ''
    			),
    			'password' => array(
    				'title' => __('Password', 'cell-user'),
    				'type' => 'password',
    				'note' => ''
    			)
    		)
    	);
    
    	if (class_exists('CellRegister')) {
    		$login_form = new CellRegister($register_form_args);
    	}

## Front End Profile
Use the short code `[cell-user-profile]` on any page, post or custom post type to show your user profile form. You can set the form fields in the configuration array. Note that this will also appear on your wp-admin profile page.

    	$billing_fields = array(
    		'first_name' => array(
    			'title' => __('First Name', 'cell-user'),
    			'type' => 'text',
    		),
    		'last_name' => array(
    			'title' => __('Last Name', 'cell-user'),
    			'type' => 'text',
    		),
    		'telephone' => array(
    			'title' => __('Telephone', 'cell-user'),
    			'type' => 'text',
    		),
    		'company' => array(
    			'title' => __('Company', 'cell-user'),
    			'type' => 'text',
    		),
    		'address' => array(
    			'title' => __('Address', 'cell-user'),
    			'type' => 'textarea',
    		),
    		'country' => array(
    			'title' => __('Country', 'cell-user'),
    			'type' => 'select',
    			'option' => array('Indonesia', 'Singapore'),
    		),
    		'postcode' => array(
    			'title' => __('Post Code', 'cell-user'),
    			'type' => 'text',
    		),
    		'have-shipping' => array(
    			'title' => __('Have Shipping', 'cell-user'),
    			'type' => 'checkbox',
    		),
    	);
    
    	$shipping_fields = array(
    		'shipping-first-name' => array(
    			'title' => __('Shipping First Name', 'cell-user'),
    			'type' => 'text',
    		),
    		'shipping-last-name' => array(
    			'title' => __('Shipping Last Name', 'cell-user'),
    			'type' => 'text',
    		),
    		'shipping-email' => array(
    			'title' => __('Shipping Email', 'cell-user'),
    			'type' => 'text',
    		),
    		'shipping-telephone' => array(
    			'title' => __('Shipping Telephone', 'cell-user'),
    			'type' => 'text',
    		),
    		'shipping-company' => array(
    			'title' => __('Shipping Company', 'cell-user'),
    			'type' => 'text',
    		),
    		'shipping-address' => array(
    			'title' => __('Shipping Address', 'cell-user'),
    			'type' => 'textarea',
    		),
    		'shipping-country' => array(
    			'title' => __('Shipping Country', 'cell-user'),
    			'type' => 'select',
    			'option' => array('Indonesia', 'Singapore'),
    		),
    		'shipping-postcode' => array(
    			'title' => __('Shipping Post Code', 'cell-user'),
    			'type' => 'text',
    		),
    	);
    	$user_profile_args = array(
    		'page' => $profile_page,
    		'page-redirect' => $login_page,
    		'include-script' => 'address',
    		'fieldset' => array(
    			'billing' => array(
    				'title' => __('Billing', 'cell-user'),
    				'class' => 'billing fieldset',
    				'fields' => $billing_fields,
    				'public' => true,
    			),
    			'shipping' => array(
    				'title' => __('Shipping', 'cell-user'),
    				'class' => 'shipping fieldset',
    				'fields' => $shipping_fields,
    				'public' => true,
    			),
    		),
    	);
    
    	if (class_exists('CellProfile')) {
    		$profile_form = new CellProfile($user_profile_args);
    	}

