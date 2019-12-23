<?php
/**
 * Class to manage Stripe payment.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton\Payment;

use Skeleton\Participant;
use Skeleton\Payment_Method;
use Skeleton\Register_Payment;
use Skeleton\Result;
use Skeleton\Helper;
use Skeleton\Stripe_Wrapper;
use Skeleton\Transaction;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Skeleton\Payment\Stripe_Payment' ) ) {

	/**
	 * Class Stripe_Payment
	 *
	 * @package Skeleton\Payment
	 */
	class Stripe_Payment extends Payment_Method {

		/**
		 * Instance variable.
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Singleton.
		 *
		 * @return Stripe_Payment|null
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Stripe_Payment constructor.
		 */
		private function __construct() {
			$this->id          = 'stripe-payment';
			$this->name        = __( 'Stripe Payment', 'wacara' );
			$this->description = __( 'Stripe payment method for Wacara', 'wacara' );
			$this->automatic   = false;
			$this->enable      = true;

			// Do register the method.
			Register_Payment::register( $this );
		}

		/**
		 * Render the payment in front-end.
		 */
		public function render() {
			echo "<div id=\"card\" class=\"form-control form-control-lg\"></div>"; // phpcs:ignore
		}

		/**
		 * Function to calculate and process the payment.
		 *
		 * @param Participant $participant the participant object of registered participant.
		 * @param array       $fields used fields which is stored from front-end, mostly it contains unserialized object.
		 * @param int         $pricing_price amount of invoice in cent.
		 * @param string      $pricing_currency the currency code of invoice.
		 *
		 * @return Result
		 */
		public function process( $participant, $fields, $pricing_price, $pricing_currency ) {
			$result                 = new Result();
			$maybe_stripe_source_id = Helper::get_serialized_val( $fields, 'stripe_source_id' );
			$email                  = Helper::get_serialized_val( $fields, 'email' );
			$name                   = Helper::get_serialized_val( $fields, 'name' );

			// Look for saved customer.
			$find_stripe_customer = Transaction::find_stripe_customer_id_by_email( $email );

			// Prepare the variable that will be used to store stripe customer id.
			$used_stripe_customer_id = '';

			// Instance the stripe.
			$stripe_wrapper = new Stripe_Wrapper( $this->get_secret_key() );

			// Validate find stripe customer status.
			if ( $find_stripe_customer->success ) {

				// Update customer source information, just in case they use different cc information.
				$update_customer = $stripe_wrapper->update_customer_source( $find_stripe_customer->callback, $maybe_stripe_source_id );

				// Validate update customer status.
				if ( $update_customer->success ) {

					// Use stripe customer id.
					$used_stripe_customer_id = $update_customer->callback;
				} else {

					// Update the result.
					$result->message = $update_customer->message;
				}
			} else {

				// Save a new customer.
				$new_customer = Transaction::save_customer( $name, $email, $maybe_stripe_source_id );

				// Validate save new customer status.
				if ( $new_customer->success ) {

					// Use stripe customer id of new customer.
					$used_stripe_customer_id = $new_customer->callback;
				} else {

					// Update the result.
					$result->message = $new_customer->message;
				}
			}

			// Check whether stripe customer id that will be used has been defined or not yet.
			if ( $used_stripe_customer_id ) {

				// Charge the customer.
				/* translators: 1: the event name */
				$charge_name = sprintf( __( 'Payment for registering to %s', 'wacara' ), get_the_title( $participant->get_event_info() ) );
				$charge      = $stripe_wrapper->charge_customer( $used_stripe_customer_id, $maybe_stripe_source_id, $pricing_price, $pricing_currency, $charge_name );

				// Validate charge status.
				if ( $charge->success ) {

					// Update result.
					$result->success  = true;
					$result->callback = 'done';

				} else {

					// Update result.
					$result->callback = 'fail';
					$result->message  = $charge->message;
				}

				/**
				 * Perform actions after making payment by stripe.
				 *
				 * @param string $participant_id participant id.
				 * @param int $pricing_price the price of pricing in cent.
				 * @param string $pricing_currency the currency of pricing.
				 * @param Result $charge the object of payment.
				 */
				do_action( 'wacara_after_making_stripe_payment', $participant->post_id, $pricing_price, $pricing_currency, $charge );
			}

			return $result;
		}

		/**
		 * Define fields for admin settings.
		 *
		 * @return array
		 */
		public function admin_setting() {
			return [
				[
					'name' => __( 'Sandbox', 'wacara' ),
					'desc' => __( 'Enable sandbox for testing', 'wacara' ),
					'id'   => 'sandbox',
					'type' => 'checkbox',
				],
				[
					'name' => __( 'Sandbox secret key', 'wacara' ),
					'id'   => 'sandbox_secret_key',
					'type' => 'text',
					'desc' => __( 'Normally it something like this sk_test_xxx', 'wacara' ),
				],
				[
					'name' => __( 'Sandbox publishable key', 'wacara' ),
					'id'   => 'sandbox_publishable_key',
					'type' => 'text',
					'desc' => __( 'Normally it something like this pk_test_xxx', 'wacara' ),
				],
				[
					'name' => __( 'Live secret key', 'wacara' ),
					'id'   => 'live_secret_key',
					'type' => 'text',
					'desc' => __( 'Normally it something like this sk_live_xxx', 'wacara' ),
				],
				[
					'name' => __( 'Live publishable key', 'wacara' ),
					'id'   => 'live_publishable_key',
					'type' => 'text',
					'desc' => __( 'Normally it something like this pk_live_xxx', 'wacara' ),
				],
			];
		}

		/**
		 * Get content that will be rendered after making manual payment.
		 *
		 * @param Participant $participant the participant object of registered participant.
		 * @param string      $reg_status current registration status of the participant.
		 * @param string      $pricing_id the id of selected pricing.
		 * @param int         $pricing_price amount of invoice in cent.
		 * @param string      $pricing_currency the currency code of invoice.
		 *
		 * @return string
		 */
		public function maybe_page_after_payment( $participant, $reg_status, $pricing_id, $pricing_price, $pricing_currency ) {
			return ''; // Well, we don't need to display anything since this payment method is automatic.
		}

		/**
		 * Check whether Stripe is run as sandbox or live.
		 *
		 * @return bool
		 */
		private static function is_sandbox() {
			return 'on' === ( new static() )->get_admin_setting( 'sandbox' ) ? true : false;
		}

		/**
		 * Get stripe secret key whether it is sandbox or live.
		 *
		 * @return bool|mixed|void
		 */
		private function get_secret_key() {
			$field_secret_key = self::is_sandbox() ? 'sandbox_secret_key' : 'live_secret_key';

			return ( new static() )->get_admin_setting( $field_secret_key );
		}

		/**
		 * Get stripe publishable key whether it is sandbox or live.
		 *
		 * @return bool|mixed|void
		 */
		public static function get_publishable_key() {
			$field_publishable_key = self::is_sandbox() ? 'sandbox_publishable_key' : 'live_publishable_key';

			return ( new static() )->get_admin_setting( $field_publishable_key );
		}
	}

	// Instance the class.
	Stripe_Payment::init();
}
