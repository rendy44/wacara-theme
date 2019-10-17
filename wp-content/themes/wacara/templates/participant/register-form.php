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
				if ( $stripe_error_message && isset( $stripe_error_message ) ) {
					?>
					<div class="alert alert-warning mb-5">
						<strong><?php esc_html_e( 'Last Error:', 'wacara' ); ?></strong><br/>
						<?php echo esc_html( $stripe_error_message ); ?>
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
							$payment_methods = [
								'manual' => __( 'Manual bank transfer', 'wacara' ),
								'stripe' => __( 'Stripe', 'wacara' ),
							];

							foreach ( $payment_methods as $payment_key => $payment_label ) {
								?>
								<div class="custom-control custom-radio">
									<input type="radio" class="custom-control-input" id="payment_<?php echo esc_attr( $payment_key ); ?>" name="payment_method" value="<?php echo esc_attr( $payment_key ); ?>">
									<label class="custom-control-label" for="payment_<?php echo esc_attr( $payment_key ); ?>"><?php echo esc_html( $payment_label ); ?></label>
								</div>
								<?php
							}
							?>
						</div>
						<div class="form-group individual_payment_method" id="manual_payment_method">
							<div class="alert alert-info">
								<?php esc_html_e( 'Bank detail will be informed after making registration', 'wacara' ); ?>
							</div>
						</div>
						<div class="form-group individual_payment_method" id="stripe_payment_method">
							<label for="card"><?php esc_html_e( 'Credit card information', 'wacara' ); ?></label>
							<div id="card" class="form-control form-control-lg"></div>
						</div>
						<?php
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
					<button class="btn btn-primary btn-lg btn-submit-reg" type="submit"><?php esc_html_e( 'Register', 'wacara' ); ?></button>
				</form>
			</div>
		</div>
	</div>
</section>
