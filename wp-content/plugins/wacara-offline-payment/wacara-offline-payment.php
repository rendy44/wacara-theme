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
register_activation_hook( __FILE__, 'maybe_activate_offline_payment' );

/**
 * Callback for checking dependency plugin.
 */
function maybe_activate_offline_payment() {
	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		include_once ABSPATH . '/wp-admin/includes/plugin.php';
	}
	if ( current_user_can( 'activate_plugins' ) && ! class_exists( 'Wacara\Wacara' ) ) {
		// Deactivate the plugin.
		deactivate_plugins( plugin_basename( __FILE__ ) );
		// Throw an error in the WordPress admin console.
		/* translators: %s: plugin nme */
		$error_message = '<p>' . sprintf( __( 'This plugin requires <strong>%s</strong> plugin to be active', 'wacara' ), 'Wacara' ) . '</p>';
		die( $error_message ); // phpcs:ignore
	}
}
