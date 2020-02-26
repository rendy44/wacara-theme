<?php
/**
 * Use this class to override cmb2 default behaviour nor extends its functionality including its library extensions.
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\CMB2_Hooks' ) ) {

	/**
	 * Class CMB2_Hooks
	 *
	 * @package Wacara
	 */
	class CMB2_Hooks {

		/**
		 * Instance variabe
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Singleton function
		 *
		 * @return CMB2_Hooks|null
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * CMB2_Hooks constructor.
		 */
		private function __construct() {
			// Add filter to override cmb2-select2 assets uri.
			add_filter( 'pw_cmb2_field_select2_asset_path', [ $this, 'override_cmb2_select2_assets_uri_callback' ] );
		}

		/**
		 * Callback for overriding cmb2 select2 field's assets uri.
		 *
		 * @return string
		 */
		public function override_cmb2_select2_assets_uri_callback() {
			return WACARA_URI . '/includes/lib/cmb2-select2';
		}
	}

	CMB2_Hooks::init();
}
