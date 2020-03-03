<?php
/**
 * Main class file for offline payment.
 *
 * @author WPerfekt
 * @package Wacara\Payment
 * @version 0.0.1
 */

namespace Wacara\Payment;

use Wacara\Event;
use Wacara\Helper;
use Wacara\Payment_Method;
use Wacara\Pricing;
use Wacara\Registrant;
use Wacara\Registrant_Status;
use Wacara\Result;
use Wacara\Template;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'Wacara\Payment\Offline_Payment' ) ) {

	/**
	 * Class Offline_Payment
	 *
	 * @package Wacara\Payment
	 */
	class Offline_Payment extends Payment_Method {

		/**
		 * Instance variable.
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Singleton.
		 *
		 * @return Offline_Payment|null
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Offline_Payment constructor.
		 */
		protected function __construct() {
			$this->id              = 'offline-payment';
			$this->name            = __( 'Offline Payment', 'wacara' );
			$this->description     = __( 'Manual payment using bank transfer', 'wacara' );
			$this->custom_checkout = false;
			$this->enable          = true;
			$this->path            = __FILE__;

			parent::__construct();

			$this->hooks();
		}

		/**
		 * Render the offline payment in front-end.
		 */
		public function render() {
			?>
			<div class="wcr-alert wcr-alert-info">
				<?php
				/* translators: $s: message for offline payment */
				echo sprintf( '<p>%s</p>', __( 'Bank detail will be informed after checking out', 'wacara' ) ); // phpcs:ignore
				?>
			</div>
			<?php
		}

		/**
		 * Function to calculate and process the payment.
		 *
		 * @param Registrant $registrant the registrant object of registered registrant.
		 * @param array      $fields used fields which is stored from front-end, mostly it contains unserialized object.
		 * @param int        $pricing_price_in_cent amount of invoice in cent.
		 * @param string     $pricing_currency the currency code of invoice.
		 *
		 * @return Result
		 */
		public function process( $registrant, $fields, $pricing_price_in_cent, $pricing_currency ) {
			$result      = new Result();
			$settings    = $this->get_admin_setting();
			$unique_code = $settings['unique_code'];

			// Set default unique number.
			$unique = 0;

			// Check maybe requires unique code.
			if ( 'on' === $unique_code ) {

				// Set default unique number range to maximal 100 cent.
				$unique = wp_rand( 0, 100 );

				// Determine the amount of unique number.
				// If the pricing price is greater than 1000000 it's probably weak currency such a Rupiah which does not use cent.
				// So we will multiple the unique number by 100.
				if ( 1000000 < $pricing_price_in_cent ) {
					$unique *= 100;
				}
			}

			// Save the unique number.
			$registrant->maybe_save_unique_number( $unique );

			// There is nothing to do here, just finish the process and wait for the payment :).
			$result->success  = true;
			$result->callback = 'waiting-payment';

			return $result;
		}

		/**
		 * Admin settings.
		 *
		 * @inheritDoc
		 * @return array
		 */
		public function admin_setting() {
			return [
				[
					'name' => __( 'Unique code', 'wacara' ),
					'id'   => 'unique_code',
					'type' => 'checkbox',
					'desc' => __( 'Enable unique code?', 'wacara' ),
				],
				[
					'id'      => 'bank_accounts',
					'type'    => 'group',
					'options' => [
						'group_title'   => __( 'Bank {#}', 'wacara' ),
						'add_button'    => __( 'Add Bank', 'wacara' ),
						'remove_button' => __( 'Remove Bank', 'wacara' ),
						'sortable'      => false,
					],
					'fields'  => [
						[
							'name' => __( 'Bank Name', 'wacara' ),
							'id'   => 'name',
							'type' => 'text',
						],
						[
							'name' => __( 'Number', 'wacara' ),
							'id'   => 'number',
							'type' => 'text',
						],
						[
							'name' => __( 'Branch', 'wacara' ),
							'id'   => 'branch',
							'type' => 'text',
						],
						[
							'name' => __( 'Holder', 'wacara' ),
							'id'   => 'holder',
							'type' => 'text',
						],
					],
				],
			];
		}

		/**
		 * Map js files that will be loaded in front-end.
		 *
		 * @return array
		 */
		public function front_js() {
			return [
				'offline-payment' => [
					'url' => WCR_OP_URI . '/js/offline-payment.js',
				],
			];
		}

		/**
		 * Map css files that will be loaded in front-end.
		 *
		 * @return array
		 */
		public function front_css() {
			return [
				'offline-payment' => [
					'url' => WCR_OP_URI . '/css/offline-payment.css',
				],
			];
		}

		/**
		 * Map js files that will be loaded in back-end.
		 *
		 * @return array
		 */
		public function admin_js() {
			return [
				'offline-payment' => [
					'url' => WCR_OP_URI . '/js/admin-offline-payment.js',
				],
			];
		}

		/**
		 * Map css files that will be loaded in back-end.
		 *
		 * @return array
		 */
		public function admin_css() {
			return [
				'offline-payment' => [
					'url' => WCR_OP_URI . '/css/admin-offline-payment.css',
				],
			];
		}

		/**
		 * Map custom ajax endpoints.
		 *
		 * @return array
		 */
		public function ajax_endpoints() {
			return [
				'confirm'        => [
					'callback' => [ $this, 'confirmation_callback' ],
				],
				'payment_status' => [
					'callback' => [ $this, 'check_payment_status_callback' ],
					'public'   => false,
				],
				'verify_payment' => [
					'callback' => [ $this, 'check_verify_payment_callback' ],
					'public'   => false,
				],
			];
		}

		/**
		 * Register custom hook.
		 */
		private function hooks() {
			add_filter( 'wacara_filter_form_registrant_submit_label', [ $this, 'custom_button_label_callback' ], 10, 4 );
			add_filter( 'wacara_filter_registrant_custom_content_args', [ $this, 'custom_args_callback' ], 10, 4 );
			add_filter( 'wacara_filter_event_csv_columns', [ $this, 'custom_csv_columns_callback' ], 10, 2 );
			add_filter( 'wacara_filter_registrant_more_details', [ $this, 'registrant_more_details_callback' ], 10, 2 );
			add_filter( 'wacara_filter_registrant_admin_columns', [ $this, 'registrant_admin_columns_callback' ], 10, 1 );
			add_action( 'wacara_registrant_admin_column_action_content', [ $this, 'registrant_admin_column_action_callback' ], 10, 2 );

			Registrant_Status::register_new_status( 'waiting-payment', __( 'Waiting payment', 'wacara' ) );
			Registrant_Status::register_new_status( 'waiting-verification', __( 'Waiting verification', 'wacara' ) );
			Registrant_Status::register_new_status( 'reject', __( 'Rejected', 'wacara' ) );
		}

		/**
		 * Get bank accounts information.
		 *
		 * @return bool|mixed|void
		 */
		private function get_bank_accounts() {
			return $this->get_admin_setting( 'bank_accounts' );
		}

		/**
		 * Callback for making payment confirmation with manual bank transfer.
		 */
		public function confirmation_callback() {
			$result          = new Result();
			$data            = Helper::post( 'data' );
			$unserialize_obj = maybe_unserialize( $data );
			$registrant_id   = Helper::get_serialized_val( $unserialize_obj, 'registrant_id' );
			$bank_account    = Helper::get_serialized_val( $unserialize_obj, 'selected_bank' );
			$nonce           = Helper::get_serialized_val( $unserialize_obj, '_wpnonce' );

			// Validate the inputs.
			if ( $registrant_id && isset( $bank_account ) ) {

				// Validate the nonce.
				if ( wp_verify_nonce( $nonce, 'wacara_nonce' ) ) {

					// Instance the registrant.
					$registrant = new Registrant( $registrant_id );

					// Process the payment confirmation.
					$confirm = $this->update_confirmation( $registrant, $bank_account );

					// Prepare log content variable.
					$log_content = __( 'Successfully confirmed transfer', 'wacara' );

					// Check the success status.
					if ( $confirm->success ) {

						// Update the result.
						$result->success  = true;
						$result->callback = $registrant->get_registrant_url();
					} else {

						// Change log content.
						$log_content = $confirm->message;

						// Update the result.
						$result->message = $confirm->message;
					}

					// Save log.
					$registrant->add_logs( $log_content );
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
		 * Callback for checking payment status.
		 */
		public function check_payment_status_callback() {
			$registrant_id = Helper::get( 'id' );
			$output        = __( 'Please try again later', 'wacara' );

			// Validate the inputs.
			if ( $registrant_id ) {

				// Instance registrant object.
				$registrant = new Registrant( $registrant_id );

				// Validate the registrant object.
				if ( $registrant->success ) {

					// Get registration status.
					$reg_status = $registrant->get_registration_status();

					// Validate registration status.
					if ( 'waiting-verification' === $reg_status ) {

						$confirmation_timestamp                           = Helper::get_post_meta( 'confirmation_timestamp', $registrant->post_id );
						$template_args['id']                              = $registrant->post_id;
						$template_args['currency']                        = $registrant->get_pricing_currency();
						$template_args['currency_symbol']                 = Helper::get_currency_symbol_by_code( $template_args['currency'] );
						$template_args['confirmation_date_time']          = Helper::convert_date( $confirmation_timestamp, true, true );
						$template_args['maybe_price_in_cent_with_unique'] = $this->maybe_get_price_in_cent_with_unique( $registrant );
						$template_args['selected_bank_account']           = $this->get_selected_bank_account( $registrant );

						// Override the template first.
						Template::override_folder( $this->path );

						// Update the output result.
						$output = Template::render( 'admin/registrant-detail', $template_args );

						// Restore folder.
						Template::reset_folder();

					} else {
						$output = __( 'Invalid registrant', 'wacara' );
					}
				} else {
					$output = $registrant->message;
				}
			}

			echo $output; // phpcs:ignore
			die( 200 );
		}

		/**
		 * Callback for performing payment action.
		 */
		public function check_verify_payment_callback() {
			$result        = new Result();
			$data          = Helper::post( 'data' );
			$registrant_id = Helper::array_val( $data, 'id' );
			$new_status    = Helper::array_val( $data, 'status' );

			// Validate the inputs.
			if ( $registrant_id && $new_status ) {

				// Instance registrant object.
				$registrant = new Registrant( $registrant_id );

				// Validate the registrant object.
				if ( $registrant->success ) {

					// Validate the new status output.
					$message_output = __( 'Successfully verified', 'wacara' );
					if ( 'done' !== $new_status ) {
						$new_status     = 'reject';
						$message_output = __( 'Successfully rejected', 'wacara' );
					}

					// Update registration status.
					Registrant_Status::set_registrant_status( $registrant, $new_status );

					// Save logs.
					$registrant->add_logs( $message_output );

					// Validate the status.
					$result->success = true;
					$result->message = $message_output;

				} else {
					$result->message = $registrant->message;
				}
			} else {
				$result->message = __( 'Please try again later', 'wacara' );
			}

			wp_send_json( $result );
		}

		/**
		 * Callback for modifying submit button label.
		 *
		 * @param string                    $submit_label current submit label.
		 * @param Registrant                $registrant object of the current registrant.
		 * @param Payment_Method|bool|mixed $payment_class object of the selected payment method.
		 * @param string                    $reg_status status of the current registrant.
		 *
		 * @return string|void
		 */
		public function custom_button_label_callback( $submit_label, $registrant, $payment_class, $reg_status ) {
			switch ( $reg_status ) {
				case 'waiting-payment':
					$result = __( 'I have made a payment', 'wacara' );
					break;
				case 'waiting-verification':
				case 'reject':
					// Clean the submit button label, since we want to hide it.
					$result = '';
					break;
				default:
					$result = $submit_label;
					break;
			}

			return $result;
		}

		/**
		 * Callback for modifying args for registrant custom content.
		 *
		 * @param array                     $temp_args default args.
		 * @param string                    $reg_status status of the current registrant.
		 * @param Registrant                $registrant object of the current registrant.
		 * @param Payment_Method|bool|mixed $payment_class object of the selected payment method.
		 *
		 * @return array
		 */
		public function custom_args_callback( $temp_args, $reg_status, $registrant, $payment_class ) {
			switch ( $reg_status ) {
				case 'waiting-payment':
					// Fetch invoice info of the registrant.
					$price_in_cent_with_unique = $this->maybe_get_price_in_cent_with_unique( $registrant );
					$pricing_currency          = $registrant->get_pricing_currency();

					// Fetch bank accounts from settings.
					$bank_accounts = $this->get_bank_accounts();

					// Add new element to the default array.
					$new_args = [
						'bank_accounts'   => $bank_accounts,
						'currency_code'   => $pricing_currency,
						'currency_symbol' => Helper::get_currency_symbol_by_code( $pricing_currency ),
						'amount'          => number_format_i18n( $price_in_cent_with_unique / 100, 2 ),
					];

					// Merge the array.
					$temp_args = array_merge( $temp_args, $new_args );
					break;
				case 'waiting-verification':
					$temp_args['alert_message'] = __( 'We are verifying your payment, we\'ll get back to you once we have an update', 'wacara' );
					break;
				case 'reject':
					$temp_args['alert_message'] = __( 'We have declined your payment, if you think this is not supposed to be, please contact us.', 'wacara' );
					break;
			}

			return $temp_args;
		}

		/**
		 * Callback for altering csv columns.
		 *
		 * @param array $csv_columns current csv columns.
		 * @param Event $event object of the current event.
		 *
		 * @return array
		 */
		public function custom_csv_columns_callback( $csv_columns, $event ) {
			$csv_columns['action'] = __( 'Action', 'wacara' );

			return $csv_columns;
		}

		/**
		 * Callback for altering registrant more details.
		 *
		 * @param array      $more_details current extra details.
		 * @param Registrant $registrant object of the current registrant.
		 *
		 * @return array
		 */
		public function registrant_more_details_callback( $more_details, $registrant ) {
			$reg_status             = $registrant->get_registration_status();
			$action_content         = 'waiting-verification' === $reg_status ? '<a href="#" class="registrant_action" data-id="' . $registrant->post_id . '">[?]</a>' : '-';
			$more_details['action'] = $action_content;

			return $more_details;
		}

		/**
		 * Callback for adding more registrant admin columns.
		 *
		 * @param array $columns default columns.
		 *
		 * @return array
		 */
		public function registrant_admin_columns_callback( $columns ) {

			// Add columns.
			$columns['action'] = __( 'Action', 'wacara' );

			return $columns;
		}

		/**
		 * Callback for adding content to action column in registrant admin.
		 *
		 * @param Registrant $registrant object of the current registrant.
		 * @param Event      $event object of the current registrant's event.
		 */
		public function registrant_admin_column_action_callback( $registrant, $event ) {
			add_thickbox();
			$reg_status = $registrant->get_registration_status();

			// Only add action button on specific status.
			if ( 'waiting-verification' === $reg_status ) {
				?>
				<button class="button dashicons-before dashicons-warning registrant_action" data-id="<?php echo esc_attr( $registrant->post_id ); ?>"></button>
				<?php
			}
		}

		/**
		 * Update the registration status after confirming the transfer.
		 *
		 * @param Registrant $registrant object of the current registrant.
		 * @param int        $bank_account_number the index number of bank account array.
		 *
		 * @return Result
		 */
		private function update_confirmation( $registrant, $bank_account_number ) {
			$result = new Result();

			// Prepare some variables.
			$date_update           = current_time( 'timestamp' );
			$bank_accounts         = $this->get_bank_accounts();
			$selected_bank_account = ! empty( $bank_accounts[ $bank_account_number ] ) ? $bank_accounts[ $bank_account_number ] : false;

			// Validate the selected bank accounts.
			if ( $selected_bank_account ) {

				// Update the meta.
				Helper::save_post_meta(
					$registrant->post_id,
					[
						'confirmation_timestamp' => $date_update,
						'selected_bank_account'  => $selected_bank_account,
					]
				);

				// Update registration status.
				Registrant_Status::set_registrant_status( $registrant, 'waiting-verification' );

				// Update result.
				$result->success = true;

			} else {
				$result->message = __( 'Invalid bank account selected', 'wacara' );
			}

			return $result;
		}

		/**
		 * Maybe get price in cent with unique key.
		 *
		 * @param Registrant $registrant object of the registrant.
		 *
		 * @return array|bool|mixed
		 */
		private function maybe_get_price_in_cent_with_unique( $registrant ) {
			return Helper::get_post_meta( 'maybe_price_in_cent_with_unique', $registrant->post_id );
		}

		/**
		 * Get registrant selected bank account.
		 *
		 * @param Registrant $registrant object of the current registrant.
		 *
		 * @return array|bool|mixed
		 */
		private function get_selected_bank_account( $registrant ) {
			return Helper::get_post_meta( 'selected_bank_account', $registrant->post_id );
		}
	}

	Offline_Payment::init();
}
