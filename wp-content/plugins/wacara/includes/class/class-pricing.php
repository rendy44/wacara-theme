<?php
/**
 * Class to manage the pricing.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Pricing' ) ) {

	/**
	 * Class Pricing
	 *
	 * @package Wacara
	 */
	class Pricing extends Post {

		/**
		 * Pricing constructor.
		 *
		 * @param string $pricing_id pricing id.
		 */
		public function __construct( $pricing_id ) {
			parent::__construct( $pricing_id, 'price' );

			// Self validate the pricing.
			$this->validate();
		}

		/**
		 * Get pricing currency code.
		 *
		 * @return array|bool|mixed
		 */
		public function get_currency_code() {
			return $this->get_meta( 'currency' );
		}

		/**
		 * Get pricing price.
		 *
		 * @return array|bool|mixed
		 */
		public function get_price() {
			return $this->get_meta( 'price' );
		}

		/**
		 * Get formatted html price.
		 *
		 * @return string
		 */
		public function get_html_price() {
			$price           = $this->get_price();
			$currency_code   = $this->get_currency_code();
			$currency_symbol = Helper::get_currency_symbol_by_code( $currency_code );

			return $currency_symbol . number_format_i18n( $price, 2 );
		}

		/**
		 * Get pricing pros.
		 *
		 * @param bool $raw whether display the result as raw or array.
		 *
		 * @return array|bool|mixed
		 */
		public function get_pros( $raw = false ) {
			$result = $this->get_meta( 'pros' );

			if ( ! $raw ) {
				$result = explode( ',', $result );
			}

			return $result;
		}

		/**
		 * Get pricing cons.
		 *
		 * @param bool $raw whether display the result as raw or array.
		 *
		 * @return array|bool|mixed
		 */
		public function get_cons( $raw = false ) {
			$result = $this->get_meta( 'cons' );

			if ( ! $raw ) {
				$result = explode( ',', $result );
			}

			return $result;
		}

		/**
		 * Get status whether set as recommended or not.
		 *
		 * @return array|bool|mixed
		 */
		public function is_recommended() {
			return $this->get_meta( 'recommended' );
		}

		/**
		 * Get status whether requires unique number or not.
		 *
		 * @return array|bool|mixed
		 */
		public function is_unique_number() {
			return $this->get_meta( 'unique_number' );
		}

		/**
		 * Validate the pricing.
		 */
		private function validate() {

			// Validate the price.
			$price = $this->get_price();
			if ( '' !== $price || 0 === $price ) {

				// Validate the currency.
				$currency = $this->get_currency_code();
				if ( $currency ) {
					$this->success = true;
				} else {
					$this->success = false;
					$this->message = __( 'The pricing is invalid, the currency has not been assigned yet', 'wacara' ) . $currency;
				}
			} else {
				$this->success = false;
				$this->message = __( 'The pricing is invalid, the price amount has not been assigned yet', 'wacara' );
			}
		}
	}
}
