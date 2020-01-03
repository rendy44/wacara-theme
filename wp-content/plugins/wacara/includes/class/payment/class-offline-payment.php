<?php
/**
 * Class to manage offline payment.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Wacara\Payment;

use Wacara\Participant;
use Wacara\Payment_Method;
use Wacara\Register_Payment;
use Wacara\Result;
use Wacara\Helper;
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
		private function __construct() {
			$this->id          = 'offline-payment';
			$this->name        = __( 'Offline Payment', 'wacara' );
			$this->description = __( 'Offline payment method for Wacara', 'wacara' );
			$this->automatic   = false;
			$this->enable      = true;

			// Do register the method.
			Register_Payment::register( $this );
		}

		/**
		 * Render the payment in front-end.
		 */
		public function render() {
			// translator: %s : message content.
			echo sprintf( '<div class="alert alert-info">%s</div>', esc_html__( 'Bank detail will be informed after making registration', 'wacara' ) ); // phpcs:ignore
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
			$participant->maybe_save_unique_number( $unique );

			// There is nothing to do here, just finish the process and wait for the payment :).
			$result->success  = true;
			$result->callback = 'wait_payment';

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

			// Prepare default content after registration as success page.
			$content = $this->get_success_page();

			// Only change the content for manual payment.
			if ( ! $this->automatic ) {

				// Prepare the templating args.
				$register_args = [
					'id'         => $participant->post_id,
					'title'      => $participant->post_title,
					'pricing_id' => $pricing_id,
					'event_id'   => $participant->get_event_info(),
				];

				// Switch the registration status.
				switch ( $reg_status ) {
					case 'wait_payment':
						$bank_accounts                  = $this->get_bank_accounts();
						$amount_fixed                   = $pricing_price / 100;
						$amount_formatted               = number_format_i18n( $amount_fixed, 2 );
						$register_args['bank_accounts'] = $bank_accounts;
						$register_args['currency_code'] = $pricing_currency;
						$register_args['amount']        = $amount_formatted;
						$template                       = 'waiting-payment';
						break;
					case 'wait_verification':
						$template = 'waiting-verification';
						break;
					case 'fail':
					default:
						$validate_pricing                 = Helper::is_pricing_valid( $register_args['pricing_id'], true );
						$register_args['use_payment']     = $validate_pricing->success;
						$register_args['payment_methods'] = Register_Payment::get_registered();
						$template                         = 'register-form';
						break;
				}

				// Update the content.
				$content = Template::render( 'participant/' . $template, $register_args ); // phpcs:ignore
			}

			return $content;
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

	// Instance the class.
	Offline_Payment::init();
}
