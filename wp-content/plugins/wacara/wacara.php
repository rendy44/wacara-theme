<?php
/**
 * Plugin Name: Wacara
 * Description: Ultimate Event Organizer for WordPress
 * Author: Rendy
 * Author URI: http://fb.com/rendy.444444
 * Version: 0.0.1
 * Text Domain: wacara
 * Domain Path: /i18n
 *
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define the constanta.
defined( 'TEMP_URI' ) || define( 'TEMP_URI', plugin_dir_url( __FILE__ ) );
defined( 'TEMP_PATH' ) || define( 'TEMP_PATH', plugin_dir_path( __FILE__ ) );
defined( 'TEMP_PREFIX' ) || define( 'TEMP_PREFIX', 'wcr_' );

// Require our main class.
require_once TEMP_PATH . '/includes/class/class-wacara.php';
