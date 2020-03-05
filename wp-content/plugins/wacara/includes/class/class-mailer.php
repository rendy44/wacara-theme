<?php
/**
 * Class to manage the mailer.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Mailer' ) ) {

	/**
	 * Class Mailer
	 *
	 * @package Wacara
	 */
	class Mailer {

		/**
		 * Email id variable.
		 *
		 * @var string
		 */
		private $id = '';

		/**
		 * Email title variable.
		 *
		 * @var string
		 */
		private $title = '';

		/**
		 * Email enable status variable.
		 *
		 * @var bool
		 */
		private $enabled = false;

		/**
		 * Email recipients variable.
		 *
		 * @var array|string
		 */
		private $recipients = [];

		/**
		 * Email subject variable.
		 *
		 * @var string
		 */
		private $subject = '';

		/**
		 * Email basic content.
		 *
		 * @var string
		 */
		private $basic_content = '';

		/**
		 * Mailer constructor.
		 *
		 * @param string       $id mailer unique id.
		 * @param string       $title mailer title.
		 * @param bool         $enabled whether enable or disable mailer.
		 * @param array|string $recipients mailer recipients.
		 * @param string       $subject mailer subject.
		 * @param Registrant   $registrant object of the current registrant.
		 * @param array        $plain_template_args default template args.
		 */
		public function __construct( $id, $title, $enabled, $recipients, $subject, $registrant, $plain_template_args = [] ) {

			// Save the properties.
			$this->id      = $id;
			$this->title   = $title;
			$this->enabled = $enabled;

			/**
			 * Wacara mailer recipient filter hook.
			 *
			 * @param array|string $recipients default recipient.
			 * @param string $id id of the current mailer.
			 * @param Registrant $registrant object of the current registrant.
			 */
			$this->recipients = apply_filters( 'wacara_filter_mailer_recipients', $recipients, $id, $registrant );

			/**
			 * Wacara mailer recipient filter hook.
			 *
			 * @param array|string $subject default subject.
			 * @param string $id id of the current mailer.
			 * @param Registrant $registrant object of the current registrant.
			 */
			$this->subject = apply_filters( 'wacara_filter_mailer_subject', $subject, $id, $registrant );

			// Add default plain template args.
			$default_template_args = [
				'recipient_name'  => $registrant->get_registrant_name(),
				'recipient_email' => $registrant->get_registrant_email(),
			];
			$plain_template_args   = wp_parse_args( $plain_template_args, $default_template_args );

			/**
			 * Wacara mailer recipient filter hook.
			 *
			 * @param array|string $plain_template_args default args.
			 * @param string $id id of the current mailer.
			 * @param Registrant $registrant object of the current registrant.
			 */
			$plain_template_args = apply_filters( 'wacara_filter_mailer_plain_template_args', $plain_template_args, $id, $registrant );

			$this->basic_content = Template::render( "email/plain/{$id}-template", $plain_template_args );
		}

		/**
		 * Get status whether the email is enabled or not.
		 *
		 * @return bool
		 */
		public function is_enabled() {
			return $this->enabled;
		}

		/**
		 * Do send email.
		 *
		 * @return bool
		 */
		public function send() {
			return wp_mail( $this->get_formatted_recipients(), $this->subject, $this->get_formatted_content() );
		}

		/**
		 * Get formatted template
		 *
		 * @return string|void
		 */
		private function get_formatted_content() {

			// Prepare template args.
			$template_args = [
				'plain_content' => $this->basic_content,
			];

			/**
			 * Wacara mailer template args filter hook.
			 *
			 * @param array $template_args default args.
			 * @param Mailer $mailer object of the current mailer.
			 */
			$template_args = apply_filters( 'wacara_filter_mailer_template_args', $template_args, $this );

			return Template::render( 'email/template', $template_args );
		}

		/**
		 * Get formatted recipients.
		 *
		 * @return array|string
		 */
		private function get_formatted_recipients() {
			if ( is_array( $this->recipients ) ) {
				$result = array_map( [ $this, 'get_formatted_single_recipient' ], $this->recipients );
			} else {
				$result = $this->get_formatted_single_recipient( $this->recipients );
			}

			return $result;
		}

		/**
		 * Format single recipient block.
		 *
		 * @param array|string $single_recipient single block recipient.
		 *
		 * @return string
		 */
		private function get_formatted_single_recipient( $single_recipient ) {
			$result = '';
			if ( is_array( $single_recipient ) ) {
				$maybe_name  = Helper::array_val( $single_recipient, 'name' );
				$maybe_email = Helper::array_val( $single_recipient, 'email' );
				if ( is_email( $maybe_email ) ) {
					$result = "{$maybe_name} <{$maybe_email}>";
				}
			} elseif ( is_email( $single_recipient ) ) {
				$result = $single_recipient;
			} else {
				$result = $this->maybe_formatted_with_symbol( $single_recipient );
			}

			return $result;
		}

		/**
		 * Maybe recipient already formatted beautifully.
		 *
		 * @param string $single_recipient single string recipient.
		 *
		 * @return string
		 */
		private function maybe_formatted_with_symbol( $single_recipient ) {
			$result = '';

			// We are assuming this is formatted like this `name <email@random.domain>.
			if ( strpos( $single_recipient, '>' ) ) { // TODO: get a better validation.
				$result = $single_recipient;
			}

			return $result;
		}
	}
}
