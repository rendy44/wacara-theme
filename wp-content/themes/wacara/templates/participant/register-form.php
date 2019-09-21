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
						<strong><?php echo esc_html__( 'Last Error:', 'wacara' ); ?></strong><br/>
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

					// Check whether use payment or not.
					if ( $use_payment ) {
						?>
						<div class="form-group">
							<label for="card"><?php echo esc_html__( 'Credit card information', 'wacara' ); ?></label>
							<div id="card" class="form-control form-control-lg"></div>
						</div>
						<?php
					}

					// Render current registration id.
					echo apply_filters( 'sk_input_field', 'registration_id', 'hidden', '', $id ); // phpcs:ignore

					// Add nonce.
					wp_nonce_field( 'sk_nonce', 'sk_payment' );
					?>
					<p><?php echo esc_html__( 'By clicking register, you are automatically agree to our term of service', 'wacara' ); ?></p>
					<button class="btn btn-primary btn-lg btn-submit-reg" type="submit"><?php echo esc_html__( 'Register', 'wacara' ); ?></button>
				</form>
			</div>
		</div>
	</div>
</section>
