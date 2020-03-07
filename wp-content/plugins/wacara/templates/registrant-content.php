<?php
/**
 * Template for displaying single registrant content.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

use Wacara\Payment_Method;
use Wacara\Event_Pricing;
use Wacara\Register_Payment;
use Wacara\Registrant;
use Wacara\Template;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Fetch registrant object.
$registrant = new Registrant( get_the_ID() );

// Fetch registration status.
$reg_status = $registrant->get_registration_status();

// Fetch event detail.
$event_id = $registrant->get_event_id();

// Fetch payment method.
$payment_class = $registrant->get_payment_method_object();

/**
 * Wacara before registrant masthead hook.
 *
 * @param Registrant $registrant the object of the current registrant.
 *
 * @hooked UI::registrant_masthead_opening_callback - 10
 */
do_action( 'wacara_before_registrant_masthead', $registrant );

/**
 * Wacara registrant masthead hook.
 *
 * @param Registrant $registrant the object of the current registrant.
 * @param string $reg_status the status of the registrant.
 *
 * @hooked UI::registrant_masthead_content_callback - 10
 */
do_action( 'wacara_registrant_masthead', $registrant, $reg_status );

/**
 * Wacara after registrant masthead hook.
 *
 * @param Registrant $registrant the object of the current registrant.
 *
 * @hooked UI::registrant_masthead_closing_callback - 50
 */
do_action( 'wacara_after_registrant_masthead', $registrant );

/**
 * Wacara before registrant content hook.
 *
 * @param Registrant $registrant the object of the current registrant.
 *
 * @hooked UI::registrant_section_opening_callback - 10
 */
do_action( 'wacara_before_registrant_content', $registrant );

