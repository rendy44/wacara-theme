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
			$recipient_detail = $registrant->get_more_details();
			$mailer_recipient = "{$recipient_detail['name']} <{$recipient_detail['email']}>";
			$mailer_subject   = __( 'Thank you for registering', 'wacara' );
			$template_args    = [
				'recipient_name'  => $recipient_detail['name'],
				'recipient_email' => $recipient_detail['email'],
			];

			// Construct the parent.
			parent::__construct( $mailer_id, $mailer_title, true, $mailer_recipient, $mailer_subject, $registrant, $template_args );
		}
	}
}
