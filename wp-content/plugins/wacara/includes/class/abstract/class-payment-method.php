<?php
/**
 * The parent class for payment method.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Payment_Method' ) ) {

	/**
	 * Class Payment_Method
	 *
	 * @package Wacara
	 */
	abstract class Payment_Method {

		/**
		 * Payment method's id variable.
		 *
		 * @var string
		 */
		public $id = '';

		/**
		 * Payment method's name variable.
		 *
		 * @var string
		 */
		public $name = '';

		/**
		 * Payment method's description variable.
		 *
		 * @var string
		 */
		public $description = '';

		/**
		 * Payment method's automatic variable.
		 *
		 * @var bool
		 */
		public $automatic = true;

		/**
		 * Payment method's availability variable.
		 *
		 * @var bool
		 */
		public $enable = true;

		/**
		 * Function to render the payment in checkout page.
		 */
		abstract public function render();

		/**
		 * Function to calculate and process the payment.
		 *
		 * @param Registrant $registrant the registrant object of registered registrant.
		 * @param array       $fields used fields which is stored from front-end, mostly it contains unserialized object.
		 * @param int         $pricing_price amount of invoice in cent.
		 * @param string      $pricing_currency the currency code of invoice.
		 *
		 * @return Result
		 */
		abstract public function process( $registrant, $fields, $pricing_price, $pricing_currency );

		/**
		 * Get cmb2 fields that will be translated into option page.
		 *
		 * @return array
		 */
		abstract public function admin_setting();

		/**
		 * Get content that will be rendered after making manual payment.
		 *
		 * @param Registrant $registrant the registrant object of registered registrant.
		 * @param string      $reg_status current registration status of the registrant.
		 * @param string      $pricing_id the id of selected pricing.
		 * @param int         $pricing_price amount of invoice in cent.
		 * @param string      $pricing_currency the currency code of invoice.
		 *
		 * @return string
		 */
		abstract public function maybe_page_after_payment( $registrant, $reg_status, $pricing_id, $pricing_price, $pricing_currency);

		/**
		 * Get admin settings from db.
		 *
		 * @param string $key specific key to filter the admin setting.
		 *
		 * @return bool|mixed|void
		 */
		public function get_admin_setting( $key = '' ) {
			$payment_options = Options::get_the_options( $this->id );
			$result          = $payment_options;

			// Filter the result by key.
			if ( $key ) {
				$result = ! empty( $payment_options[ $key ] ) ? $payment_options[ $key ] : false;
			}

			return $result;
		}

		/**
		 * Get content for displaying success page.
		 *
		 * @return string
		 */
		protected function get_success_page() {
			return Template::render( 'registrant/register-success' ); // phpcs:ignore
		}
	}
}
