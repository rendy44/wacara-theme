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
					<input class="btn btn-primary btn-lg btn-submit-reg" type="submit" value="<?php echo esc_attr__( 'Register', 'wacara' ); ?>">
				</form>
			</div>
		</div>
	</div>
</section>
