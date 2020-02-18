<?php
/**
 * Main class file for Stripe payment.
 *
 * @author WPerfekt
 * @package Wacara\Payment
 * @version 0.0.1
 */

namespace Wacara\Payment;

use Wacara\Event;
use Wacara\Helper;
use Wacara\Payment\Stripe_Payment\Stripe_Wrapper;
use Wacara\Payment\Stripe_Payment\Transaction;
use Wacara\Payment_Method;
use Wacara\Registrant;
use Wacara\Result;
use Wacara\Template;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'Wacara\Payment\Stripe_Payment' ) ) {

	/**
	 * Class Stripe_Payment
	 *
	 * @package Wacara\Payment
	 */
	class Stripe_Payment extends Payment_Method {

		/**
		 * Instance variable.
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Singleton.
		 *
		 * @return Stripe_Payment|null
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Stripe_Payment constructor.
		 */
		protected function __construct() {
			$this->id              = 'stripe-payment';
			$this->name            = __( 'Stripe Payment', 'wacara' );
			$this->description     = __( 'Automatic payment using Stripe gateway', 'wacara' );
			$this->custom_checkout = true;
			$this->enable          = true;
			$this->path            = __FILE__;

			parent::__construct();

			$this->load_dependencies();
			$this->register_post_types();
		}

		/**
		 * Render the stripe payment in front-end.
		 */
		public function render() {
			?>
			<div id="card" class="wcr-form-field-wrapper"></div>
			<div class="wcr-alert wcr-alert-info">
				<p><?php esc_html_e( 'We do not save your card information.', 'wacara' ); ?></p>
			</div>
			<?php
		}

		/**
		 * Function to calculate and process the payment.
		 *
		 * @param Registrant $registrant the registrant object of registered registrant.
		 * @param array      $fields used fields which is stored from front-end, mostly it contains unserialized object.
		 * @param int        $pricing_price_in_cent amount of invoice in cent.
		 * @param string     $pricing_currency the currency code of invoice.
		 *
		 * @return Result
		 */
		public function process( $registrant, $fields, $pricing_price_in_cent, $pricing_currency ) {
			$result                 = new Result();
			$maybe_stripe_source_id = Helper::get_serialized_val( $fields, 'stripe_source_id' );
			$email                  = Helper::get_serialized_val( $fields, 'email' );
			$name                   = Helper::get_serialized_val( $fields, 'name' );

			// Look for saved customer.
			$find_stripe_customer = Transaction::find_stripe_customer_id_by_email( $email );

			// Prepare the variable that will be used to store stripe customer id.
			$used_stripe_customer_id = '';

			// Instance the stripe.
			$stripe_wrapper = new Stripe_Wrapper( $this->get_secret_key() );

			// Validate find stripe customer status.
			if ( $find_stripe_customer->success ) {

				// Update customer source information, just in case they use different cc information.
				$update_customer = $stripe_wrapper->update_customer_source( $find_stripe_customer->callback, $maybe_stripe_source_id );

				// Validate update customer status.
				if ( $update_customer->success ) {

					// Use stripe customer id.
					$used_stripe_customer_id = $update_customer->callback;
				} else {

					// Update the result.
					$result->message = $update_customer->message;
				}
			} else {

				// Save a new customer.
				$new_customer = Transaction::save_customer( $stripe_wrapper, $name, $email, $maybe_stripe_source_id );

				// Validate save new customer status.
				if ( $new_customer->success ) {

					// Use stripe customer id of new customer.
					$used_stripe_customer_id = $new_customer->callback;
				} else {

					// Update the result.
					$result->message = $new_customer->message;
				}
			}

			// Check whether stripe customer id that will be used has been defined or not yet.
			if ( $used_stripe_customer_id ) {

				// Charge the customer.
				$event_id = $registrant->get_event_info();
				/* translators: 1: the event name */
				$charge_name = sprintf( __( 'Payment for registering to %s', 'wacara' ), get_the_title( $event_id ) );
				$charge      = $stripe_wrapper->charge_customer( $used_stripe_customer_id, $maybe_stripe_source_id, $pricing_price_in_cent, $pricing_currency, $charge_name );

				// Validate charge status.
				if ( $charge->success ) {

					// Update result.
					$result->success  = true;
					$result->callback = 'done';

				} else {

					// Update result.
					$result->callback = 'fail';
					$result->message  = $charge->message;
				}

				/**
				 * Perform actions after making payment by stripe.
				 *
				 * @param Registrant $registrant object of the current registrant.
				 * @param int $pricing_price_in_cent the price of pricing in cent.
				 * @param string $pricing_currency the currency of pricing.
				 * @param Result $charge the object of payment.
				 */
				do_action( 'wacara_after_stripe_payment', $registrant, $pricing_price_in_cent, $pricing_currency, $charge );
			}

			return $result;
		}

		/**
		 * Admin settings.
		 *
		 * @inheritDoc
		 * @return array
		 */
		public function admin_setting() {
			return [
				[
					'name' => __( 'Sandbox', 'wacara' ),
					'desc' => __( 'Enable sandbox for testing', 'wacara' ),
					'id'   => 'sandbox',
					'type' => 'checkbox',
				],
				[
					'name' => __( 'Sandbox secret key', 'wacara' ),
					'id'   => 'sandbox_secret_key',
					'type' => 'text',
					'desc' => __( 'Normally it something like this sk_test_xxx', 'wacara' ),
				],
				[
					'name' => __( 'Sandbox publishable key', 'wacara' ),
					'id'   => 'sandbox_publishable_key',
					'type' => 'text',
					'desc' => __( 'Normally it something like this pk_test_xxx', 'wacara' ),
				],
				[
					'name' => __( 'Live secret key', 'wacara' ),
					'id'   => 'live_secret_key',
					'type' => 'text',
					'desc' => __( 'Normally it something like this sk_live_xxx', 'wacara' ),
				],
				[
					'name' => __( 'Live publishable key', 'wacara' ),
					'id'   => 'live_publishable_key',
					'type' => 'text',
					'desc' => __( 'Normally it something like this pk_live_xxx', 'wacara' ),
				],
			];
		}

		/**
		 * Map js files that will be loaded in front-end.
		 *
		 * @return array
		 */
		public function front_js() {
			return [
				'stripe-api'     => [
					'url'    => 'https://js.stripe.com/v3',
					'module' => false,
				],
				'stripe-payment' => [
					'url'  => WCR_STP_URI . 'js/stripe-payment.js',
					'vars' => [
						'stripe_key' => $this->get_publishable_key(),
					],
				],
			];
		}

		/**
		 * Map css files that will be loaded in front-end.
		 *
		 * @return array
		 */
		public function front_css() {
			return [];
		}

		/**
		 * Map js files that will be loaded in back-end.
		 *
		 * @return array
		 */
		public function admin_js() {
			return [];
		}

		/**
		 * Map css files that will be loaded in back-end.
		 *
		 * @return array
		 */
		public function admin_css() {
			return [];
		}

		/**
		 * Map custom ajax endpoints.
		 *
		 * @return array
		 */
		public function ajax_endpoints() {
			return [];
		}

		/**
		 * Load dependency classes.
		 */
		private function load_dependencies() {
			include WCR_STP_PATH . 'lib/stripe-php/init.php';
			include WCR_STP_PATH . 'includes/class-stripe-wrapper.php';
			include WCR_STP_PATH . 'includes/class-transaction.php';
		}

		/**
		 * Register post types.
		 */
		private function register_post_types() {
			Helper::register_post_type(
				'customer',
				[],
				[
					'public'       => false,
					'show_ui'      => false,
					'show_in_menu' => false,
					'query_var'    => false,
					'capabilities' => [
						'create_posts' => 'do_not_allow',
					],
				]
			);
		}

		/**
		 * Get status whether current payment is using sandbox or not.
		 *
		 * @return bool
		 */
		private function is_sandbox() {
			$result          = true;
			$sandbox_setting = $this->get_admin_setting( 'sandbox' );
			if ( 'on' !== $sandbox_setting ) {
				$result = false;
			}
			return $result;
		}

		/**
		 * Get publishable key.
		 *
		 * @return bool|mixed|void
		 */
		private function get_publishable_key() {
			$sb_key = 'publishable_key';
			if ( $this->is_sandbox() ) {
				$sb_key = 'sandbox_' . $sb_key;
			}
			return $this->get_admin_setting( $sb_key );
		}

		/**
		 * Get secret key.
		 *
		 * @return bool|mixed|void
		 */
		private function get_secret_key() {
			$sb_key = 'secret_key';
			if ( $this->is_sandbox() ) {
				$sb_key = 'sandbox_' . $sb_key;
			}
			return $this->get_admin_setting( $sb_key );
		}
	}

	Stripe_Payment::init();
}
