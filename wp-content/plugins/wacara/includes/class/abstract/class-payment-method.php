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
		 * Path of main file location.
		 *
		 * @var string
		 */
		public $path = '';

		/**
		 * Payment_Method constructor.
		 */
		protected function __construct() {
			// Register this payment method.
			Register_Payment::register( $this );

			$this->hooks();
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
		 * Get custom ajax endpoints.
		 *
		 * @return array
		 */
		abstract public function ajax_endpoints();

		/**
		 * Add custom hooks.
		 */
		private function hooks() {
			add_action( 'wp_enqueue_scripts', [ $this, 'maybe_load_assets_callback' ] );
			add_filter( 'wacara_ajax_endpoints', [ $this, 'custom_ajax_endpoints_callback' ] );
		}

		/**
		 * Callback for loading assets.
		 */
		public function maybe_load_assets_callback() {
			global $post;
			$js_prefix = WACARA_PREFIX . 'module_';
			$js_files  = $this->front_js();
			$css_files = $this->front_css();

			// Just in case the payment method doesn't have js file, use default file instead.
			if ( empty( $js_files ) ) {
				$js_files = $this->reserve_js_files();
			}

			if ( 'registrant' === $post->post_type ) {
				foreach ( $js_files as $js_name => $js_obj ) {
					Helper::load_js( $js_prefix . $js_name, $js_obj );
				}

				foreach ( $css_files as $css_name => $css_obj ) {
					Helper::load_css( $css_name, $css_obj );
				}
			}
		}

		/**
		 * Callback for modifying ajax endpoints.
		 *
		 * @param array $endpoints default ajax endpoints.
		 *
		 * @return  array
		 */
		public function custom_ajax_endpoints_callback( $endpoints ) {
			$new_endpoints = $this->ajax_endpoints();

			// Merge custom and core endpoints.
			if ( ! empty( $new_endpoints ) ) {
				$endpoints = array_merge( $endpoints, $new_endpoints );
			}

			return $endpoints;
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

		/**
		 * Get default js files in checkout page.
		 *
		 * @return array
		 */
		private function reserve_js_files() {
			return [
				'default_checkout' => [
					'url'     => WACARA_URI . 'assets/js/checkout.js',
					'modules' => true,
				],
			];
		}

		/**
		 * Render custom content based on registrant status.
		 *
		 * @param Registrant                $registrant object of the current registrant.
		 * @param Payment_Method|bool|mixed $payment_class object of the selected payment method.
		 * @param string                    $reg_status status of the registrant.
		 * @param Pricing                   $pricing object of selected pricing.
		 */
		public function render_custom_content( $registrant, $payment_class, $reg_status, $pricing ) {

			// Override template folder.
			Template::override_folder( $this->path );

			$temp_args = [
				'registrant' => $registrant,
				'pricing'    => $pricing,
				'reg_status' => $reg_status,
			];

			/**
			 * Wacara filter registrant custom content args hook.
			 *
			 * @param array $temp_args default args.
			 * @param string $reg_status status of the current registrant.
			 * @param Registrant $registrant object of the current registrant.
			 * @param Payment_Method|bool|mixed $payment_class object of the selected payment method.
			 */
			$temp_args = apply_filters( 'wacara_filter_registrant_custom_content_args', $temp_args, $reg_status, $registrant, $payment_class );

			// Render the template.
			Template::render( 'registrant/status-' . $reg_status, $temp_args, true );

			// Reset template folder.
			Template::reset_folder();
		}
	}
}
