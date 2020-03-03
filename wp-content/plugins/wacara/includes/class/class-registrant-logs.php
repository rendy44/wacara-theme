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

			// Log registrant after selecting pricing.
			add_action( 'wacara_after_creating_registrant_ajax', [ $this, 'log_after_selecting_pricing_callback' ], 10, 2 );

			// Log registrant after filling details.
			add_action( 'wacara_after_filling_registration', [ $this, 'log_after_filling_details_callback' ], 10, 3 );

			// Log registrant each time the status being changed.
			add_action( 'wacara_after_setting_registrant_status', [ $this, 'log_after_status_changed_callback' ], 10, 3 );

			// Log registrant after making checking-out.
			add_action( 'wacara_after_registrant_payment_process', [ $this, 'log_after_checking_out_callback' ], 10, 3 );
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
				$registrant->add_logs( sprintf( __( '%s package successfully selected.', 'wacara' ), $cached_data['pricing_cache_name'] ) );
			}
		}

		/**
		 * Callback for logging registrant after filling details.
		 *
		 * @param Registrant $registrant object of the current registrant.
		 * @param string     $reg_status status of the registrant.
		 * @param Result     $result object of the current process.
		 */
		public function log_after_filling_details_callback( $registrant, $reg_status, $result ) {

			// First of all, validate the status.
			if ( $result->success ) {
				// Instance payment method.
				$payment_method = $registrant->get_payment_method_object();

				// Set default log content.
				/* translators: %s : name of the payment method */
				$log_content = sprintf( __( '%s payment method is selected.', 'wacara' ), $payment_method->name );

				// Change content if registrant already done, maybe for a free package.
				if ( 'done' === $reg_status ) {
					$log_content = __( 'Registration is done, since it is free.', 'wacara' );
				}
			} else {
				/* translators: %s further detail of the errors */
				$log_content = sprintf( __( 'Failed filling details. %s', 'wacara' ), $result->message );
			}

			$registrant->add_logs( $log_content );
		}

		/**
		 * Callback for logging registrant each status being changed.
		 *
		 * @param Registrant $registrant object of the current registrant.
		 * @param string     $new_status new status of the registrant.
		 * @param string     $old_status old status of the registrant.
		 */
		public function log_after_status_changed_callback( $registrant, $new_status, $old_status ) {

			// Only process if new status is different from the old one.
			if ( $new_status !== $old_status ) {
				/* translators: %s : new registrant status */
				$log_content = sprintf( __( 'Status changed to %s', 'wacara' ), Registrant_Status::get_status( $new_status ) );

				// Maybe add detail from the old status.
				/* translators: %s : old registrant status */
				$log_content .= $old_status ? ' ' . sprintf( __( 'from %s.', 'wacara' ), Registrant_Status::get_status( $old_status ) ) : '.';

				$registrant->add_logs( $log_content );
			}
		}

		/**
		 * Callback for logging registrant after checking out.
		 *
		 * @param Registrant $registrant object of the current registrant.
		 * @param string     $reg_status status of the current registrant.
		 * @param Result     $result object of the current process.
		 */
		public function log_after_checking_out_callback( $registrant, $reg_status, $result ) {
			// Validate the result.
			if ( $result->success ) {
				$log_content = __( 'Successfully checked-out', 'wacara' );
			} else {
				/* translators: %s further detail of the errors */
				$log_content = sprintf( __( 'Failed checkout. %s', 'wacara' ), $result->message );
			}

			$registrant->add_logs( $log_content );
		}
	}

	Registrant_Logs::init();
}
