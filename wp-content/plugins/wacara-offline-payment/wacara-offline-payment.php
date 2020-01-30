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

add_action( 'plugin_loaded', 'load_offline_payment' );

/**
 * Load main class file.
 */
function load_offline_payment() {

	// Only load the main class if wacara core is available.
	if ( class_exists( 'Wacara\Wacara' ) ) {
		require_once plugin_dir_path( __FILE__ ) . 'class-offline-payment.php';
	}
}
