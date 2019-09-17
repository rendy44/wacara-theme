<?php
/**
 * Class to handle all the payment related to stripe.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Skeleton\Payment' ) ) {
	/**
	 * Class Payment
	 *
	 * @package Skeleton
	 */
	class Payment {
		/**
		 * Options variable.
		 *
		 * @var array
		 */
		private static $options = [];

		/**
		 * Is testing sandbox variable.
		 *
		 * @var bool
		 */
		private static $is_test = false;

		/**
		 * Secret key variable.
		 *
		 * @var string
		 */
		private static $secret_key = '';

		/**
		 * Publishable key.
		 *
		 * @var string
		 */
		private static $publishable_key = '';

		/**
		 * Instance variable.
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Singleton
		 *
		 * @return Payment|null
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Payment constructor.
		 */
		private function __construct() {
			self::$options = get_option( TEMP_PREFIX . 'stripe_options' );

			self::set_testing_status();
			self::set_publishable_key();
			self::set_secret_key();
		}

		/**
		 * Get value from options.
		 *
		 * @param string $key option key.
		 *
		 * @return bool|mixed
		 */
		private static function get_option_val( $key ) {
			$result = false;
			if ( ! empty( self::$options[ $key ] ) ) {
				$result = self::$options[ $key ];
			}

			return $result;
		}

		/**
		 * Set testing status.
		 */
		private static function set_testing_status() {
			$result = self::get_option_val( 'sandbox' );
			if ( 'on' === $result ) {
				self::$is_test = true;
			}
		}

		/**
		 * Whether testing stripe or live.
		 *
		 * @return bool
		 */
		public static function is_testing() {
			return self::$is_test;
		}

		/**
		 * Set publishable key.
		 */
		private static function set_publishable_key() {
			$temp_key              = self::is_testing() ? 'sandbox_publishable_key' : 'live_publishable_key';
			$result                = self::get_option_val( $temp_key );
			self::$publishable_key = $result;
		}

		/**
		 * Get publishable key.
		 *
		 * @return string
		 */
		public static function get_publishable_key() {
			return self::$publishable_key;
		}

		/**
		 * Set secret key.
		 */
		private static function set_secret_key() {
			$temp_key         = self::is_testing() ? 'sandbox_secret_key' : 'live_secret_key';
			$result           = self::get_option_val( $temp_key );
			self::$secret_key = $result;
		}

		/**
		 * Get secret key.
		 *
		 * @return string
		 */
		public static function get_secret_key() {
			return self::$secret_key;
		}
	}
}

Payment::init();
