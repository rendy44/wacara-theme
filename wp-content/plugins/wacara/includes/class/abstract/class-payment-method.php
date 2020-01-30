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
		 * Payment_Method constructor.
		 */
		protected function __construct() {
			// Register this payment method.
			Register_Payment::register( $this );

			$this->maybe_load_assets();
		}

		/**
		 * Function to render the payment in checkout page.
		 */
		abstract public function render();

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
		abstract public function process( $registrant, $fields, $pricing_price, $pricing_currency );

		/**
		 * Get cmb2 fields that will be translated into option page.
		 *
		 * @return array
		 */
		abstract public function admin_setting();

		/**
		 * Get array of js files that will be loaded in front-end.
		 *
		 * @return array
		 */
		abstract public function front_js();

		/**
		 * Get array of css files that will be loaded in front-end.
		 *
		 * @return array
		 */
		abstract public function front_css();

		/**
		 * Load assets in front-end.
		 */
		private function maybe_load_assets() {
			add_action( 'wp_enqueue_scripts', [ $this, 'maybe_load_assets_callback' ] );
		}

		/**
		 * Callback for loading assets.
		 */
		public function maybe_load_assets_callback() {
			global $post;
			$js_files  = $this->front_js();
			$css_files = $this->front_css();

			if ( 'registrant' === $post->post_type ) {
				foreach ( $js_files as $js_name => $js_obj ) {
					Helper::load_js( $js_name, $js_obj );
				}

				foreach ( $css_files as $css_name => $css_obj ) {
					Helper::load_css( $css_name, $css_obj );
				}
			}
		}

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
	}
}
