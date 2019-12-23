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

			// Register ajax endpoint for processing payment confirmation.
			add_action( 'wp_ajax_nopriv_confirmation', [ $this, 'confirmation_callback' ] );
			add_action( 'wp_ajax_confirmation', [ $this, 'confirmation_callback' ] );

			// Register ajax endpoint for finding participant by booking code before checking in..
			add_action( 'wp_ajax_nopriv_find_by_booking_code', [ $this, 'find_by_booking_code_callback' ] );
			add_action( 'wp_ajax_find_by_booking_code', [ $this, 'find_by_booking_code_callback' ] );

			// Register ajax endpoint for processing checkin.
			add_action( 'wp_ajax_nopriv_participant_checkin', [ $this, 'participant_checkin_callback' ] );
			add_action( 'wp_ajax_participant_checkin', [ $this, 'participant_checkin_callback' ] );

			// Register ajax endpoint for displaying all participants.
			add_action( 'wp_ajax_nopriv_list_participants', [ $this, 'list_participants_callback' ] );
			add_action( 'wp_ajax_list_participants', [ $this, 'list_participants_callback' ] );

			// Register ajax endpoint for displaying participant payment status.
			add_action( 'wp_ajax_check_payment_status', [ $this, 'check_payment_status_callback' ] );

			// Register ajax endpoint for either verify or reject payment.
			add_action( 'wp_ajax_verify_payment', [ $this, 'check_verify_payment_callback' ] );
		}

		/**
		 * Callback for performing payment action.
		 */
		public function check_verify_payment_callback() {
			$result         = new Result();
			$participant_id = Helper::post( 'id' );
			$new_status     = Helper::post( 'status' );

			// Validate the inputs.
			if ( $participant_id && $new_status ) {

				// Instance participant object.
				$participant = new Participant( $participant_id );

				// Validate the participant object.
				if ( $participant->success ) {

					// Validate the new status output.
					$message_output = __( 'Verification is successful', 'wacara' );
					if ( 'done' !== $new_status ) {
						$new_status     = 'fail';
						$message_output = __( 'Rejection is successful', 'wacara' );
					}

					// Update the status.
					$participant->set_registration_status( $new_status );

					// Update the result.
					$result->success = true;
					$result->message = $message_output;

				} else {
					$result->message = $participant->message;
				}
			} else {
				$result->message = __( 'Please try again later', 'wacara' );
			}

			wp_send_json( $result );
		}

		/**
		 * Callback for checking payment status.
		 */
		public function check_payment_status_callback() {
			$participant_id = Helper::get( 'id' );
			$output         = __( 'Please try again later', 'wacara' );

			// Validate the inputs.
			if ( $participant_id ) {

				// Instance participant object.
				$participant = new Participant( $participant_id );

				// Validate the participant object.
				if ( $participant->success ) {

					// Get registration status.
					$reg_status = $participant->get_registration_status();

					// Validate registration status.
					if ( 'wait_verification' === $reg_status ) {

						// Collect payment infor.
						$payment_info       = $participant->get_manual_payment_info_status();
						$payment_info['id'] = $participant->post_id;

						// Update the output result.
						$output = Template::render( 'admin/participant-detail', $payment_info );

					} else {
						$output = __( 'Invalid participant', 'wacara' );
					}
				} else {
					$output = $participant->message;
				}
			}

			echo $output; // phpcs:ignore
			die( 200 );
		}

		/**
		 * Callback for listing participants
		 */
		public function list_participants_callback() {
			$result   = new Result();
			$event_id = Helper::get( 'id' );
			$page     = Helper::get( 'page' );

			// Maybe validate the page.
			if ( ! $page ) {
				$page = 1;
			}

			// Validate the inputs.
			if ( $event_id ) {

				// Override the result with event instance.
				$result = new Event( $event_id );

				// Run the method to fetch all participants.
				$result->get_all_participants_by_registration_status( $page );

				// Get column name for csv.
				$result->callback = [
					__( 'Booking Code', 'wacara' ),
					__( 'Name', 'wacara' ),
					__( 'Email', 'wacara' ),
					__( 'Company', 'wacara' ),
					__( 'Position', 'wacara' ),
					__( 'Phone', 'wacara' ),
					__( 'Id Number', 'wacara' ),
					__( 'Status', 'wacara' ),
				];

			} else {
				$result->message = __( 'Please try again later', 'wacara' );
			}

			wp_send_json( $result );
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
		 * Callback for making payment confirmation with manual bank transfer.
		 */
		public function confirmation_callback() {
			$result          = new Result();
			$data            = Helper::post( 'data' );
			$unserialize_obj = maybe_unserialize( $data );
			$participant_id  = Helper::get_serialized_val( $unserialize_obj, 'participant_id' );
			$bank_account    = Helper::get_serialized_val( $unserialize_obj, 'selected_bank' );
			$nonce           = Helper::get_serialized_val( $unserialize_obj, 'sk_payment' );

			// Validate the inputs.
			if ( $participant_id && isset( $bank_account ) ) {

				// Validate the nonce.
				if ( wp_verify_nonce( $nonce, 'sk_nonce' ) ) {

					// Prepare the variables.
					$bank_accounts = [];

					// Get payment method options.
					$payment_method     = Helper::get_post_meta( 'payment_method', $participant_id );
					$payment_method_obj = Register_Payment::get_payment_method_class( $payment_method );
					if ( $payment_method_obj ) {
						$bank_accounts = $payment_method_obj->get_admin_setting( 'bank_accounts' );
					}

					// Instance the participant.
					$participant = new Participant( $participant_id );

					// Update the registration.
					$participant->update_confirmation( $bank_account, $bank_accounts );

					// Check the success status.
					if ( $participant->success ) {
						$result->success  = true;
						$result->callback = $participant->get_participant_url();
					} else {
						$result->message = $participant->message;
					}
				} else {

					// Update the result.
					$result->message = __( 'Please reload the page and try again', 'wacara' );
				}
			} else {

				// Update the result.
				$result->message = __( 'Please select the bank account', 'wacara' );
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

			// Validate the inputs.
			if ( $participant_id ) {

				// Validate the nonce.
				if ( wp_verify_nonce( $nonce, 'sk_nonce' ) ) {

					// Define variables.
					$maybe_payment_method = Helper::get_serialized_val( $unserialize_obj, 'payment_method' );
					$email                = Helper::get_serialized_val( $unserialize_obj, 'email' );
					$name                 = Helper::get_serialized_val( $unserialize_obj, 'name' );
					$company              = Helper::get_serialized_val( $unserialize_obj, 'company' );
					$position             = Helper::get_serialized_val( $unserialize_obj, 'position' );
					$phone                = Helper::get_serialized_val( $unserialize_obj, 'phone' );
					$id_number            = Helper::get_serialized_val( $unserialize_obj, 'id_number' );
					$pricing_id           = Helper::get_post_meta( 'pricing_id', $participant_id );
					$pricing_currency     = Helper::get_post_meta( 'currency', $pricing_id );
					$pricing_price        = Helper::get_post_meta( 'price', $pricing_id );

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
					 * @param string $participant_id the id of participant.
					 * @param string $maybe_payment_method maybe selected payment method.
					 */
					do_action( 'wacara_before_finishing_registration', $participant_id, $maybe_payment_method );

					// Check if using payment method.
					if ( $maybe_payment_method ) {
						$selected_payment_method = Register_Payment::get_payment_method_class( $maybe_payment_method );

						// Check if payment method is exist.
						if ( $selected_payment_method ) {
							$do_payment = $selected_payment_method->process( $participant, $unserialize_obj, $pricing_price, $pricing_currency );
							// Validate the process.
							if ( $do_payment->success ) {

								// Update the registration status.
								$result->success = true;
								$reg_status      = $do_payment->callback;
							} else {

								// Update the result.
								$result->message = $do_payment->message;
							}
						}
					} else {

						// There is nothing to do here, just finish the process :).
						$result->success = true;

						// Save registration status.
						$reg_status = 'done';
					}

					/**
					 * Perform actions after finishing registration.
					 *
					 * @param string $participant_id participant id.
					 * @param string $reg_status the status of registration.
					 */
					do_action( 'wacara_after_finishing_registration', $participant_id, $reg_status );

					// Update the callback.
					$result->callback = $participant->get_participant_url();

					// Update registration status.
					$participant->set_registration_status( $reg_status );

					// Save payment method information.
					$participant->save_payment_method_info( $maybe_payment_method );

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

			// Validate the input.
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
