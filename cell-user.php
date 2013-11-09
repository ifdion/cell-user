<?php
/*
Plugin Name: Cell User
Plugin URI: http://google.com
Description: Add front end registration, profile editing
Version: 0.0
Author: Dion
Author URI: http://google.com
Author Email: ifdion@gmail.com
License:

	Copyright 2013 (ifdion@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

//set constant values
define( 'CELL_USER_FILE', __FILE__ );
define( 'CELL_USER', dirname( __FILE__ ) );
define( 'CELL_USER_PATH', plugin_dir_path(__FILE__) );
define( 'CELL_USER_TEXT_DOMAIN', 'cell-user' );

// set for internationalization
function cell_user_init() {
	load_plugin_textdomain('cell-user', false, basename( dirname( __FILE__ ) ) . '/lang' );
}
add_action('plugins_loaded', 'cell_user_init');


/* session
---------------------------------------------------------------
*/

	if (!session_id()) {
		session_start();
	}

/* global 
---------------------------------------------------------------
*/

// include_once ('settings.php');

	include_once ('common-functions.php');

	include_once ('login/login.php');

	include_once ('register/register.php');

	include_once ('user-profile/user-profile.php');

