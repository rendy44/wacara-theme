<?php
/**
 * Use this class to add custome ajax handler
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Ajax' ) ) {

	/**
	 * Class Ajax
	 *
	 * @package Wacara
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
			$endpoints = $this->get_default_endpoints();

			foreach ( $endpoints as $endpoint => $endpoint_obj ) {
				$args = Helper::maybe_convert_ajax_endpoint_obj( $endpoint_obj );

				Helper::add_ajax_endpoint( $endpoint, $args['callback'], $args['public'], $args['logged_in'] );
			}
		}

		/**
		 * Map ajax endpoint and its callback
		 *
		 * @return array
		 */
		private function get_default_endpoints() {
			return [
				'select_price'     => [
					'callback' => [ $this, 'register_callback' ],
				],
				'fill_detail'      => [
					'callback' => [ $this, 'payment_callback' ],
				],
				'checkout'         => [
					'callback' => [ $this, 'checkout_callback' ],
				],
				'find_reg_by_code' => [
					'callback' => [ $this, 'find_by_booking_code_callback' ],
				],
				'checkin'          => [
					'callback' => [ $this, 'registrant_checkin_callback' ],
				],
				'list_registrants' => [
					'callback' => [ $this, 'list_registrants_callback' ],
				],
			];
		}

		/**
		 * Callback for listing registrants
		 */
		public function list_registrants_callback() {
			$result   = new Result();
			$data     = Helper::get( 'data' );
			$event_id = Helper::array_val( $data, 'id' );
			$page     = Helper::array_val( $data, 'page' );

			// Maybe validate the page.
			if ( ! $page ) {
				$page = 1;
			}

			// Validate the inputs.
			if ( $event_id ) {

				// Override the result with event instance.
				$event = new Event( $event_id );

				// Run the method to fetch all registrants.
				$event->get_all_registrants_by_registration_status( $page );

				// Override the result object.
				$result = $event;

				// Get column name for csv.
				$csv_columns = [
					'booking_code'        => __( 'Booking Code', 'wacara' ),
					'name'                => __( 'Name', 'wacara' ),
					'email'               => __( 'Email', 'wacara' ),
					'readable_reg_status' => __( 'Status', 'wacara' ),
				];

				/**
				 * Wacara csv column filter hook.
				 *
				 * @param array $csv_columns current csv column.
				 * @param Event $event current selected event.
				 */
				$result->callback = apply_filters( 'wacara_filter_event_csv_columns', $csv_columns, $event );

			} else {
				$result->message = __( 'Please try again later', 'wacara' );
			}

			wp_send_json( $result );
		}

		/**
		 * Callback for registering registrant.
		 */
		public function register_callback() {
			$result     = new Result();
			$data       = Helper::post( 'data' );
			$event_id   = Helper::array_val( $data, 'event_id' );
			$pricing_id = Helper::array_val( $data, 'pricing_id' );

			// Validate the inputs.
			if ( $event_id && $pricing_id ) {

				// Instance the event obj.
				$event = new Event( $event_id, true );

				// Validate the event before using it for registration.
				$event->validate_event();

				// Validate the event.
				if ( $event->success ) {

					// create registrant.
					$new_registrant = new Registrant(
						false,
						[
							'event_id'   => $event_id,
							'pricing_id' => $pricing_id,
						]
					);

					// Validate the newly created event.
					if ( $new_registrant->success ) {

						// Recount the event.
						$event->maybe_recount_limitation();

						// Update the result.
						$result->success  = true;
						$result->callback = $new_registrant->get_registrant_url();
					} else {

						// Update the result.
						$result->message = $new_registrant->message;
					}
				} else {

					// Update the result.
					$result->message = $event->message;
				}
			} else {

				// Update the result.
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
			$registrant_id   = Helper::get_serialized_val( $unserialize_obj, 'registrant_id' );
			$nonce           = Helper::get_serialized_val( $unserialize_obj, '_wpnonce' );

			// Validate the inputs.
			if ( $registrant_id ) {

				// Validate the nonce.
				if ( wp_verify_nonce( $nonce, 'wacara_nonce' ) ) {

					// Instance the registrant.
					$registrant = new Registrant( $registrant_id );

					// Validate the registrant.
					if ( $registrant->success ) {

						// Define variables.
						$maybe_payment_method = Helper::get_serialized_val( $unserialize_obj, 'payment_method' );
						$email                = Helper::get_serialized_val( $unserialize_obj, 'email' );
						$name                 = Helper::get_serialized_val( $unserialize_obj, 'name' );
						$company              = Helper::get_serialized_val( $unserialize_obj, 'company' );
						$position             = Helper::get_serialized_val( $unserialize_obj, 'position' );
						$phone                = Helper::get_serialized_val( $unserialize_obj, 'phone' );
						$id_number            = Helper::get_serialized_val( $unserialize_obj, 'id_number' );
						$pricing_id           = Helper::get_post_meta( 'pricing_id', $registrant->post_id );

						// Instance the pricing.
						$pricing = new Pricing( $pricing_id );

						// Validate the pricing.
						if ( $pricing->success ) {

							// Fetch pricing's details.
							$pricing_currency = $pricing->get_currency_code();
							$pricing_price    = $pricing->get_price();

							// First, convert the price into cent.
							$pricing_price_in_cent = $pricing_price * 100;

							// Instance the registrant.
							$registrant = new Registrant( $registrant_id );

							// Save the details.
							$registrant->save_more_details( $name, $email, $company, $position, $phone, $id_number );

							// Save invoice info.
							$registrant->save_invoicing_info( $pricing_price_in_cent, $pricing_currency );

							// Define some variables related to registration.
							$reg_status = '';

							/**
							 * Wacara before filling registrant detail hook.
							 *
							 * @param Registrant $registrant object of the current registrant.
							 * @param string $maybe_payment_method maybe selected payment method.
							 */
							do_action( 'wacara_before_filling_registrant', $registrant, $maybe_payment_method );

							// Check pricing price.
							if ( $pricing_price > 0 ) {

								// Check if payment method is selected.
								if ( $maybe_payment_method ) {

									// Update the result.
									$result->success = true;

									// Save registration status.
									$reg_status = 'hold';

								} else {

									// Update the result.
									$result->message = __( 'Please select a payment method', 'wacara' );
								}
							} else {

								// There is nothing to do here, since payment is not required just finish the process :).
								$result->success = true;

								// Save registration status.
								$reg_status = 'done';
							}

							/**
							 * Wacara after filling registrant hook.
							 *
							 * @param Registrant $registrant object of the current registrant.
							 * @param string $reg_status the status of registration.
							 */
							do_action( 'wacara_after_filling_registration', $registrant, $reg_status );

							// Update the callback.
							$result->callback = $registrant->get_registrant_url();

							// Update registration status.
							$registrant->set_registration_status( $reg_status );

							// Save payment method information.
							$registrant->save_payment_method_info( $maybe_payment_method );

						} else {

							// Update result.
							$result->message = $pricing->message;
						}
					} else {

						// Update the result.
						$result->message = $registrant->message;
					}
				} else {

					// Update the result.
					$result->message = __( 'Please reload the page and try again', 'wacara' );
				}
			} else {

				// Update the result.
				$result->message = __( 'Please try again later', 'wacara' );
			}

			wp_send_json( $result );
		}

		/**
		 * Callback for checking-out.
		 */
		public function checkout_callback() {
			$result          = new Result();
			$data            = Helper::post( 'data' );
			$unserialize_obj = maybe_unserialize( $data );
			$registrant_id   = Helper::get_serialized_val( $unserialize_obj, 'registrant_id' );
			$nonce           = Helper::get_serialized_val( $unserialize_obj, '_wpnonce' );

			// Validate the registrant.
			if ( $registrant_id ) {

				// Validate the nonce.
				if ( wp_verify_nonce( $nonce, 'wacara_nonce' ) ) {

					// Instance the registrant.
					$registrant = new Registrant( $registrant_id );

					// Validate the registrant.
					if ( $registrant->success ) {

						// Get registrant invoice detail.
						$invoice = $registrant->get_invoicing_info();

						// Instance pricing.
						$pricing = new Pricing( $invoice['pricing_id'] );

						// Validate the pricing.
						if ( $pricing->success ) {

							// Fetch pricing detail.
							$pricing_price         = $pricing->get_price();
							$pricing_price_in_cent = $pricing_price * 100;
							$pricing_currency      = $pricing->get_currency_code();

							// Fetch payment method.
							$payment_method = $registrant->get_payment_method_id();

							// Save default registrant status.
							$reg_status = $registrant->get_registration_status();

							/**
							 * Wacara before registrant payment process hook.
							 *
							 * @param Registrant $registrant object of the current registrant.
							 */
							do_action( 'wacara_before_registrant_payment_process', $registrant );

							// Process the payment.
							$selected_payment_method = Register_Payment::get_payment_method_class( $payment_method );

							// Check if payment method is exist.
							if ( $selected_payment_method ) {

								// Process the payment.
								$do_payment = $selected_payment_method->process( $registrant, $unserialize_obj, $pricing_price_in_cent, $pricing_currency );

								// Validate the process.
								if ( $do_payment->success ) {

									// Update the result.
									$result->success = true;
								} else {

									// Update the result.
									$result->message = $do_payment->message;
								}

								// Save registrant status.
								$reg_status = $do_payment->callback;
							} else {

								// Update the result.
								$result->message = __( 'Unknown payment method', 'wacara' );
							}

							// Update the callback.
							$result->callback = $registrant->get_registrant_url();

							// Update the registrant status.
							$registrant->set_registration_status( $reg_status );

							/**
							 * Wacara after registrant payment process hook.
							 *
							 * @param Registrant $registrant object of the current registrant.
							 * @param string $reg_status status of the current registrant.
							 */
							do_action( 'wacara_after_registrant_payment_process', $registrant, $reg_status );

						} else {

							// Update the result.
							$result->success = $pricing->message;
						}
					} else {

						// Update the result.
						$result->message = $registrant->message;
					}
				} else {

					// Update the result.
					$result->message = __( 'Please reload the page and try again', 'wacara' );
				}
			} else {

				// Update the result.
				$result->message = __( 'Please try again later', 'wacara' );
			}

			wp_send_json( $result );
		}

		/**
		 * Callback for finding registrant by booking code.
		 */
		public function find_by_booking_code_callback() {
			$result       = new Result();
			$booking_code = Helper::post( 'booking_code' );

			// Validate the input.
			if ( $booking_code ) {

				// Find the registrant by booking code.
				$find_registrant = Registrant::find_registrant_by_booking_code( $booking_code );

				// Validate the find registrant.
				if ( $find_registrant->success ) {

					// Save registrant id into variable.
					$registrant_id = $find_registrant->callback;

					// Instance the registrant.
					$registrant = new Registrant( $registrant_id );
					if ( $registrant->success ) {
						$result->success  = true;
						$result->items    = $registrant->get_data();
						$result->callback = $registrant_id;
					} else {
						$result->message = $registrant->message;
					}
				} else {
					$result->message  = $find_registrant->message;
					$result->callback = $find_registrant->callback;
				}
			} else {
				$result->message = __( 'Please try again later', 'wacara' );
			}

			wp_send_json( $result );
		}

		/**
		 * Callback for processing checkin.
		 */
		public function registrant_checkin_callback() {
			$result        = new Result();
			$registrant_id = Helper::post( 'registrant_id' );

			// Validate the input.
			if ( $registrant_id ) {

				// Instance registrant.
				$registrant = new Registrant( $registrant_id );

				// Perform checkin.
				$registrant->maybe_do_checkin();

				// Validate checkin status.
				if ( $registrant->success ) {
					$result->success = true;
					$result->message = __( 'Thank you for checking in', 'wacara' );
				} else {
					$result->message = $registrant->message;
				}
			} else {
				$result->message = __( 'Please try again later', 'wacara' );
			}

			wp_send_json( $result );
		}
	}

	Ajax::init();
}
