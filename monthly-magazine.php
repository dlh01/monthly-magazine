<?php
/*
Plugin Name: Monthly Magazine
Version: 1.0.0
Description: Create issues for a magazine using out-of-the-box integration with Posts 2 Posts and Developers Custom Fields.
Author: David Herrera
Author URI: http://github.com/dlh01
Plugin URI: http://github.com/dlh01/monthly-magazine
Text Domain: monthly-magazine
Domain Path: /languages
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . '/classes/class-mm.php' );
require_once( plugin_dir_path( __FILE__ ) . '/classes/class-mm-issue.php' );

add_action( 'plugins_loaded', array( 'MM', 'get_instance' ) );
