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

if ( ! class_exists( 'Wacara\Event_Pricing' ) ) {

	/**
	 * Class Event_Pricing
	 *
	 * @package Wacara
	 */
	class Event_Pricing extends Post {

		/**
		 * Currency code variable.
		 *
		 * @var array|bool|mixed
		 */
		private $currency_code;

		/**
		 * Price variable.
		 *
		 * @var array|bool|mixed
		 */
		private $price;

		/**
		 * Pros variable.
		 *
		 * @var array|bool|mixed
		 */
		private $pros;

		/**
		 * Cons variable.
		 *
		 * @var array|bool|mixed
		 */
		private $cons;

		/**
		 * Is unique variable.
		 *
		 * @var array|bool|mixed
		 */
		private $is_unique;

		/**
		 * Is recommended variable.
		 *
		 * @var array|bool|mixed
		 */
		private $is_recommended;

		/**
		 * Event_Pricing constructor.
		 *
		 * @param string $pricing_id pricing id.
		 */
		public function __construct( $pricing_id ) {
			parent::__construct( $pricing_id, 'price' );

			// Fetch details.
			$this->currency_code  = $this->get_meta( 'currency' );
			$this->price          = $this->get_meta( 'price' );
			$this->pros           = $this->get_meta( 'pros' );
			$this->cons           = $this->get_meta( 'cons' );
			$this->is_unique      = $this->get_meta( 'unique_number' );
			$this->is_recommended = $this->get_meta( 'recommended' );
		}

		/**
		 * Get pricing currency code.
		 *
		 * @return array|bool|mixed
		 */
		public function get_currency_code() {
			return $this->currency_code;
		}

		/**
		 * Get pricing currency symbol.
		 *
		 * @return mixed|void
		 */
		public function get_currency_symbol() {
			return Helper::get_currency_symbol_by_code( $this->get_currency_code() );
		}

		/**
		 * Get pricing price.
		 *
		 * @return int
		 */
		public function get_price() {
			return (int) $this->price;
		}

		/**
		 * Get formatted html price.
		 *
		 * @return string
		 */
		public function get_html_price() {
			return $this->get_currency_symbol() . number_format_i18n( $this->get_price(), 2 );
		}

		/**
		 * Get pricing pros.
		 *
		 * @param bool $raw whether display the result as raw or array.
		 *
		 * @return array|bool|mixed
		 */
		public function get_pros( $raw = false ) {
			$result = $this->pros;

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
			$result = $this->cons;

			if ( ! $raw ) {
				$result = explode( ',', $result );
			}

			return $result;
		}

		/**
		 * Get status whether set as recommended or not.
		 *
		 * @return bool
		 */
		public function is_recommended() {
			return 'on' === $this->is_recommended;
		}

		/**
		 * Get status whether requires unique number or not.
		 *
		 * @return bool
		 */
		public function is_unique_number() {
			return 'on' === $this->is_unique;
		}
	}
}
