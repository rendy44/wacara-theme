<?php
/**
 * Class to manage offline payment.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton\Payment;

use Skeleton\Options;
use Skeleton\Payment_Method;
use Skeleton\Register_Payment;
use Skeleton\Result;
use Skeleton\Helper;
use Skeleton\Template;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Skeleton\Payment\Offline_Payment' ) ) {

	/**
	 * Class Offline_Payment
	 *
	 * @package Skeleton\Payment
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
		 * Processing payment method.
		 *
		 * @param string $participant_id the registered participant id.
		 * @param string $pricing_id the pricing id.
		 * @param array  $fields data from front-end.
		 *
		 * @return Result
		 */
		public function process( $participant_id, $pricing_id, $fields ) {
			$result      = new Result();
			$settings    = $this->get_admin_setting();
			$unique_code = $settings['unique_code'];

			// Fetch the details from db.
			$pricing_price = Helper::get_post_meta( 'price', $pricing_id );

			// Set default unique number.
			$unique = 0;

			// Check maybe requires unique code.
			if ( 'on' === $unique_code ) {

				// Set default unique number range to maximal 100 cent.
				$unique = wp_rand( 0, 100 );

				// Determine the amount of unique number.
				// If the pricing price is greater than 10000 it's probably weak currency such a Rupiah which does not use cent.
				// So we will multiple the unique number by 100.
				if ( 10000 < $pricing_price ) {
					$unique *= 100;
				}
			}

			// Save the unique number.
			$old_price_in_cent                    = Helper::get_post_meta( 'price_in_cent', $participant_id );
			$new_price_with_unique_number_in_cent = $old_price_in_cent + $unique;
			Helper::save_post_meta(
				$participant_id,
				[
					'maybe_unique_number'             => $unique,
					'maybe_price_in_cent_with_unique' => $new_price_with_unique_number_in_cent,
				]
			);

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

		public function maybe_page_after_payment( $participant_id, $reg_status, $pricing_id, $event_id ) {

			// Prepare default content after registration as success page.
			$content = $this->get_success_page();

			// Only change the content for manual payment.
			if ( ! $this->automatic ) {

				// Prepare the templating args.
				$register_args = [
					'id'         => $participant_id,
					'title'      => get_the_title( $participant_id ),
					'pricing_id' => $pricing_id,
					'event_id'   => $event_id,
				];
				switch ( $reg_status ) {
					case 'wait_payment':
						$bank_accounts                  = $this->get_bank_accounts();
						$amount_cent                    = Helper::get_post_meta( 'maybe_price_in_cent_with_unique', $participant_id );
						$amount_fixed                   = $amount_cent / 100;
						$amount_formatted               = number_format_i18n( $amount_fixed, 2 );
						$register_args['bank_accounts'] = $bank_accounts;
						$register_args['currency_code'] = Helper::get_post_meta( 'currency', $participant_id );
						$register_args['amount']        = $amount_formatted;
						$template                       = 'waiting-payment';
						break;
					case 'wait_verification':
						$template = 'waiting-verification';
						break;
					case 'fail':
					default:
						$validate_pricing                      = Helper::is_pricing_valid( $register_args['pricing_id'], true );
						$register_args['use_payment']          = $validate_pricing->success;
						$register_args['stripe_error_message'] = Helper::get_post_meta( 'stripe_error_message', $participant_id );
						$register_args['payment_methods']      = Register_Payment::get_registered();
						$template                              = 'register-form';
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
