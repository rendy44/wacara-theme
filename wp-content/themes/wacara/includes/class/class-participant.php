<?php
/**
 * Use this class to manage participants.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

use QRcode;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Skeleton\Participant' ) ) {
	/**
	 * Class Participant
	 *
	 * @package Skeleton
	 */
	class Participant extends Post {

		/**
		 * Participant data.
		 *
		 * @var array
		 */
		public $participant_data = [];

		/**
		 * Participant constructor.
		 *
		 * @param bool  $participant_id leave it empty to create a new participant,
		 *                              and assign with participant id to fetch the participant's detail.
		 * @param array $args           arguments to create a new participant.
		 */
		public function __construct( $participant_id = false, $args = [] ) {

			// Create a new participant.
			if ( ! $participant_id ) {

				// Prepare default args.
				$default_args = [
					'event_id'   => false,
					'pricing_id' => false,
				];

				// Parse the arguments.
				$args = wp_parse_args( $args, $default_args );

				// Validate inputs.
				if ( $args['event_id'] && $args['pricing_id'] ) {

					// Save event id to variable.
					$event_id = $args['event_id'];

					// Generate unique key.
					$participant_key = wp_generate_password( 12, false );

					/**
					 * Perform the filter to modify participant key.
					 *
					 * @param string $participant_key participant random key.
					 */
					apply_filters( 'wacara_filter_participant_key', $participant_key );

					/**
					 * Perform action before creating participant
					 *
					 * @param array $args setting for creating new post.
					 */
					do_action( 'wacara_before_creating_participant', $args );

					// Proceed creating participant.
					$new_participant = wp_insert_post(
						[
							'post_type'   => 'participant',
							'post_title'  => strtoupper( $participant_key ),
							'post_name'   => sanitize_title( $participant_key ),
							'post_status' => 'publish',
						]
					);

					/**
					 * Perform action after creating participant
					 *
					 * @param array        $args            setting for creating new post.
					 * @param int|WP_Error $new_participant result of newly created participant.
					 */
					do_action( 'wacara_after_creating_participant', $args, $new_participant );

					// Validate after creating participant.
					if ( is_wp_error( $new_participant ) ) {

						// Update result.
						$this->success = false;
						$this->message = $new_participant->get_error_messages();
					} else {

						// Create participant booking code.
						$event_and_id_length = strlen( $event_id . $new_participant );
						$unique_length       = 8 - $event_and_id_length;
						$booking_code        = strtoupper( $event_id . wp_generate_password( $unique_length, false ) . $new_participant );

						/**
						 * Perform filter to modify participant publishable key.
						 *
						 * @param string $booking_code    the original publishable key.
						 * @param string $event_id        event id of the participant.
						 * @param int    $new_participant id number of newly created participant.
						 */
						$booking_code = apply_filters( 'wacara_filter_participant_booking_code', $booking_code, $event_id, $new_participant );

						// Add booking code to participant meta.
						$args['booking_code'] = $booking_code;

						// Update class object.
						$this->success          = true;
						$this->post_id          = $new_participant;
						$this->post_url         = get_permalink( $new_participant );
						$this->participant_data = $args;

						// Create qrcode for participant.
						$this->save_qrcode_to_participant();

						// Update participant meta after successfully being created.
						parent::save_meta( $args );
					}
				} else {

					// Update result.
					$this->success = false;
					$this->message = __( 'Please use valid input', 'wacara' );
				}
			} else {

				// Fetch the detail.
				parent::__construct( $participant_id, 'participant' );

				// Validate the participant id.
				if ( $this->success ) {

					// Fetch participant detail.
					$this->participant_data = parent::get_meta(
						[
							'booking_code',
							'email',
							'name',
							'company',
							'position',
							'phone',
							'id_number',
							'booking_code',
							'event_id',
							'pricing_id',
						]
					);

					// Get readable status.
					$readable_status = '';
					$status          = parent::get_meta( 'reg_status' );
					switch ( $status ) {
						case 'wait_payment':
							$readable_status = __( 'Waiting Payment', 'wacara' );
							break;
						case 'wait_verification':
							$readable_status = __( 'Waiting Verification', 'wacara' );
							break;
						case 'fail':
							$readable_status = __( 'Failed', 'wacara' );
							break;
						case 'done':
							$readable_status = __( 'Success', 'wacara' );
							break;
					}

					// Save status into object.
					$this->participant_data['reg_status']          = $status;
					$this->participant_data['readable_reg_status'] = $readable_status;

				}
			}
		}

		/**
		 * Save qrcode image locally.
		 */
		private function generate_qrcode_locally() {
			$qrcode_name = $this->post_id;
			$file_name   = TEMP_PATH . "/assets/qrcode/{$qrcode_name}.png";
			QRcode::png( $qrcode_name, $file_name, QR_ECLEVEL_H, 5 );
		}

		/**
		 * Save qrcode information into participant.
		 */
		private function save_qrcode_to_participant() {
			// Generate qrcode locally.
			$this->generate_qrcode_locally();

			// Save qrcode data.
			$qrcode_name = $this->post_id . '.png';
			$qrcode_uri  = TEMP_URI . '/assets/qrcode/' . $qrcode_name;

			// Save qrcode into participant.
			parent::save_meta(
				[
					'qrcode_name' => $qrcode_name,
					'qrcode_url'  => $qrcode_uri,
				]
			);

			// Save participant id into variable.
			$participant_id = $this->post_id;

			/**
			 * Perform actions after creating participant qrcode.
			 *
			 * @param string $participant_id participant id.
			 * @param string $qrcode_uri     the url of generated qrcode.
			 */
			do_action( 'wacara_after_creating_participant_qrcode', $participant_id, $qrcode_uri );
		}

		/**
		 * Get checkin date lists.
		 *
		 * @return array
		 */
		public function get_checkin_lists() {
			$checkin_dates = (array) parent::get_meta( 'checkin_dates' );

			return array_filter( $checkin_dates );
		}

		/**
		 * Save more participant`s details
		 *
		 * @param string $name      participant name.
		 * @param string $email     participant email.
		 * @param string $company   participant company.
		 * @param string $position  participant position.
		 * @param string $phone     participant phone.
		 * @param string $id_number participant id number.
		 */
		public function save_more_details( $name = '', $email = '', $company = '', $position = '', $phone = '', $id_number = '' ) {
			parent::save_meta(
				[
					'name'      => $name,
					'email'     => $email,
					'company'   => $company,
					'position'  => $position,
					'phone'     => $phone,
					'id_number' => $id_number,
				]
			);
		}

		/**
		 * Maybe perform checkin.
		 */
		public function maybe_do_checkin() {
			// Save participant id into variable.
			$participant_id = $this->post_id;

			// Get participant status.
			$reg_status = parent::get_meta( 'reg_status' );

			// Validate participant status.
			if ( 'done' === $reg_status ) {

				// Save event id into variable.
				$event_id = parent::get_meta( 'event_id' );

				// Instance event obj.
				$event = new Event( $event_id, true );

				// Check is in checkin period.
				$is_in_checkin_period = $event->is_in_checkin_period();
				if ( $is_in_checkin_period ) {

					// Check whether participant already checkin today.
					$is_checkin_today = $this->is_today_checkin();

					if ( ! $is_checkin_today ) {
						/**
						 * Perform action before participant checkin.
						 *
						 * @param string $participant_id id of participant that will be check-in.
						 */
						do_action( 'wacara_before_participant_checkin', $participant_id );

						// Finally, do the checkin.
						$this->do_checkin();

						// Update the result.
						$this->success = true;

						/**
						 * Perform action after participant checkin.
						 *
						 * @param string $participant_id id of participant that just checked-in.
						 */
						do_action( 'wacara_after_participant_checkin', $participant_id );

					} else {
						$this->success = false;
						$this->message = __( 'You have already checked in for today', 'wacara' );
					}
				} else {
					$this->success = false;
					$this->message = __( 'You are not allowed to checkin, since the event is completely past', 'wacara' );
				}
			} else {
				$this->success = false;
				$this->message = __( 'You are not allowed to checkin', 'wacara' );
			}
		}

		/**
		 * Do checkin for today.
		 */
		private function do_checkin() {
			$today_timestamp    = Helper::get_today_timestamp();
			$previous_checkin   = $this->get_checkin_lists();
			$previous_checkin[] = $today_timestamp;

			// Update the checkin dates.
			parent::save_meta( [ 'checkin_dates' => $previous_checkin ] );
		}

		/**
		 * Check whether the participant already checkin for today ot not.
		 *
		 * @return bool
		 */
		private function is_today_checkin() {
			$result          = false;
			$today_timestamp = Helper::get_today_timestamp();
			$checkin_dates   = $this->get_checkin_lists();
			if ( in_array( $today_timestamp, $checkin_dates, false ) ) { // phpcs:ignore
				$result = true;
			}

			return $result;
		}

		/**
		 * Get participant data.
		 *
		 * @return array|bool|mixed
		 */
		public function get_data() {
			return $this->participant_data;
		}

		/**
		 * Get participant url.
		 *
		 * @return false|string
		 */
		public function get_participant_url() {
			return $this->post_url;
		}

		/**
		 * Set participant registration status.
		 *
		 * @param string $status status of registration.
		 */
		public function set_registration_status( $status = 'done' ) {

			// Save old status into variable.
			$old_status = parent::get_meta( 'reg_status' );

			// Save participant id into variable.
			$participant_id = $this->post_id;

			// Change the status.
			parent::save_meta( [ 'reg_status' => $status ] );

			/**
			 * Perform action when participant status changed.
			 *
			 * @param string $participant_id the participant id.
			 * @param string $status         the new status of participant.
			 * @param string $old_status     the old status of participant.
			 */
			do_action( 'wacara_after_setting_participant_status', $participant_id, $status, $old_status );
		}

		/**
		 * Save stripe error message.
		 *
		 * @param string $error_message error message.
		 */
		public function save_stripe_error_message( $error_message = '' ) {
			parent::save_meta( [ 'stripe_error_message' => $error_message ] );
		}

		/**
		 * Save payment method information
		 *
		 * @param string $payment_method selected payment method.
		 */
		public function save_payment_method_info( $payment_method ) {
			parent::save_meta( [ 'payment_method' => $payment_method ] );
		}

		/**
		 * Save invoice information
		 *
		 * @param int    $price_need_to_pay_in_cent the amount that should be paid in cent.
		 * @param string $currency                  currency code of invoice.
		 */
		public function save_invoicing_info( $price_need_to_pay_in_cent, $currency ) {
			parent::save_meta(
				[
					'price_in_cent' => $price_need_to_pay_in_cent,
					'currency'      => $currency,
				]
			);
		}

		/**
		 * Update the registration status after confirming the transfer.
		 *
		 * @param int $bank_account_number the index number of bank account array.
		 */
		public function update_confirmation( $bank_account_number ) {

			// Save participant id into variable.
			$participant_id = $this->post_id;

			// Prepare some variables.
			$date_update           = current_time( 'timestamp' );
			$bank_accounts         = Options::get_bank_accounts();
			$selected_bank_account = ! empty( $bank_accounts[ $bank_account_number ] ) ? $bank_accounts : false;

			// Validate the selected bank accounts.
			if ( $selected_bank_account ) {

				/**
				 * Perform actions before confirming payment
				 *
				 * @param string $participant_id the participant id.
				 */
				do_action( 'wacara_before_confirming_payment', $participant_id );

				// Update the status.
				$this->success = true;
				$this->set_registration_status( 'wait_verification' );

				// Update the status.
				parent::save_meta(
					[
						'confirmation_timestamp' => $date_update,
						'selected_bank_account'  => $selected_bank_account,
					]
				);

				/**
				 * Perform actions after confirming payment.
				 *
				 * @param string $participant_id the participant id.
				 */
				do_action( 'wacara_after_confirming_payment', $participant_id );

			} else {
				$this->success = false;
				$this->message = __( 'Invalid bank account selected', 'wacara' );
			}
		}

		/**
		 * Maybe save unique number for easier payment confirmation.
		 *
		 * @param int $unique_number the unique number.
		 */
		public function maybe_save_unique_number( $unique_number ) {
			$old_price_in_cent                    = parent::get_meta( 'price_in_cent' );
			$new_price_with_unique_number_in_cent = $old_price_in_cent + $unique_number;
			parent::save_meta(
				[
					'maybe_unique_number'             => $unique_number,
					'maybe_price_in_cent_with_unique' => $new_price_with_unique_number_in_cent,
				]
			);
		}

		/**
		 * Find participant by their booking code.
		 *
		 * @param string $booking_code booking code.
		 *
		 * @return Result
		 */
		public static function find_participant_by_booking_code( $booking_code ) {
			return Helper::get_post_id_by_meta_key( 'booking_code', $booking_code, 'participant' );
		}
	}
}
