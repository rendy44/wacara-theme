<?php
/**
 * Use this class to add custome ajax handler
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

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

			// Register ajax endpoint for finding participant by booking coce before checking in..
			add_action( 'wp_ajax_nopriv_find_by_booking_code', [ $this, 'find_by_booking_code_callback' ] );
			add_action( 'wp_ajax_find_by_booking_code', [ $this, 'find_by_booking_code_callback' ] );

			// Register ajax endpoint for processing checkin.
			add_action( 'wp_ajax_nopriv_participant_checkin', [ $this, 'participant_checkin_callback' ] );
			add_action( 'wp_ajax_participant_checkin', [ $this, 'participant_checkin_callback' ] );
		}

		/**
		 * Callback for registering participant.
		 */
		public function register_callback() {
			$result     = new Result();
			$event_id   = Helper::post( 'event_id' );
			$pricing_id = Helper::post( 'pricing_id' );

			// Validate the inputs.
			if ( $event_id && $pricing_id ) {

				// Instance the event obj.
				$event = new Event( $event_id, true );

				// Validate the event before using it for registration.
				$event->validate_event();

				// Validate the event.
				if ( $event->success ) {

					// create participant.
					$new_participant = new Participant(
						false,
						[
							'event_id'   => $event_id,
							'pricing_id' => $pricing_id,
						]
					);

					// Validate the newly created event.
					if ( $new_participant->success ) {

						// Recount the event.
						$event->maybe_recount_limitation();

						// Update the result.
						$result->success  = true;
						$result->callback = $new_participant->get_participant_url();
					} else {

						// Update the result.
						$result->message = $new_participant->message;
					}
				} else {
					$result->message = $event->message;
				}
			} else {
				$result->message = __( 'Please provide a valid event id', 'wacara' );
			}

			wp_send_json( $result );
		}

		/**
		 * Callback for making payment.
		 */
		public function payment_callback() {
			$result          = new Result();
			$data            = Helper::post( 'data' );
			$unserialize_obj = maybe_unserialize( $data );
			$participant_id  = Helper::get_serialized_val( $unserialize_obj, 'participant_id' );
			$nonce           = Helper::get_serialized_val( $unserialize_obj, 'sk_payment' );

			// Validate the nonce.
			if ( wp_verify_nonce( $nonce, 'sk_nonce' ) ) {

				// Define variables.
				$maybe_payment_method   = Helper::get_serialized_val( $unserialize_obj, 'payment_method' );
				$maybe_stripe_source_id = Helper::get_serialized_val( $unserialize_obj, 'stripe_source_id' );
				$email                  = Helper::get_serialized_val( $unserialize_obj, 'email' );
				$name                   = Helper::get_serialized_val( $unserialize_obj, 'name' );
				$company                = Helper::get_serialized_val( $unserialize_obj, 'company' );
				$position               = Helper::get_serialized_val( $unserialize_obj, 'position' );
				$phone                  = Helper::get_serialized_val( $unserialize_obj, 'phone' );
				$id_number              = Helper::get_serialized_val( $unserialize_obj, 'id_number' );
				$event_id               = Helper::get_post_meta( 'event_id', $participant_id );
				$pricing_id             = Helper::get_post_meta( 'pricing_id', $participant_id );
				$pricing_currency       = Helper::get_post_meta( 'currency', $pricing_id );
				$pricing_price          = Helper::get_post_meta( 'price', $pricing_id );
				$pricing_unique_code    = Helper::get_post_meta( 'use_unique_code', $pricing_id );

				// First, convert the price into cent.
				$pricing_price = $pricing_price * 100;

				// Instance the participant.
				$participant = new Participant( $participant_id );

				// Save the details.
				$participant->save_more_details( $name, $email, $company, $position, $phone, $id_number );

				// Save invoice info.
				$participant->save_invoicing_info( $pricing_price, $pricing_currency );

				// Define some variables related to registration.
				$reg_status = '';

				/**
				 * Perform actions before finishing registration.
				 *
				 * @param string $participant_id       the id of participant.
				 * @param string $maybe_payment_method maybe selected payment method.
				 */
				do_action( 'wacara_before_finishing_registration', $participant_id, $maybe_payment_method );

				// Switch which payment will be used.
				switch ( $maybe_payment_method ) {

					case 'manual':
						// Check maybe requires unique code.
						if ( 'on' === $pricing_unique_code ) {

							// Set default unique number range to maximal 100 cent.
							$unique = wp_rand( 0, 100 );

							// Determine the amount of unique number.
							// If the pricing price is greater than 100000 it's probably weak currency such a Rupiah which does not use cent.
							// So we will multiple the unique number by 100.
							if ( 100000 < $pricing_price ) {
								$unique *= 100;
							}

							// Save the unique number.
							$participant->save_unique_number( $unique );
						}

						// There is nothing to do here, just finish the process and wait for the payment :).
						$result->success  = true;
						$result->callback = get_permalink( $participant_id );

						// Save registration status.
						$reg_status = 'wait_payment';
						break;

					case 'stripe':
						// Prepare variable to store stripe error message.
						$stripe_error_message = '';

						// Look for saved customer.
						$find_stripe_customer = Transaction::find_stripe_customer_id_by_email( $email );

						// Prepare the variable that will be used to store stripe customer id.
						$used_stripe_customer_id = '';

						// Validate find stripe customer status.
						if ( $find_stripe_customer->success ) {

							// Update customer source information, just in case they use different cc information.
							$update_customer = Payment::update_customer_source( $find_stripe_customer->callback, $maybe_stripe_source_id );

							// Validate update customer status.
							if ( $update_customer->success ) {

								// Use stripe customer id.
								$used_stripe_customer_id = $update_customer->callback;
							} else {

								// Save stripe error message.
								$stripe_error_message = $update_customer->message;

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
							$charge_name = sprintf( __( 'Payment for registering to %s', 'wacara' ), get_the_title( $event_id ) );
							$charge      = Payment::charge_customer( $used_stripe_customer_id, $maybe_stripe_source_id, $pricing_price, $pricing_currency, $charge_name );

							// Validate charge status.
							if ( $charge->success ) {

								// Update reg status.
								$reg_status = 'done';

								// Update result.
								$result->success  = true;
								$result->callback = get_permalink( $participant_id );

								/**
								 * Perform actions after making payment.
								 *
								 * @param string $participant_id participant id.
								 */
								do_action( 'wacara_after_making_payment', $participant_id, $pricing_price, $pricing_currency );

							} else {

								// Save stripe error message.
								$stripe_error_message = $charge->message;
								$reg_status           = 'fail';

								// Update result.
								$result->message = $charge->message;
							}
						} else {

							// Update result.
							$result->message = __( 'Failed to contact stripe, please try again later', 'wacara' );
						}

						// Save stripe error message.
						$participant->save_stripe_error_message( $stripe_error_message );
						break;

					default:
						// There is nothing to do here, just finish the process :).
						$result->success  = true;
						$result->callback = get_permalink( $participant_id );

						// Save registration status.
						$reg_status = 'done';
						break;
				}

				/**
				 * Perform actions after finishing registration.
				 *
				 * @param string $participant_id participant id.
				 */
				do_action( 'wacara_after_finishing_registration', $participant_id );

				// Update registration status.
				$participant->set_registration_status( $reg_status );

				// Save payment method information.
				$participant->save_payment_method_info( $maybe_payment_method );

			} else {

				// Update the result.
				$result->message = __( 'Please reload the page and try again', 'wacara' );
			}

			wp_send_json( $result );
		}

		/**
		 * Callback for finding participant by booking code.
		 */
		public function find_by_booking_code_callback() {
			$result       = new Result();
			$booking_code = Helper::post( 'booking_code' );

			// Validate the input.
			if ( $booking_code ) {

				// Find the participant by booking code.
				$find_participant = Participant::find_participant_by_booking_code( $booking_code );

				// Validate the find participant.
				if ( $find_participant->success ) {

					// Save participant id into variable.
					$participant_id = $find_participant->callback;

					// Instance the participant.
					$participant = new Participant( $participant_id );
					if ( $participant->success ) {
						$result->success  = true;
						$result->items    = $participant->get_data();
						$result->callback = $participant_id;
					} else {
						$result->message = $participant->message;
					}
				} else {
					$result->message  = $find_participant->message;
					$result->callback = $find_participant->callback;
				}
			} else {
				$result->message = __( 'Please try again later', 'wacara' );
			}

			wp_send_json( $result );
		}

		/**
		 * Callback for processing checkin.
		 */
		public function participant_checkin_callback() {
			$result         = new Result();
			$participant_id = Helper::post( 'participant_id' );
			if ( $participant_id ) {

				// Instance participant.
				$participant = new Participant( $participant_id );

				// Perform checkin.
				$participant->maybe_do_checkin();

				// Validate checkin status.
				if ( $participant->success ) {
					$result->success = true;
					$result->message = __( 'Thank you for checking in', 'wacara' );
				} else {
					$result->message = $participant->message;
				}
			} else {
				$result->message = __( 'Please try again later', 'wacara' );
			}

			wp_send_json( $result );
		}
	}
}

Ajax::init();
