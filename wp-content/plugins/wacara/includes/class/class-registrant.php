<?php
/**
 * Use this class to manage registrants.
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.2
 */

namespace Wacara;

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
		private $registrant_data = array();

		/**
		 * Registrant constructor.
		 *
		 * @param bool  $registrant_id leave it empty to create a new registrant,
		 *              and assign with registrant id to fetch the registrant's detail.
		 * @param array $args arguments to create a new registrant.
		 *              Or list of field to displaying registrant.
		 *
		 * @version 0.0.2
		 */
		public function __construct( $registrant_id = false, $args = array() ) {

			// Create a new registrant.
			if ( ! $registrant_id ) {

				// Prepare default args.
				$default_args = array(
					'event_id'   => false,
					'pricing_id' => false,
				);

				// Parse the arguments.
				$args = wp_parse_args( $args, $default_args );

				// Validate inputs.
				if ( $args['event_id'] && $args['pricing_id'] ) {

					// Save details into variable.
					$event_id = $args['event_id'];

					// Generate unique key based on timestamp and random string..
					$registrant_key = current_time( 'timestamp' ) . wp_generate_password( 6, false );

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
						array(
							'post_type'   => 'registrant',
							'post_title'  => strtoupper( $registrant_key ),
							'post_name'   => sanitize_title( $registrant_key ),
							'post_status' => 'publish',
						)
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

						// Update class object.
						$this->success         = true;
						$this->post_id         = $new_registrant;
						$this->post_url        = get_permalink( $new_registrant );
						$this->registrant_data = $args;

						// Create registrant booking code.
						$this->save_registrant_booking_code( $event_id, $new_registrant );

						// Create qrcode for registrant.
						$this->save_qrcode_to_registrant();

						// Create registrant key.
						$this->save_registrant_key();

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
					$used_args = array(
						'booking_code',
						'email',
						'name',
						'booking_code',
						'event_id',
						'pricing_id',
					);
					if ( ! empty( $args ) ) {
						$used_args = array_merge( $used_args, $args );
					}

					// Fetch registrant detail.
					$this->registrant_data = $this->get_meta( $used_args );

					// Add more fields to be displayed.
					$more_data = array(
						'reg_status'          => $this->get_registration_status(),
						'readable_reg_status' => $this->get_readable_registrant_status(),
					);

					/**
					 * Wacara registrant more detail filter hooks.
					 *
					 * @param array $more_data current extra details.
					 * @param Registrant $this object of the current registrant.
					 */
					$more_data = apply_filters( 'wacara_filter_registrant_more_details', $more_data, $this );

					// Save status into object.
					$this->registrant_data = array_merge( $this->registrant_data, $more_data );

				}
			}
		}

		/**
		 * Save qrcode image locally.
		 *
		 * @return string
		 * @version 0.0.2
		 */
		private function generate_qrcode_locally() {
			$booking_code = $this->get_booking_code();
			$file_name    = "/assets/qrcode/{$booking_code}.png";
			$file_path    = WACARA_PATH . $file_name;

			// QRcode::png( $booking_code, $file_path, QR_ECLEVEL_H, 5 );

			return $file_name;
		}

		/**
		 * Save qrcode information into registrant.
		 */
		private function save_qrcode_to_registrant() {

			// Generate qrcode locally.
			$created_qrcode = $this->generate_qrcode_locally();

			// Save qrcode data.
			$qrcode_uri = WACARA_URI . $created_qrcode;

			// Save qrcode into registrant.
			$this->save_meta(
				array(
					'qrcode_name' => $created_qrcode,
					'qrcode_url'  => $qrcode_uri,
				)
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
		 * Save registrant booking code.
		 *
		 * @param string $event_id id of the selected event.
		 * @param int    $registrant_id id of the newly created registrant.
		 */
		private function save_registrant_booking_code( $event_id, $registrant_id ) {

			// Create registrant booking code.
			$event_and_id_length = strlen( $registrant_id );
			$unique_length       = 8 - $event_and_id_length;
			$booking_code        = strtoupper( wp_generate_password( $unique_length, false ) . $registrant_id );

			/**
			 * Perform filter to modify registrant publishable key.
			 *
			 * @param string $booking_code default booking code.
			 * @param string $event_id id of the selected event.
			 * @param int $registrant_id id of the newly created registrant.
			 */
			$booking_code = apply_filters( 'wacara_filter_registrant_booking_code', $booking_code, $event_id, $registrant_id );

			$this->save_meta(
				array(
					'booking_code' => $booking_code,
				)
			);
		}

		/**
		 * Save registrant public and secret key.
		 */
		private function save_registrant_key() {

			// Create a public key based on registrant booking code.
			$public_key = Helper::encryption( $this->get_booking_code() );

			// Create a secret key based on registrant id.
			$secret_key = Helper::encryption( $this->post_id );

			$this->save_meta(
				array(
					'secret_key' => $secret_key,
					'public_key' => $public_key,
				)
			);
		}

		/**
		 * Get registrant public key.
		 *
		 * @return array|bool|mixed
		 */
		public function get_public_key() {
			return $this->get_meta( 'public_key' );
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
		 */
		public function save_more_details( $name = '', $email = '' ) {
			$this->save_meta(
				array(
					'name'  => $name,
					'email' => $email,
				)
			);
		}

		/**
		 * Get registrant name.
		 *
		 * @return array|bool|mixed
		 */
		public function get_registrant_name() {
			return $this->get_meta( 'name' );
		}

		/**
		 * Get registrant email.
		 *
		 * @param bool $censor whether display email in censored or not.
		 *
		 * @return array|bool|mixed
		 */
		public function get_registrant_email( $censor = false ) {
			$email = $this->get_meta( 'email' );

			// Maybe censor.
			if ( $censor ) {
				$email = Helper::censor_email( $email );
			}

			return $email;
		}

		/**
		 * Maybe perform checkin.
		 */
		public function maybe_do_checkin() {

			// Validate registrant status.
			if ( 'done' === $this->get_registration_status() ) {

				// Check is in checkin period.
				if ( $this->get_event_object( true )->is_in_checkin_period() ) {

					// Check whether registrant already checkin today.
					if ( ! $this->is_today_checked_in() ) {

						/**
						 * Perform action before registrant checkin.
						 *
						 * @param Registrant $registrant object of the selected registrant.
						 */
						do_action( 'wacara_before_registrant_checkin', $this );

						// Finally, do the checkin.
						$this->do_checkin();

						// Update the result.
						$this->success = true;

						/**
						 * Perform action after registrant checkin.
						 *
						 * @param Registrant $registrant object of the current registrant.
						 */
						do_action( 'wacara_after_registrant_checkin', $this );

					} else {
						$this->success  = false;
						$this->message  = __( 'You have already checked in for today', 'wacara' );
						$this->callback = array( Helper::get_today_timestamp(), $this->get_checkin_lists() );
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
			$this->save_meta( array( 'checkin_dates' => $previous_checkin ) );
		}

		/**
		 * Check whether the registrant already checkin for today ot not.
		 *
		 * @return bool
		 */
		private function is_today_checked_in() {
			$today_timestamp = Helper::get_today_timestamp();
			$checkin_dates   = $this->get_checkin_lists();

			return in_array( $today_timestamp, $checkin_dates, true );
		}

		/**
		 * Get registrant data.
		 *
		 * @param string $field specific field of the data.
		 *
		 * @return array|bool|mixed
		 */
		public function get_data( $field = '' ) {
			$result = $this->registrant_data;

			// Maybe filter.
			if ( $field ) {
				$result = Helper::array_val( $result, $field );
			}

			return $result;
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
		 * Get registrant invoice id.
		 *
		 * @return string
		 */
		public function get_invoice_id() {
			return $this->post_title;
		}

		/**
		 * Set registrant registration status.
		 *
		 * @param string $status status of registration.
		 */
		public function set_registration_status( $status ) {

			// Save old status into variable.
			$old_status = $this->get_registration_status();

			// Change the status.
			$this->save_meta( array( 'reg_status' => $status ) );

			/**
			 * Perform action when registrant status changed.
			 *
			 * @param Registrant $registrant object of the current registrant.
			 * @param string $status the new status of registrant.
			 * @param string $old_status the old status of registrant.
			 *
			 * @hooked Mailer_Event::send_email_each_status_changed_callback - 10
			 */
			do_action( 'wacara_after_setting_registrant_status', $this, $status, $old_status );
		}

		/**
		 * Save payment method information
		 *
		 * @param string $payment_method selected payment method.
		 */
		public function save_payment_method_info( $payment_method ) {
			$this->save_meta( array( 'payment_method' => $payment_method ) );
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
		 * Maybe instance payment method class.
		 *
		 * @return bool|mixed|Payment_Method
		 */
		public function get_payment_method_object() {

			// Retrieve payment method id.
			$payment_method_id = $this->get_payment_method_id();

			// Instance payment method class.
			return Register_Payment::get_payment_method_class( $payment_method_id );
		}

		/**
		 * Get event object.
		 *
		 * @param bool $get_detail whether get event details or not.
		 *
		 * @return Event
		 */
		public function get_event_object( $get_detail = false ) {
			return new Event( $this->get_meta( 'event_id' ), $get_detail );
		}

		/**
		 * Save registrant log.
		 *
		 * @param string $content log's details.
		 */
		public function add_logs( $content ) {

			// Retrieve time of the submitted log.
			$time = time();

			// Convert content into array.
			$log_content = array(
				'time'    => $time,
				'content' => $content,
			);

			$this->add_meta( 'logs', $log_content );
		}

		/**
		 * Get registrant logs.
		 *
		 * @return array|bool|mixed
		 */
		public function get_logs() {
			return $this->get_meta( 'logs', false );
		}

		/**
		 * Maybe save unique number for easier payment confirmation.
		 */
		public function maybe_save_unique_number() {

			// Make sure pricing is supporting unique number.
			if ( ! $this->is_pricing_unique_number() ) {
				return;
			}

			// Get old price in cent.
			$old_price_in_cent = $this->get_pricing_price_in_cent();

			// Set default unique number range to maximal 100 cent.
			$unique = wp_rand( 0, 100 );

			// Determine the amount of unique number.
			// If the pricing price is greater than 1000000 it's probably weak currency such a Rupiah which does not use cent.
			// So we will multiple the unique number by 100.
			if ( 1000000 < $old_price_in_cent ) {
				$unique *= 100;
			}

			// Calculate new price.
			$new_price_with_unique_number_in_cent = $old_price_in_cent + $unique;

			$this->save_meta(
				array(
					'maybe_unique_number'             => $unique,
					'maybe_price_with_unique'         => $new_price_with_unique_number_in_cent / 100,
					'maybe_price_with_unique_in_cent' => $new_price_with_unique_number_in_cent,
				)
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
		 * Get reabable registrant status.
		 *
		 * @param bool $html whether return as html or plain text.
		 *
		 * @return string
		 */
		public function get_readable_registrant_status( $html = false ) {
			$reg_status = $this->get_registration_status();
			$result     = Registrant_Status::get_status( $reg_status );

			// Maybe display in html.
			if ( $html && $result ) {
				/* translators: %1$s : plain registrant status, %2$s : readable registrant status */
				$result = sprintf( '<span class="wcr-label wcr-label-%1$s">%2$s</span>', $reg_status, $result );
			}

			return $result;
		}

		/**
		 * Get pricing id that already attached into registrant.
		 *
		 * @return array|bool|mixed
		 */
		public function get_pricing_id() {
			return $this->get_meta( 'pricing_id' );
		}

		/**
		 * Get pricing name that already attached into registrant.
		 *
		 * @return array|bool|mixed
		 */
		public function get_pricing_name() {
			return $this->get_meta( 'pricing_cache_name' );
		}

		/**
		 * Get pricing unique number status that already attached into registrant.
		 *
		 * @return array|bool|mixed
		 */
		public function is_pricing_unique_number() {
			return $this->get_meta( 'pricing_cache_unique_number' );
		}

		/**
		 * Get pricing currency code that already attached into registrant.
		 *
		 * @return array|bool|mixed
		 */
		public function get_pricing_currency() {
			return $this->get_meta( 'pricing_cache_currency' );
		}

		/**
		 * Get pricing price that already attached into registrant.
		 *
		 * @return array|bool|mixed
		 */
		public function get_pricing_price() {
			return $this->get_meta( 'pricing_cache_price' );
		}

		/**
		 * Get pricing price in cent that already attached into registrant.
		 *
		 * @return array|bool|mixed
		 */
		public function get_pricing_price_in_cent() {
			return $this->get_meta( 'pricing_cache_price_in_cent' );
		}

		/**
		 * Get pricing price in html that already attached into registrant.
		 *
		 * @return string
		 */
		public function get_pricing_price_in_html() {
			$currency_code   = $this->get_pricing_currency();
			$currency_symbol = Helper::get_currency_symbol_by_code( $currency_code );
			$price           = $this->get_pricing_price();

			/* translators: %1$s : currency symbol : %2$s : formatted amount */

			return sprintf( '<span class="wcr-amount"><span class="wcr-currency">%1$s</span><span class="wcr-value">%2$s</span></span>', $currency_symbol, number_format_i18n( $price, 2 ) );
		}

		/**
		 * Maybe get pricing unique number that already attached into registrant.
		 *
		 * @return array|bool|mixed
		 */
		public function get_pricing_unique_number() {
			return $this->get_meta( 'maybe_unique_number' );
		}

		/**
		 * Maybe get total pricing price include unique number that already attached into registrant.
		 *
		 * @return array|bool|mixed
		 */
		public function get_total_pricing_price() {
			$maybe_price_with_unique = $this->get_meta( 'maybe_price_with_unique' );

			return $maybe_price_with_unique ? $maybe_price_with_unique : $this->get_pricing_price();
		}

		/**
		 * Maybe get total pricing price include unique number in cent that already attached into registrant.
		 *
		 * @return array|bool|mixed
		 */
		public function get_total_pricing_price_in_cent() {
			return $this->get_meta( 'maybe_price_with_unique_in_cent' );
		}

		/**
		 * Get total pricing price include unique number in html that already attached into registrant.
		 *
		 * @return string
		 */
		public function get_total_pricing_in_html() {
			$currency_code   = $this->get_pricing_currency();
			$currency_symbol = Helper::get_currency_symbol_by_code( $currency_code );
			$price           = $this->get_total_pricing_price();

			/* translators: %1$s : currency symbol : %2$s : formatted amount */

			return sprintf( '<span class="wcr-amount"><span class="wcr-currency">%1$s</span><span class="wcr-value">%2$s</span></span>', $currency_symbol, number_format_i18n( $price, 2 ) );
		}

		/**
		 * Get pricing pros that already attached into registrant.
		 *
		 * @param bool $raw whether get result in raw array or convert it into string.
		 *
		 * @return array|bool|mixed|string
		 */
		public function get_pricing_pros( $raw = true ) {
			$result = $this->get_meta( 'pricing_cache_pros' );

			// Maybe convert into string.
			if ( ! $raw ) {
				$result = implode( ', ', $result );
			}

			return $result;
		}

		/**
		 * Get pricing cons that already attached into registrant.
		 *
		 * @param bool $raw whether get result in raw array or convert it into string.
		 *
		 * @return array|bool|mixed|string
		 */
		public function get_pricing_cons( $raw = true ) {
			$result = $this->get_meta( 'pricing_cache_cons' );

			// Maybe convert into string.
			if ( ! $raw ) {
				$result = implode( ', ', $result );
			}

			return $result;
		}

		/**
		 * Get admin highlight message.
		 *
		 * @return mixed|string|void
		 */
		public function get_admin_highlight() {

			// Fetch details.
			$reg_status     = $this->get_registration_status();
			$payment_method = $this->get_payment_method_object()->name;

			switch ( $reg_status ) {
				case 'done':
					/* translators: %s : name of the selected payment method */
					$highlight = sprintf( __( 'Registrant is completed with %s', 'wacara' ), $payment_method );
					break;
				case 'fail':
					/* translators: %s : name of the selected payment method */
					$highlight = sprintf( __( 'Registrant is failed with %s', 'wacara' ), $payment_method );
					break;
				case 'reject':
					/* translators: %s : name of the selected payment method */
					$highlight = sprintf( __( 'Registrant is rejected with %s', 'wacara' ), $payment_method );
					break;
				default:
					$highlight = __( 'Registrant has not completed yet', 'wacara' );
					break;
			}

			/**
			 * Wacara registrant admin highlight filter hook.
			 *
			 * @param string $highlight default highlight content.
			 * @param Registrant $registrant object of the current registrant.
			 * @param string $payment_method name of the selected payment method.
			 * @param string $reg_status status of the current register.
			 */
			$highlight = apply_filters( 'wacara_filter_registrant_admin_highlight', $highlight, $this, $payment_method, $reg_status );

			return $highlight;
		}

		/**
		 * Get the booking code.
		 *
		 * @return array|bool|mixed
		 */
		public function get_booking_code() {
			return $this->get_meta( 'booking_code' );
		}
	}
}
