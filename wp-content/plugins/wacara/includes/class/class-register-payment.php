<?php
/**
 * Class to register payment methods.
 *
 * @author Rendy
 * @package Wacara
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Register_Payment' ) ) {

	/**
	 * Class Register_Payment
	 *
	 * @package Wacara
	 */
	class Register_Payment {

		/**
		 * Lists of payment methods.
		 *
		 * @var array
		 */
		private static $payment_methods = [];

		/**
		 * Register payment methods.
		 *
		 * @param Payment_Method $class_object the payment method class that will be registered.
		 */
		public static function register( $class_object ) {
			self::$payment_methods[ $class_object->id ] = $class_object;
		}

		/**
		 * Get lists of registered payment methods.
		 *
		 * @param bool $enabled_only whether display only enabled payment methods.
		 *
		 * @return array
		 */
		public static function get_registered( $enabled_only = true ) {
			$result = [];

			// Check whether we need to filter payment methods by enable status or not.
			if ( $enabled_only ) {
				foreach ( self::$payment_methods as $payment_method ) {

					// Filter only enabled payment methods.
					if ( $payment_method->enable ) {
						$result[] = $payment_method;
					}
				}
			} else {

				// Since no filter is required, just display all payment methods.
				$result = self::$payment_methods;
			}

			return $result;
		}

		/**
		 * Get instance of payment method by its id.
		 *
		 * @param string $id the id of payment method.
		 *
		 * @return Payment_Method|mixed|bool
		 */
		public static function get_payment_method_class( $id ) {
			return ! empty( self::$payment_methods[ $id ] ) ? self::$payment_methods[ $id ] : false;
		}
	}
}
