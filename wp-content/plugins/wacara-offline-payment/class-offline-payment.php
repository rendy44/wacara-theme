<?php
/**
 * Main class file for offline payment.
 *
 * @author Rendy
 * @package Wacara\Payment
 * @version 0.0.1
 */

namespace Wacara\Payment;

use Wacara\Payment_Method;
use Wacara\Register_Payment;
use Wacara\Registrant;
use Wacara\Result;

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
			$this->id          = 'offline-payment';
			$this->name        = __( 'Offline Payment', 'wacara' );
			$this->description = __( 'Offline payment method for Wacara', 'wacara' );
			$this->automatic   = false;
			$this->enable      = true;
			$this->path        = __FILE__;

			parent::__construct();

			$this->hooks();
		}

		/**
		 * Register custom hook.
		 */
		private function hooks() {
			add_filter( 'wacara_filter_form_registrant_submit_label', [ $this, 'custom_button_label_callback' ], 10, 4 );
			add_filter( 'wacara_filter_registrant_custom_content_args', [ $this, 'custom_args_callback' ], 10, 4 );
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
					$result = __( 'Oke', 'wacara' );
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
					$invoice_info = $registrant->get_invoicing_info();

					// Fetch bank accounts from settings.
					$bank_accounts = $this->get_bank_accounts();

					// Add new element to the default array.
					$new_args = [
						'bank_accounts' => $bank_accounts,
						'currency_code' => $invoice_info['currency'],
						'amount'        => $invoice_info['price_in_cent'],
					];

					// Merge the array.
					$temp_args = array_merge( $temp_args, $new_args );
					break;
			}

			return $temp_args;
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
		 * @param int        $pricing_price amount of invoice in cent.
		 * @param string     $pricing_currency the currency code of invoice.
		 *
		 * @return Result
		 */
		public function process( $registrant, $fields, $pricing_price, $pricing_currency ) {
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
				if ( 1000000 < $pricing_price ) {
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
			return [];

			return [
				'offline-payment' => [
					'url'     => plugin_dir_url( __FILE__ ) . '/js/offline-payment.js',
					'modules' => true,
				],
			];
		}

		/**
		 * Map css files that will be loaded in front-end.
		 *
		 * @return array
		 */
		public function front_css() {
			return [];
		}

		/**
		 * Get bank accounts information.
		 *
		 * @return bool|mixed|void
		 */
		private function get_bank_accounts() {
			return $this->get_admin_setting( 'bank_accounts' );
		}
	}

	Offline_Payment::init();
}
