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
			// Send email after participant status changed.
			add_action( 'wacara_after_setting_participant_status', [ $this, 'send_email_based_on_status' ], 10, 3 );

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
		 * @param string $new_status     the new status of registration.
		 * @param string $old_status     the old status of registration.
		 */
		public function send_email_based_on_status( $participant_id, $new_status, $old_status ) {

			// Fetch participant detail.
			$participant      = new Participant( $participant_id );
			$participant_data = $participant->get_data();
			$event_name       = get_the_title( $participant_data['event_id'] );

			$site_name = get_bloginfo( 'name' );
			/* translators: 1: the site name */
			$email_subject = sprintf( __( 'Welcome To %s', 'wacara' ), $site_name );
			/* translators: 1: participant name, 2: event name, 3: booking code*/
			$email_content = sprintf( __( 'Hello %1$s, thank you for registering to %2$s. This is your booking code: %3$s', 'wacara' ), $participant_data['name'], $event_name, $participant_data['booking_code'] );

			switch ( $new_status ) {
				case 'wait_payment':
					/* translators: 1: participant name, 2: event name */
					$email_content = sprintf( __( 'Hello %1$s, thank you for registering to %2$s. Please do a payment to save your seat', 'wacara' ), $participant_data['name'], $event_name );
					break;
				case 'wait_verification':
					$email_subject = __( 'Thank You For Confirmation', 'wacara' );
					/* translators: 1: participant name, 2: event name */
					$email_content = sprintf( __( 'Hello %1$s, thank you for the confirmation. We are verifying your payment. And we will get back to you once we have an update', 'wacara' ), $participant_data['name'] );
					break;
				case 'done':
					// Check whether participant previously from other status or directly to done status.
					if ( 'wait_verification' === $old_status ) {
						$email_subject = __( 'Congratulation', 'wacara' );
						/* translators: 1: participant name, 2: event name, 3: booking code*/
						$email_content = sprintf( __( 'Hello %1$s, we have verified your payment. You are ready to join the %2$s. This is your booking code: %3$s ', 'wacara' ), $participant_data['name'], $event_name, $participant_data['booking_code'] );
					}
					break;
			}

			/**
			 * Apply filters to modify email content after participant making registration.
			 *
			 * @param string $email_content  the original email content.
			 * @param string $participant_id the id of registered participant.
			 */
			$email_content = apply_filters( 'wacara_filter_email_content_after_register', $email_content, $participant_id );

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

			/**
			 * Apply filters to modify email content after participant checking in.
			 *
			 * @param string $email_content  the original email content.
			 * @param string $participant_id the id of registered participant.
			 */
			$email_content = apply_filters( 'wacara_filter_email_content_after_checkin', $email_content, $participant_id );

			// Send the email.
			$this->send_email( $participant_data['email'], $participant_data['name'], $email_subject, $email_content );
		}
	}
}

Mailer::init();
