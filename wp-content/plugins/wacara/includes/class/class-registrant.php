<?php
/**
 * Use this class to manage registrants.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Wacara;

use QRcode;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Registrant' ) ) {

	/**
	 * Class Registrant
	 *
	 * @package Wacara
	 */
	class Registrant extends Post {

		/**
		 * Registrant data.
		 *
		 * @var array
		 */
		public $registrant_data = [];

		/**
		 * Registrant constructor.
		 *
		 * @param bool  $registrant_id leave it empty to create a new registrant,
		 *                                 and assign with registrant id to fetch the registrant's detail.
		 * @param array $args arguments to create a new registrant.
		 *                              Or list of field to displaying registrant.
		 */
		public function __construct( $registrant_id = false, $args = [] ) {

			// Create a new registrant.
			if ( ! $registrant_id ) {

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
					$registrant_key = wp_generate_password( 12, false );

					/**
					 * Perform the filter to modify registrant key.
					 *
					 * @param string $registrant_key registrant random key.
					 */
					$registrant_key = apply_filters( 'wacara_filter_registrant_key', $registrant_key );

					/**
					 * Perform action before creating registrant
					 *
					 * @param array $args setting for creating new post.
					 */
					do_action( 'wacara_before_creating_registrant', $args );

					// Proceed creating registrant.
					$new_registrant = wp_insert_post(
						[
							'post_type'   => 'registrant',
							'post_title'  => strtoupper( $registrant_key ),
							'post_name'   => sanitize_title( $registrant_key ),
							'post_status' => 'publish',
						]
					);

					/**
					 * Perform action after creating registrant
					 *
					 * @param array $args setting for creating new post.
					 * @param int|WP_Error $new_registrant result of newly created registrant.
					 */
					do_action( 'wacara_after_creating_registrant', $args, $new_registrant );

					// Validate after creating registrant.
					if ( is_wp_error( $new_registrant ) ) {

						// Update result.
						$this->success = false;
						$this->message = $new_registrant->get_error_messages();
					} else {

						// Create registrant booking code.
						$event_and_id_length = strlen( $event_id . $new_registrant );
						$unique_length       = 8 - $event_and_id_length;
						$booking_code        = strtoupper( $event_id . wp_generate_password( $unique_length, false ) . $new_registrant );

						/**
						 * Perform filter to modify registrant publishable key.
						 *
						 * @param string $booking_code the original publishable key.
						 * @param string $event_id event id of the registrant.
						 * @param int $new_registrant id number of newly created registrant.
						 */
						$booking_code = apply_filters( 'wacara_filter_registrant_booking_code', $booking_code, $event_id, $new_registrant );

						// Add booking code to registrant meta.
						$args['booking_code'] = $booking_code;

						// Update class object.
						$this->success         = true;
						$this->post_id         = $new_registrant;
						$this->post_url        = get_permalink( $new_registrant );
						$this->registrant_data = $args;

						// Create qrcode for registrant.
						$this->save_qrcode_to_registrant();

						// Update registrant meta after successfully being created.
						$this->save_meta( $args );
					}
				} else {

					// Update result.
					$this->success = false;
					$this->message = __( 'Please use valid input', 'wacara' );
				}
			} else {

				// Fetch the detail.
				parent::__construct( $registrant_id, 'registrant' );

				// Validate the registrant id.
				if ( $this->success ) {

					// Maybe merge displayed fields with args from parameter.
					$used_args = [
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
					];
					if ( ! empty( $args ) ) {
						$used_args = array_merge( $used_args, $args );
					}

					// Fetch registrant detail.
					$this->registrant_data = $this->get_meta( $used_args );

					// Get readable status.
					$readable_status = '';
					$status          = $this->get_meta( 'reg_status' );
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
					$this->registrant_data['reg_status']          = $status;
					$this->registrant_data['readable_reg_status'] = $readable_status;

				}
			}
		}

		/**
		 * Save qrcode image locally.
		 */
		private function generate_qrcode_locally() {
			$qrcode_name = $this->post_id;
			$file_name   = WACARA_PATH . "/assets/qrcode/{$qrcode_name}.png";
			QRcode::png( $qrcode_name, $file_name, QR_ECLEVEL_H, 5 );
		}

		/**
		 * Save qrcode information into registrant.
		 */
		private function save_qrcode_to_registrant() {
			// Generate qrcode locally.
			$this->generate_qrcode_locally();

			// Save qrcode data.
			$qrcode_name = $this->post_id . '.png';
			$qrcode_uri  = WACARA_URI . '/assets/qrcode/' . $qrcode_name;

			// Save qrcode into registrant.
			$this->save_meta(
				[
					'qrcode_name' => $qrcode_name,
					'qrcode_url'  => $qrcode_uri,
				]
			);

			// Save registrant id into variable.
			$registrant_id = $this->post_id;

			/**
			 * Perform actions after creating registrant qrcode.
			 *
			 * @param string $registrant_id registrant id.
			 * @param string $qrcode_uri the url of generated qrcode.
			 */
			do_action( 'wacara_after_creating_registrant_qrcode', $registrant_id, $qrcode_uri );
		}

		/**
		 * Get checkin date lists.
		 *
		 * @return array
		 */
		public function get_checkin_lists() {
			$checkin_dates = (array) $this->get_meta( 'checkin_dates' );

			return array_filter( $checkin_dates );
		}

		/**
		 * Save more registrant`s details
		 *
		 * @param string $name registrant name.
		 * @param string $email registrant email.
		 * @param string $company registrant company.
		 * @param string $position registrant position.
		 * @param string $phone registrant phone.
		 * @param string $id_number registrant id number.
		 */
		public function save_more_details( $name = '', $email = '', $company = '', $position = '', $phone = '', $id_number = '' ) {
			$this->save_meta(
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
			// Save registrant id into variable.
			$registrant_id = $this->post_id;

			// Get registrant status.
			$reg_status = $this->get_meta( 'reg_status' );

			// Validate registrant status.
			if ( 'done' === $reg_status ) {

				// Save event id into variable.
				$event_id = $this->get_meta( 'event_id' );

				// Instance event obj.
				$event = new Event( $event_id, true );

				// Check is in checkin period.
				$is_in_checkin_period = $event->is_in_checkin_period();
				if ( $is_in_checkin_period ) {

					// Check whether registrant already checkin today.
					$is_checkin_today = $this->is_today_checkin();

					if ( ! $is_checkin_today ) {
						/**
						 * Perform action before registrant checkin.
						 *
						 * @param string $registrant_id id of registrant that will be check-in.
						 */
						do_action( 'wacara_before_registrant_checkin', $registrant_id );

						// Finally, do the checkin.
						$this->do_checkin();

						// Update the result.
						$this->success = true;

						/**
						 * Perform action after registrant checkin.
						 *
						 * @param string $registrant_id id of registrant that just checked-in.
						 */
						do_action( 'wacara_after_registrant_checkin', $registrant_id );

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
			$this->save_meta( [ 'checkin_dates' => $previous_checkin ] );
		}

		/**
		 * Check whether the registrant already checkin for today ot not.
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
		 * Get registrant data.
		 *
		 * @return array|bool|mixed
		 */
		public function get_data() {
			return $this->registrant_data;
		}

		/**
		 * Get registrant url.
		 *
		 * @return false|string
		 */
		public function get_registrant_url() {
			return $this->post_url;
		}

		/**
		 * Set registrant registration status.
		 *
		 * @param string $status status of registration.
		 */
		public function set_registration_status( $status = 'done' ) {

			// Save old status into variable.
			$old_status = $this->get_registration_status();

			// Save registrant id into variable.
			$registrant_id = $this->post_id;

			// Change the status.
			$this->save_meta( [ 'reg_status' => $status ] );

			/**
			 * Perform action when registrant status changed.
			 *
			 * @param string $registrant_id the registrant id.
			 * @param string $status the new status of registrant.
			 * @param string $old_status the old status of registrant.
			 */
			do_action( 'wacara_after_setting_registrant_status', $registrant_id, $status, $old_status );
		}

		/**
		 * Save payment method information
		 *
		 * @param string $payment_method selected payment method.
		 */
		public function save_payment_method_info( $payment_method ) {
			$this->save_meta( [ 'payment_method' => $payment_method ] );
		}

		/**
		 * Save invoice information
		 *
		 * @param int    $price_need_to_pay_in_cent the amount that should be paid in cent.
		 * @param string $currency currency code of invoice.
		 */
		public function save_invoicing_info( $price_need_to_pay_in_cent, $currency ) {
			$this->save_meta(
				[
					'price_in_cent' => $price_need_to_pay_in_cent,
					'currency'      => $currency,
				]
			);
		}

		/**
		 * Get invoice information.
		 *
		 * @return array|bool|mixed
		 */
		public function get_invoicing_info() {
			return $this->get_meta( [ 'pricing_id', 'price_in_cent', 'currency' ] );
		}

		/**
		 * Get event id information.
		 *
		 * @return array|bool|mixed
		 */
		public function get_event_info() {
			return $this->get_meta( 'event_id' );
		}

		/**
		 * Update the registration status after confirming the transfer.
		 *
		 * @param int   $bank_account_number the index number of bank account array.
		 * @param array $bank_accounts list of available bank accounts.
		 */
		public function update_confirmation( $bank_account_number, $bank_accounts ) {

			// Save registrant id into variable.
			$registrant_id = $this->post_id;

			// Prepare some variables.
			$date_update           = current_time( 'timestamp' );
			$selected_bank_account = ! empty( $bank_accounts[ $bank_account_number ] ) ? $bank_accounts[ $bank_account_number ] : false;

			// Validate the selected bank accounts.
			if ( $selected_bank_account ) {

				/**
				 * Perform actions before confirming payment
				 *
				 * @param string $registrant_id the registrant id.
				 */
				do_action( 'wacara_before_confirming_payment', $registrant_id );

				// Update the status.
				$this->success = true;
				$this->set_registration_status( 'wait_verification' );

				// Update the status.
				$this->save_meta(
					[
						'confirmation_timestamp' => $date_update,
						'selected_bank_account'  => $selected_bank_account,
					]
				);

				/**
				 * Perform actions after confirming payment.
				 *
				 * @param string $registrant_id the registrant id.
				 */
				do_action( 'wacara_after_confirming_payment', $registrant_id );

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
			$old_price_in_cent                    = $this->get_meta( 'price_in_cent' );
			$new_price_with_unique_number_in_cent = $old_price_in_cent + $unique_number;
			$this->save_meta(
				[
					'maybe_unique_number'             => $unique_number,
					'maybe_price_in_cent_with_unique' => $new_price_with_unique_number_in_cent,
				]
			);
		}

		/**
		 * Get registration status.
		 *
		 * @return array|bool|mixed
		 */
		public function get_registration_status() {
			return $this->get_meta( 'reg_status' );
		}

		/**
		 * Get payment method id.
		 *
		 * @return array|bool|mixed
		 */
		public function get_payment_method_id() {
			return $this->get_meta( 'payment_method' );
		}

		/**
		 * Get manual payment information.
		 *
		 * @return array
		 */
		public function get_manual_payment_info_status() {
			$result         = [ 'reg_status' => $this->get_registration_status() ];
			$payment_method = $this->get_payment_method_id();

			// Parse the selected payment method.
			if ( 'manual' === $payment_method ) {

				// Prepare the variable.
				$confirmation_timestamp = $this->get_meta( 'confirmation_timestamp' );

				$more_fields = [
					'selected_bank_account'           => $this->get_meta( 'selected_bank_account' ),
					'currency'                        => $this->get_meta( 'currency' ),
					'maybe_unique_number'             => $this->get_meta( 'maybe_unique_number' ),
					'maybe_price_in_cent_with_unique' => $this->get_meta( 'maybe_price_in_cent_with_unique' ),
					'confirmation_timestamp'          => $confirmation_timestamp,
					'confirmation_date_time'          => Helper::convert_date( $confirmation_timestamp, true, true ),
					'payment_method'                  => $payment_method,
				];

				// Merge the result.
				$result = array_merge( $result, $more_fields );
			}

			return $result;
		}

		/**
		 * Find registrant by their booking code.
		 *
		 * @param string $booking_code booking code.
		 *
		 * @return Result
		 */
		public static function find_registrant_by_booking_code( $booking_code ) {
			return Helper::get_post_id_by_meta_key( 'booking_code', $booking_code, 'registrant' );
		}
	}
}
