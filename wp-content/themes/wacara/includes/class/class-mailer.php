<?php
/**
 * Handle class to manage emails
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Skeleton\Mailer' ) ) {
	/**
	 * Class Mailer
	 *
	 * @package Skeleton
	 */
	class Mailer {
		/**
		 * Instance variable.
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Singleton function
		 *
		 * @return Mailer|null
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Mailer constructor.
		 */
		private function __construct() {
			// Send email after finishing registration.
			add_action( 'wacara_after_finishing_registration', [ $this, 'send_email_after_registration' ], 10 );

			// Send email after checking in.
			add_action( 'wacara_after_participant_checkin', [ $this, 'send_email_after_checkin' ], 10 );
		}

		/**
		 * Perform send email.
		 *
		 * @param string $recipient_email the email recipients.
		 * @param string $recipient_name  the name of recipient.
		 * @param string $subject         email subject.
		 * @param string $content         email body content.
		 * @param array  $headers         email headers.
		 */
		private function send_email( $recipient_email, $recipient_name, $subject, $content, $headers = [] ) {
			$recipient_format = "{$recipient_name} <{$recipient_email}>";

			/**
			 * Perform filter to modify email content.
			 *
			 * @param string $content the plain email content.
			 */
			$content = apply_filters( 'wacara_filter_email_body', $content );

			/**
			 * Perform filter to modify email headers.
			 *
			 * @param array $headers email headers.
			 */
			$headers = apply_filters( 'wacara_filter_email_headers', $headers );

			wp_mail( $recipient_format, $subject, $content, $headers );
		}

		/**
		 * Callback for sending email after finishing registration.
		 *
		 * @param string $participant_id participant id.
		 */
		public function send_email_after_registration( $participant_id ) {

			// Fetch participant detail.
			$participant      = new Participant( $participant_id );
			$participant_data = $participant->get_data();
			$event_name       = get_the_title( $participant_data['event_id'] );

			$site_name = get_bloginfo( 'name' );
			/* translators: 1: the site name */
			$email_subject = sprintf( __( 'Welcome To %s', 'wacara' ), $site_name );
			/* translators: 1: participant name, 2: event name, 3: booking code*/
			$email_content = sprintf( __( 'Hello %1$s, thank you for registering to %2$s. This is your booking code: %3$s', 'wacara' ), $participant_data['name'], $event_name, $participant_data['booking_code'] );

			// Send the email.
			$this->send_email( $participant_data['email'], $participant_data['name'], $email_subject, $email_content );
		}

		/**
		 * Callback for sending email after checking in.
		 *
		 * @param string $participant_id participant id.
		 */
		public function send_email_after_checkin( $participant_id ) {

			// Fetch participant detail.
			$participant      = new Participant( $participant_id );
			$participant_data = $participant->get_data();
			$event_name       = get_the_title( $participant_data['event_id'] );

			/* translators: 1: the event name */
			$email_subject = sprintf( __( 'Thank you for checking in to %s', 'wacara' ), $event_name );
			/* translators: 1: participant name, 2: event name*/
			$email_content = sprintf( __( 'Hello %1$s, thank you for checking in to %2$s', 'wacara' ), $participant_data['name'], $event_name );

			// Send the email.
			$this->send_email( $participant_data['email'], $participant_data['name'], $email_subject, $email_content );
		}
	}
}

Mailer::init();