// Check whether registrant is still fresh or not.
if ( '' === $reg_status ) {

	/**
	 * Wacara before registrant form content hook.
	 *
	 * @param Registrant $registrant the object of the current registrant.
	 *
	 * @hooked UI::registrant_form_opening_callback - 10
	 */
	do_action( 'wacara_before_registrant_form_content', $registrant );

	$used_fields = [ 'name', 'email' ];

	// Render all the fields.
	foreach ( $used_fields as $field ) {

		/**
		 * Wacara input field for event filter hook.
		 *
		 * @param int $event_id id of the selected event.
		 * @param string $field id of the field.
		 */
		echo apply_filters( 'wacara_input_field_event', $event_id, $field ); // phpcs:ignore
	}

	/**
	 * Wacara before registrant payment method form hook.
	 *
	 * @param Registrant $registrant the object of the current registrant.
	 */
	do_action( 'wacara_before_registrant_payment_method_form', $registrant );

	// Validate the pricing price.
	if ( $registrant->get_pricing_price() > 0 ) {

		// Fetch all available payment methods.
		$payment_methods = Register_Payment::get_registered();

		if ( ! empty( $payment_methods ) ) {
			?>
			<div class="wcr-field-payment-method wcr-form-field-wrapper">
				<label><?php esc_html_e( 'Payment Method', 'wacara' ); ?></label>
				<?php
				$payment_count = 0;
				foreach ( $payment_methods as $payment_method ) {
					$maybe_checked = 0 === $payment_count ? 'checked' : '';
					?>
					<div class="wcr-form-field-multi-radio-wrapper">
						<input type="radio" class="wcr-form-field" id="payment_<?php echo esc_attr( $payment_method->id ); ?>" name="payment_method" value="<?php echo esc_attr( $payment_method->id ); ?>" <?php echo esc_attr( $maybe_checked ); ?>>
						<label for="payment_<?php echo esc_attr( $payment_method->id ); ?>" class="wcr-tooltip"><?php echo esc_html( $payment_method->name ); ?><span class="wcr-tooltip-text"><?php echo esc_html( $payment_method->description ); ?></span></label>
					</div>
					<?php
					$payment_count ++;
				}
				?>
			</div>
			<?php
		} else {

			$error_no_payment = __( 'No payment methods available', 'wacara' );

			/**
			 * Wacara registrant no payment method message filter hook.
			 *
			 * @param string $error_no_payment current error message.
			 * @param Registrant $registrant object of the current registrant.
			 */
			$error_no_payment = apply_filters( 'wacara_filter_registrant_no_payment_method', $error_no_payment, $registrant );

			?>
			<div class="wcr-form-field-wrapper">
				<div class="wcr-alert wcr-alert-danger">
					<?php echo sprintf( '<p>%s</p>', $error_no_payment ); // phpcs:ignore ?>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Wacara after registrant payment method form hook.
	 *
	 * @param Registrant $registrant the object of the current registrant.
	 */
	do_action( 'wacara_after_registrant_payment_method_form', $registrant );

	?>
	<div class="wcr-field-disclaimer wcr-form-field-wrapper">
		<div class="wcr-alert wcr-alert-info">
			<p><?php esc_html_e( 'By clicking continue, you are automatically agree to our term of service', 'wacara' ); ?></p>
		</div>
	</div>

	<?php
	$submit_label = __( 'Continue', 'wacara' );

	/**
	 * Wacara hold registrant submit button filter hook.
	 *
	 * @param string $submit_label current submit label.
	 * @param Registrant $registrant object of the current registrant.
	 */
	$submit_label = apply_filters( 'wacara_filter_hold_registrant_submit_label', $submit_label, $registrant );

	$submit_args = [
		'submit_label' => $submit_label,
	];

	// Render submit button.
	Template::render( 'registrant/form-submit', $submit_args, true );

	/**
	 * Wacara after registrant form content hook.
	 *
	 * @param Registrant $registrant the object of the current registrant.
	 *
	 * @hooked registrant_form_closing_callback - 50
	 */
	do_action( 'wacara_after_registrant_form_content', $registrant );

} elseif ( 'hold' === $reg_status ) {

	/**
	 * Wacara before registrant hold content hook.
	 *
	 * @param Registrant $registrant object of the current registrant.
	 * @param Payment_Method|bool|mixed $payment_class object of the selected payment method.
	 * @param string $reg_status status of the current registrant.
	 *
	 * @hooked UI::registrant_hold_opening_callback - 10
	 * @hooked UI::registrant_hold_opening_field_callback - 20
	 */
	do_action( 'wacara_before_registrant_hold_content', $registrant, $payment_class, $reg_status );

	// Make sure payment class is available.
	if ( $payment_class ) {

		// Render the content.
		$payment_class->render();
	}

	/**
	 * Wacara after registrant hold content hook.
	 *
	 * @param Registrant $registrant object of the current registrant.
	 * @param Payment_Method|bool|mixed $payment_class object of the selected payment method.
	 * @param string $reg_status status of the current registrant.
	 *
	 * @hooked UI::registrant_hold_closing_field_callback - 30
	 * @hooked UI::registrant_hold_submit_button_callback - 40
	 * @hooked UI::registrant_hold_closing_callback - 50
	 */
	do_action( 'wacara_after_registrant_hold_content', $registrant, $payment_class, $reg_status );

} elseif ( 'done' === $reg_status ) {

	/**
	 * Wacara before registrant success content hook.
	 *
	 * @param Registrant $registrant object of the current registrant.
	 */
	do_action( 'wacara_before_registrant_success_content', $registrant );

	// Render the content for success page.
	$alert_message = __( 'We have recorded your registration, please check your email for the further detail', 'wacara' );

	/**
	 * Wacara registrant success description filter hook.
	 *
	 * @param string $alert_message current success message.
	 * @param Registrant $registrant the object of the current registrant.
	 */
	$alert_message = apply_filters( 'wacara_filter_registrant_success_desc', $alert_message, $registrant );

	$success_args = [
		'alert_message' => $alert_message,
	];

	// Render the success template.
	Template::render( 'registrant/status-done', $success_args, true );

	/**
	 * Wacara after registrant success content hook.
	 *
	 * @param Registrant $registrant object of the current registrant.
	 */
	do_action( 'wacara_after_registrant_success_content', $registrant );

} else {

	/**
	 * Wacara before registrant custom content hook.
	 *
	 * @param Registrant $registrant object of the current registrant.
	 * @param Payment_Method|bool|mixed $payment_class object of the selected payment method.
	 * @param string $reg_status status of the current registrant.
	 *
	 * @hooked UI::registrant_hold_opening_callback - 10
	 */
	do_action( 'wacara_before_registrant_custom_content', $registrant, $payment_class, $reg_status );

	// Make sure payment class is available.
	if ( $payment_class ) {
		$payment_class->render_custom_content( $registrant, $payment_class, $reg_status );
	}

	/**
	 * Wacara after registrant custom content hook.
	 *
	 * @param Registrant $registrant object of the current registrant.
	 * @param Payment_Method|bool|mixed $payment_class object of the selected payment method.
	 * @param string $reg_status status of the current registrant.
	 *
	 * @hooked UI::registrant_hold_submit_button_callback - 40
	 * @hooked UI::registrant_hold_closing_callback - 50
	 */
	do_action( 'wacara_after_registrant_custom_content', $registrant, $payment_class, $reg_status );
}

/**
 * Wacara after registrant content hook.
 *
 * @param Registrant $registrant the object of the current registrant.
 *
 * @hooked UI::registrant_section_closing_callback - 50
 */
do_action( 'wacara_after_registrant_content', $registrant );
