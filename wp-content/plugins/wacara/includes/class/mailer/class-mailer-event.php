<?php
/**
 * A class to manage all email events.
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Mailer_Event' ) ) {

	/**
	 * Class Mailer_Event
	 *
	 * @package Wacara
	 */
	class Mailer_Event {

		/**
		 * Instance variable.
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Singleton function
		 *
		 * @return Mailer_Event|null
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Mailer_Event constructor.
		 */
		private function __construct() {

			// Send email after registrant filling details.
			add_action( 'wacara_after_filling_registration', [ $this, 'send_email_after_register_callback' ], 10, 3 );
		}

		/**
		 * Callback for sending email after register.
		 *
		 * @param Registrant $registrant object of the current registrant.
		 * @param string     $reg_status the status of registration.
		 * @param Result     $result object of the current process.
		 */
		public function send_email_after_register_callback( $registrant, $reg_status, $result ) {

			// Only send email if result is success.
			if ( ! $result->success || 'done' === $reg_status ) {
				return;
			}

			// Instance the mailer.
			$after_register_mailer = new Mailer_After_Register( $registrant );

			// Make sure that mailer is enabled.
			if ( ! $after_register_mailer->is_enabled() ) {
				return;
			}

			// Send the email.
			$after_register_mailer->send();
		}
	}

	Mailer_Event::init();
}
