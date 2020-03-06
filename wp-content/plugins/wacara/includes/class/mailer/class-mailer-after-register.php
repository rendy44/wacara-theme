<?php
/**
 * Class to send email after register.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Mailer_After_Register' ) ) {

	/**
	 * Class Mailer_After_Register
	 *
	 * @package Wacara
	 */
	class Mailer_After_Register extends Mailer {

		/**
		 * Mailer_After_Register constructor.
		 *
		 * @param Registrant $registrant object of the current registrant.
		 */
		public function __construct( $registrant ) {

			// Prepare all details.
			$mailer_id        = 'after-register';
			$mailer_title     = __( 'After register', 'wacara' );
			$mailer_recipient = "{$registrant->get_registrant_name()} <{$registrant->get_registrant_email()}>";
			/* translators: %1$s : name of the event, %2$s : id of the invoice */
			$mailer_subject = sprintf( __( '%1$s - Invoice for #%2$s', 'wacara' ), $registrant->get_event_name(), $registrant->get_invoice_id() );

			// Construct the parent.
			parent::__construct( $mailer_id, $mailer_title, true, $mailer_recipient, $mailer_subject, $registrant );
		}
	}
}
