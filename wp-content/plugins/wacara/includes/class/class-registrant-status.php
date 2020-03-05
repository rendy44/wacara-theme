<?php
/**
 * Class to manage registrant status.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Registrant_Status' ) ) {

	/**
	 * Class Registrant_Status
	 *
	 * @package Wacara
	 */
	class Registrant_Status {

		/**
		 * Status variable.
		 *
		 * @var array
		 */
		private static $status = [];

		/**
		 * Init registrant default status.
		 */
		public static function init() {

			// Prepare default status.
			$default_status = [
				'hold'   => __( 'Waiting details', 'wacara' ),
				'done'   => __( 'Done', 'wacara' ),
				'fail'   => __( 'Fail', 'wacara' ),
				'reject' => __( 'Rejected', 'wacara' ),
			];

			/**
			 * Wacara registrant default status filter hook.
			 *
			 * @param array $default_status default status.
			 */
			$default_status = apply_filters( 'wacara_filter_registrant_default_status', $default_status );

			// Save the attribute.
			self::$status = $default_status;
		}

		/**
		 * Maybe get available status.
		 *
		 * @param string $key filter status by key.
		 *
		 * @return array|bool|mixed
		 */
		public static function get_status( $key = '' ) {

			// Prepare default.
			$result = __( 'Pending', 'wacara' );

			// Maybe search status by filter.
			if ( $key ) {
				$result = Helper::array_val( self::$status, $key );
			}

			return $result;
		}

		/**
		 * Get list of all available status.
		 *
		 * @return array
		 */
		public static function get_all_status() {
			return self::$status;
		}

		/**
		 * Set registrant status.
		 *
		 * @param Registrant $registrant object of the current registrant.
		 * @param string     $status new status of the registrant.
		 */
		public static function set_registrant_status( $registrant, $status ) {

			// Validate the new status.
			$validate_status = self::get_status( $status );
			if ( $validate_status ) {

				// Save the status.
				$registrant->set_registration_status( $status );
			}
		}

		/**
		 * Add a new registrant status.
		 *
		 * @param string $key key of the status.
		 * @param string $label readable name of the status.
		 */
		public static function register_new_status( $key, $label ) {
			self::$status[ $key ] = $label;
		}
	}
}
