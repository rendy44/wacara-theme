<?php
/**
 * Plugin Name: Wacara - Stripe Payment
 * Description: An add-on for Wacara to enable Stripe payment method.
 * Author: WPerfekt
 * Author URI: http://wperfekt.com
 * Version: 0.0.1
 * Text Domain: wacara
 * Domain Path: /i18n
 *
 * @package Wacara\Payment
 */

// Define constanta.
defined( 'WCR_STP_URI' ) || define( 'WCR_STP_URI', plugin_dir_url( __FILE__ ) );
defined( 'WCR_STP_PATH' ) || define( 'WCR_STP_PATH', plugin_dir_path( __FILE__ ) );

// Maybe load the main files.
add_action( 'wacara_loaded', 'load_stp_main_file_callback' );

/**
 * Callback for loading main file.
 */
function load_stp_main_file_callback() {
	include WCR_STP_PATH . 'includes/class-stripe-payment.php';
}
