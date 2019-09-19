<?php
/**
 * Use this class to add custome ajax handler
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

use Stripe\Exception\ApiErrorException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Skeleton\Ajax' ) ) {
	/**
	 * Class Ajax
	 *
	 * @package Skeleton
	 */
	class Ajax {
		/**
		 * Instance variable
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Singleton function
		 *
		 * @return Ajax|null
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Ajax constructor.
		 */
		private function __construct() {
			// Register ajax endpoint for registering participant.
			add_action( 'wp_ajax_nopriv_register', [ $this, 'register_callback' ] );
			add_action( 'wp_ajax_register', [ $this, 'register_callback' ] );
			// Register ajax endpoint for making payment.
			add_action( 'wp_ajax_nopriv_payment', [ $this, 'payment_callback' ] );
			add_action( 'wp_ajax_payment', [ $this, 'payment_callback' ] );
		}

		/**
		 * Callback for registering participant.
		 */
		public function register_callback() {
			$result     = new Result();
			$event_id   = Helper::post( 'event_id' );
			$pricing_id = Helper::post( 'pricing_id' );
			if ( $event_id && $pricing_id ) {
				// Validate the event before using it for registration.
				$validate_event = Helper::is_event_valid( $event_id );
				if ( $validate_event->success ) {
					// create participant.
					$new_participant = new Participant(
						false,
						[
							'event_id'   => $event_id,
							'pricing_id' => $pricing_id,
						]
					);
					if ( $new_participant->success ) {
						$result->success  = true;
						$result->callback = $new_participant->participant_url;
					} else {
						$result->message = $new_participant->message;
					}
				} else {
					$result->message = $validate_event->message;
				}
			} else {
				$result->message = __( 'Please provide a valid event id', 'wacara' );
			}
			wp_send_json( $result );
		}

		/**
		 * Callback for making payment.
		 *
		 * @throws ApiErrorException Handle error from stripe.
		 */
		public function payment_callback() {
			$result          = new Result();
			$data            = Helper::post( 'data' );
			$unserialize_obj = maybe_unserialize( $data );
			$registration_id = Helper::get_serialized_val( $unserialize_obj, 'registration_id' );
			$nonce           = Helper::get_serialized_val( $unserialize_obj, 'sk_payment' );
			// Validate the nonce.
			if ( wp_verify_nonce( $nonce, 'sk_nonce' ) ) {
				$stripe_source_id = Helper::get_serialized_val( $unserialize_obj, 'stripe_source_id' );
				$email            = Helper::get_serialized_val( $unserialize_obj, 'email' );
				$name             = Helper::get_serialized_val( $unserialize_obj, 'name' );
				$company          = Helper::get_serialized_val( $unserialize_obj, 'company' );
				$position         = Helper::get_serialized_val( $unserialize_obj, 'position' );
				$phone            = Helper::get_serialized_val( $unserialize_obj, 'phone' );
				$id_number        = Helper::get_serialized_val( $unserialize_obj, 'id_number' );
				$event_id         = Helper::get_post_meta( 'event_id', $registration_id );
				$pricing_id       = Helper::get_post_meta( 'pricing_id', $registration_id );
				$pricing_currency = Helper::get_post_meta( 'currency', $pricing_id );
				$pricing_price    = Helper::get_post_meta( 'price', $pricing_id );
				// Save the details.
				Helper::save_post_meta(
					$registration_id,
					[
						'email'     => $email,
						'name'      => $name,
						'company'   => $company,
						'position'  => $position,
						'phone'     => $phone,
						'id_number' => $id_number,
					]
				);
				// Only process the payment if the price if greater than 0.
				if ( $pricing_price > 0 ) {
					// Look for saved customer.
					$find_stripe_customer = Transaction::find_stripe_customer_id_by_email( $email );
					// Prepare the variable that will be used to store stripe customer id.
					$used_stripe_customer_id = '';
					if ( $find_stripe_customer->success ) {
						// Update customer source information, just in case they use different cc information.
						$update_customer = Payment::update_customer_source( $find_stripe_customer->callback, $stripe_source_id );
						if ( $update_customer->success ) {
							$used_stripe_customer_id = $update_customer->callback;
						} else {
							$result->message = $update_customer->message;
						}
					} else {
						// Save a new customer.
						$new_customer = Transaction::save_customer( $name, $email, $stripe_source_id );
						if ( $new_customer->success ) {
							$used_stripe_customer_id = $new_customer->callback;
						} else {
							$result->message = $new_customer->message;
						}
					}
					// Check whether stripe customer id that will be used has been defined or not yet.
					if ( $used_stripe_customer_id ) {

						// First, convert the price into cent.
						$pricing_price = $pricing_price * 100;
						// Charge the customer.
						/* translators: 1: the event name */
						$charge_name = sprintf( __( 'Payment for registering to %s', 'wacara' ), get_the_title( $event_id ) );
						$result      = Payment::charge_customer( $used_stripe_customer_id, $stripe_source_id, $pricing_price, $pricing_currency, $charge_name );
					}
				} else {
					// There is nothing to do here, just finidh the process :).
					$result->success = true;
				}
			} else {
				$result->message = __( 'Please reload the page and try again', 'wacara' );
			}
			wp_send_json( $result );
		}
	}
}

Ajax::init();
