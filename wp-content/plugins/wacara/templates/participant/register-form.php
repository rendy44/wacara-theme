<?php
/**
 * Template for rendering registration form in single registration.
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<section class="py-0" id="registration-form">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-md-10 mx-auto">
				<?php
				if ( $maybe_auto_error_message && isset( $maybe_auto_error_message ) ) {
					?>
					<div class="alert alert-warning mb-5">
						<strong><?php esc_html_e( 'Last Error:', 'wacara' ); ?></strong><br/>
						<?php echo esc_html( $maybe_auto_error_message ); ?>
					</div>
					<?php
				}
				?>
				<form id="frm_register" method="post">
					<?php
					$fields = [ 'name', 'email', 'company', 'position', 'id_number', 'phone' ];

					foreach ( $fields as $field ) {
						echo apply_filters( 'sk_input_field_event', $event_id, $field ); // phpcs:ignore
					}

					/**
					 * Perform action before rendering payment field..
					 *
					 * @param string $event_id event id.
					 */
					do_action( 'wacara_before_rendering_payment_field', $event_id );

					// Check whether use payment or not.
					if ( $use_payment ) {
						?>
						<div class="form-group" id="payment_methods">
							<label><?php esc_html_e( 'Payment Method', 'wacara' ); ?></label>
							<?php
							// Get available payment methods.
							if ( $payment_methods ) {
								foreach ( $payment_methods as $payment_method ) {
									?>
									<div class="custom-control custom-radio">
										<input type="radio" class="custom-control-input" id="payment_<?php echo esc_attr( $payment_method->id ); ?>" name="payment_method" value="<?php echo esc_attr( $payment_method->id ); ?>">
										<label class="custom-control-label" for="payment_<?php echo esc_attr( $payment_method->id ); ?>"><?php echo esc_html( $payment_method->name ); ?></label>
									</div>
									<?php
								}
							} else {
								?>
								<div class="alert alert-danger">
									<?php esc_html_e( 'No payment methods available', 'wacara' ); ?>
								</div>
								<?php
							}
							?>
						</div>

						<?php
						// Render the front-end ui of payment methods.
						if ( $payment_methods ) {
							foreach ( $payment_methods as $payment_method ) {
								?>
								<div class="form-group individual_payment_method" id="<?php echo esc_attr( $payment_method->id ); ?>_payment_method">
									<?php $payment_method->render(); ?>
								</div>
								<?php
							}
						}
					}

					/**
					 * Perform action after rendering register form.
					 *
					 * @param string $event_id event id.
					 */
					do_action( 'wacara_after_rendering_register_form', $event_id );

					// Render current participant id.
					echo apply_filters( 'sk_input_field', 'participant_id', 'hidden', '', $id ); // phpcs:ignore

					// Add nonce.
					wp_nonce_field( 'sk_nonce', 'sk_payment' );
					?>
					<p><?php esc_html_e( 'By clicking register, you are automatically agree to our term of service', 'wacara' ); ?></p>
					<button class="btn btn-primary btn-lg btn-submit-reg"
							type="submit"><?php esc_html_e( 'Register', 'wacara' ); ?></button>
				</form>
			</div>
		</div>
	</div>
</section>
