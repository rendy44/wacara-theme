<?php
/**
 * Class to handle all the payment related to stripe.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

use Stripe\Charge;
use Stripe\Customer;
use Stripe\Error\Base;
use Stripe\Stripe;

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
			self::get_stripe_options();

			self::set_testing_status();
			self::set_publishable_key();
			self::set_secret_key();

			self::set_stripe_secret_key();
		}

		/**
		 * Get stripe options
		 */
		private static function get_stripe_options() {
			self::$options = Options::get_stripe_options();
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

		/**
		 * Set secret key to Stripe Core class
		 */
		private static function set_stripe_secret_key() {
			Stripe::setApiKey( self::get_secret_key() );
		}

		/**
		 * Create stripe customer.
		 *
		 * @param string $name             customer name.
		 * @param string $email            customer email.
		 * @param string $stripe_source_id customer source id which generated from inputting credit card information.
		 *
		 * @return Result
		 */
		public static function create_customer( $name, $email, $stripe_source_id ) {
			$result = new Result();
			try {
				$create_customer  = Customer::create(
					[
						'name'   => $name,
						'email'  => $email,
						'source' => $stripe_source_id,
					]
				);
				$result->success  = true;
				$result->callback = $create_customer->id;
			} catch ( Base $e ) {
				$result->message = $e->getMessage();
			}

			return $result;
		}

		/**
		 * Update stripe customer source id
		 *
		 * @param string $stripe_customer_id stripe customer id.
		 * @param string $stripe_source_id   stripe source id.
		 *
		 * @return Result
		 */
		public static function update_customer_source( $stripe_customer_id, $stripe_source_id ) {
			$result = new Result();
			try {
				Customer::update( $stripe_customer_id, [ 'source' => $stripe_source_id ] );
				$result->success  = true;
				$result->callback = $stripe_customer_id;
			} catch ( Base $e ) {
				$result->message = $e->getMessage();
			}

			return $result;
		}

		/**
		 * Create a charge.
		 *
		 * @param string $stripe_customer_id stripe customer id.
		 * @param string $stripe_source_id   stripe source id.
		 * @param int    $amount             amount that will be charged.
		 * @param string $currency           currency code.
		 * @param string $description        name of the charge.
		 *
		 * @return Result
		 */
		public static function charge_customer( $stripe_customer_id, $stripe_source_id, $amount, $currency, $description ) {
			$result = new Result();
			try {
				$charge           = Charge::create(
					[
						'amount'      => $amount,
						'currency'    => strtolower( $currency ),
						'description' => $description,
						'source'      => $stripe_source_id,
						'customer'    => $stripe_customer_id,
					]
				);
				$result->success  = true;
				$result->callback = $charge->id;
			} catch ( Base $e ) {
				$result->message = $e->getMessage();
			}

			return $result;
		}
	}
}

Payment::init();
