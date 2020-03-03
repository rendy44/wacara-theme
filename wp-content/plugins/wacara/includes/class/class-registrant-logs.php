<?php
/**
 * Class to save registrant logs through action hooks..
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Registrant_Logs' ) ) {

	/**
	 * Class Registrant_Logs
	 *
	 * @package Wacara
	 */
	class Registrant_Logs {

		/**
		 * Instance variable.
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Singleton.
		 *
		 * @return Registrant_Logs|null
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Registrant_Logs constructor.
		 */
		private function __construct() {
			add_action( 'wacara_after_creating_registrant_ajax', [ $this, 'log_after_selecting_pricing_callback' ], 10, 2 );
		}

		/**
		 * Callback for logging registrant after selecting pricing.
		 *
		 * @param Registrant $registrant newly created registrant.
		 * @param array      $cached_data data from pricing that stored in post meta.
		 */
		public function log_after_selecting_pricing_callback( $registrant, $cached_data ) {

			// Validate the registrant.
			if ( $registrant->success ) {
				/* translators: %s name of the pricing */
				$registrant->add_logs( sprintf( __( '%s package successfully selected', 'wacara' ), $cached_data['pricing_cache_name'] ) );
			}
		}
	}

	Registrant_Logs::init();
}
