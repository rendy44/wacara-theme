<?php
/**
 * Template for displaying single registrant content.
 *
 * @author Rendy
 * @package Wacara
 * @version 0.0.1
 */

use Wacara\Helper;
use Wacara\Register_Payment;
use Wacara\Registrant;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Fetch registrant object.
$registrant = new Registrant( get_the_ID() );

// Fetch registration status.
$reg_status = $registrant->get_registration_status();

// Fetch event detail.
$event_id = $registrant->get_event_info();

// Fetch invoice detail.
$invoice = $registrant->get_invoicing_info();

/**
 * Wacara before registrant masthead hook.
 *
 * @param Registrant $registrant the object of the current registrant.
 *
 * @hooked registrant_masthead_opening_callback - 10
 */
do_action( 'wacara_before_registrant_masthead', $registrant );

/**
 * Wacara registrant masthead hook.
 *
 * @param Registrant $registrant the object of the current registrant.
 * @param string $reg_status the status of the registrant.
 *
 * @hooked registrant_masthead_content_callback - 10
 */
do_action( 'wacara_registrant_masthead', $registrant, $reg_status );

/**
 * Wacara after registrant masthead hook.
 *
 * @param Registrant $registrant the object of the current registrant.
 *
 * @hooked registrant_masthead_closing_callback - 50
 */
do_action( 'wacara_after_registrant_masthead', $registrant );

/**
 * Wacara before registrant content hook.
 *
 * @param Registrant $registrant the object of the current registrant.
 *
 * @hooked registrant_section_opening_callback - 10
 */
do_action( 'wacara_before_registrant_content', $registrant );

// Check whether registrant is complete or not.
if ( 'done' === $reg_status ) {

	/**
	 * Wacara registrant done or success content hook.
	 *
	 * @param Registrant $registrant the object of the current registrant.
	 */
	do_action( 'wacara_registrant_done_content', $registrant );

} else {

	/**
	 * Wacara before registrant form content hook.
	 *
	 * @param Registrant $registrant the object of the current registrant.
	 */
	do_action( 'wacara_before_registrant_form_content', $registrant );
	?>

	<form class="wcr-form wcr-registrant-form" id="wcr-registrant-form-<?php echo esc_attr( $registrant->post_id ); ?>" method="post">
		<?php
		$used_fields = [ 'name', 'email', 'company', 'position', 'id_number', 'phone' ];

		// Render all the fields.
		foreach ( $used_fields as $field ) {
			echo apply_filters( 'wacara_input_field_event', $event_id, $field ); // phpcs:ignore
		}

		/**
		 * Wacara before registrant payment method form hook.
		 *
		 * @param Registrant $registrant the object of the current registrant.
		 */
		do_action( 'wacara_before_registrant_payment_method_form', $registrant );

		// Check whether payment is required or not.
		$validate_pricing = Helper::is_pricing_valid( $invoice['pricing_id'], true );
		if ( $validate_pricing->success ) {

			// Fetch all available payment methods.
			$payment_methods = Register_Payment::get_registered();

			if ( ! empty( $payment_methods ) ) {
				?>
				<div class="wcr-field-payment-method wcr-form-field-wrapper">
					<label><?php esc_html_e( 'Payment Method', 'wacara' ); ?></label>
					<?php
					foreach ( $payment_methods as $payment_method ) {
						?>
						<div class="wcr-form-field-multi-radio-wrapper">
							<input type="radio" class="wcr-form-field" id="payment_<?php echo esc_attr( $payment_method->id ); ?>" name="payment_method" value="<?php echo esc_attr( $payment_method->id ); ?>">
							<label for="payment_<?php echo esc_attr( $payment_method->id ); ?>"><?php echo esc_html( $payment_method->name ); ?></label>
						</div>
						<?php
					}
					?>
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

		// Render current registrant id.
		echo apply_filters( 'wacara_input_field', 'registrant_id', 'hidden', '', $registrant->post_id ); // phpcs:ignore

		// Add nonce.
		wp_nonce_field( 'wacara_nonce', 'wacara_payment' );

		?>
		<div class="wcr-form-disclaimer wcr-registrant-form-disclaimer-wrapper">
			<p class="wcr-registrant-form-disclaimer">
				<?php esc_html_e( 'By clicking register, you are automatically agree to our term of service', 'wacara' ); ?>
			</p>
		</div>
		<div class="wcr-form-submit wcr-registrant-form-submit-wrapper">
			<button type="submit" class="wcr-form-submit wcr-registrant-form-submit">
				<?php esc_html_e( 'Register', 'wacara' ); ?>
			</button>
		</div>
	</form>

	<?php
	/**
	 * Wacara after registrant form content hook.
	 *
	 * @param Registrant $registrant the object of the current registrant.
	 */
	do_action( 'wacara_after_registrant_form_content', $registrant );

}

/**
 * Wacara after registrant content hook.
 *
 * @param Registrant $registrant the object of the current registrant.
 *
 * @hooked registrant_section_closing_callback - 50
 */
do_action( 'wacara_after_registrant_content', $registrant );
