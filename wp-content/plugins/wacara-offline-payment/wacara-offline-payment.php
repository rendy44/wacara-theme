<?php
/**
 * Plugin Name: Wacara - Offline Payment
 * Description: An add-on for Wacara to enable offline payment method.
 * Author: Rendy
 * Author URI: http://fb.com/rendy.444444
 * Version: 0.0.1
 * Text Domain: wacara
 * Domain Path: /i18n
 *
 * @package Wacara\Payment
 */

// Define constanta.
defined( 'WCR_OP_URI' ) || define( 'WCR_OP_URI', plugin_dir_url( __FILE__ ) );
defined( 'WCR_OP_PATH' ) || define( 'WCR_OP_PATH', plugin_dir_path( __FILE__ ) );

// Maybe load the main files.
add_action( 'plugin_loaded', 'load_offline_payment' );

/**
 * Callback for loading main class file.
 */
function load_offline_payment() {

	// Only load the main class if wacara core is available.
	if ( class_exists( 'Wacara\Wacara' ) ) {
		require_once WCR_OP_PATH . 'class-offline-payment.php';
	}
}
