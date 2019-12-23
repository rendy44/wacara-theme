<?php
/**
 * Class to handle all the Stripe_Wrapper related to stripe.
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

if ( ! class_exists( 'Skeleton\Stripe_Wrapper' ) ) {

	/**
	 * Class Stripe_Wrapper
	 *
	 * @package Skeleton
	 */
	class Stripe_Wrapper {

		/**
		 * Stripe_Wrapper constructor.
		 *
		 * @param string $secret_key stripe secret key.
		 */
		public function __construct( $secret_key ) {

			// Save secret key into app.
			self::set_stripe_secret_key( $secret_key );
		}

		/**
		 * Set secret key to Stripe Core class
		 *
		 * @param string $secret_key stripe secret key.
		 */
		private function set_stripe_secret_key( $secret_key ) {
			Stripe::setApiKey( $secret_key );
		}

		/**
		 * Create stripe customer.
		 *
		 * @param string $name customer name.
		 * @param string $email customer email.
		 * @param string $stripe_source_id customer source id which generated from inputting credit card information.
		 *
		 * @return Result
		 */
		public function create_customer( $name, $email, $stripe_source_id ) {
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
		 * @param string $stripe_source_id stripe source id.
		 *
		 * @return Result
		 */
		public function update_customer_source( $stripe_customer_id, $stripe_source_id ) {
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
		 * @param string $stripe_source_id stripe source id.
		 * @param int    $amount amount that will be charged.
		 * @param string $currency currency code.
		 * @param string $description name of the charge.
		 *
		 * @return Result
		 */
		public function charge_customer( $stripe_customer_id, $stripe_source_id, $amount, $currency, $description ) {
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
