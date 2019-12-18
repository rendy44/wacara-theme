<?php
/**
 * The parent class for payment method.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Skeleton\Payment_Method' ) ) {

	/**
	 * Class Payment_Method
	 *
	 * @package Skeleton
	 */
	abstract class Payment_Method {

		/**
		 * Payment method's id variable.
		 *
		 * @var string
		 */
		public $id = '';

		/**
		 * Payment method's name variable.
		 *
		 * @var string
		 */
		public $name = '';

		/**
		 * Payment method's description variable.
		 *
		 * @var string
		 */
		public $description = '';

		/**
		 * Payment method's automatic variable.
		 *
		 * @var bool
		 */
		public $automatic = true;

		/**
		 * Payment method's availability variable.
		 *
		 * @var bool
		 */
		public $enable = true;

		/**
		 * Function to render the payment in checkout page.
		 */
		abstract public function render();

		/**
		 * Function to calculate and process the payment.
		 *
		 * @param string $participant_id the id of registered participant.
		 * @param string $pricing_id the id of selected pricing.
		 * @param array  $fields used fields which is stored from front-end, mostly it contains unserialized object.
		 *
		 * @return Result
		 */
		abstract public function process( $participant_id, $pricing_id, $fields );

		/**
		 * Get cmb2 fields that will be translated into option page.
		 *
		 * @return array
		 */
		abstract public function admin_setting();

		/**
		 * Get content that will be rendered after making manual payment.
		 *
		 * @param string $participant_id the id of currently processed participant.
		 * @param string $reg_status current registration status of the participant.
		 * @param string $pricing_id the id of selected pricing of current processed participant.
		 * @param string $event_id the id of selected event of current processed participant.
		 *
		 * @return string
		 */
		abstract public function maybe_page_after_payment( $participant_id, $reg_status, $pricing_id, $event_id );

		/**
		 * Get admin settings from db.
		 *
		 * @param string $key specific key to filter the admin setting.
		 *
		 * @return bool|mixed|void
		 */
		public function get_admin_setting( $key = '' ) {
			$payment_options = Options::get_the_options( $this->id );
			$result          = $payment_options;

			// Filter the result by key.
			if ( $key ) {
				$result = ! empty( $payment_options[ $key ] ) ? $payment_options[ $key ] : false;
			}

			return $result;
		}

		/**
		 * Get content for displaying success page.
		 *
		 * @return string
		 */
		protected function get_success_page() {
			return Template::render( 'participant/register-success' ); // phpcs:ignore
		}
	}
}
