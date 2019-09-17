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
					// Maybe render name field.
					echo apply_filters( 'sk_input_field_event', $event_id, 'name' ); // phpcs:ignore
					// Maybe render email field.
					echo apply_filters( 'sk_input_field_event', $event_id, 'email' ); // phpcs:ignore
					// Maybe render company field.
					echo apply_filters( 'sk_input_field_event', $event_id, 'company' ); // phpcs:ignore
					// Maybe render position field.
					echo apply_filters( 'sk_input_field_event', $event_id, 'position' ); // phpcs:ignore
					// Maybe render id number field.
					echo apply_filters( 'sk_input_field_event', $event_id, 'id_number' ); // phpcs:ignore
					// Maybe render phone field.
					echo apply_filters( 'sk_input_field_event', $event_id, 'phone' ); // phpcs:ignore

					// Check whether use payment or not.
					if ( $use_payment ) {
						?>
						<div class="form-group">
							<label for="card"><?php echo esc_html__( 'Credit card information', 'wacara' ); ?></label>
							<div id="card" class="form-control form-control-lg"></div>
						</div>
						<?php
					}
					?>
					<p><?php echo esc_html__( 'By clicking register, you are automatically agree to our term of service', 'wacara' ); ?></p>
					<input class="btn btn-primary btn-lg btn-submit-reg" type="submit" value="<?php echo esc_attr__( 'Register', 'wacara' ); ?>">
				</form>
			</div>
		</div>
	</div>
</section>
