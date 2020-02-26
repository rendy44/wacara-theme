<?php
/**
 * Plugin Name: Wacara
 * Description: Ultimate Conference Ticket Organizer for WordPress
 * Author: WPerfekt
 * Author URI: http://wperfekt.com
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
defined( 'WACARA_URI' ) || define( 'WACARA_URI', plugin_dir_url( __FILE__ ) );
defined( 'WACARA_PATH' ) || define( 'WACARA_PATH', plugin_dir_path( __FILE__ ) );
defined( 'WACARA_MAYBE_THEME_URI' ) || define( 'WACARA_MAYBE_THEME_URI', get_stylesheet_directory_uri() );
defined( 'WACARA_MAYBE_THEME_PATH' ) || define( 'WACARA_MAYBE_THEME_PATH', get_stylesheet_directory() );
defined( 'WACARA_PREFIX' ) || define( 'WACARA_PREFIX', 'wcr_' );
defined( 'WACARA_VERSION' ) || define( 'WACARA_VERSION', '0.0.1' );

// Require our main class.
if ( ! class_exists( 'Wacara\Wacara' ) ) {
	require_once WACARA_PATH . '/includes/class/class-wacara.php';
}
