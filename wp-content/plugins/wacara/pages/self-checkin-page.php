<?php
/**
 * Template Name: Self-checkin template
 *
 * @author WPerfekt
 * @package Wacara_Theme
 * @version 0.0.1
 */

get_header( 'wacara' );

/**
 * Wacara before self checkin content hook.
 */
do_action( 'wacara_before_self_checkin_content' ); ?>

	<div class="wcr-form-self-checkin-wrapper">

		<?php
		/**
		 * Wacara before self checkin form hook.
		 */
		do_action( 'wacara_before_self_checkin_form' );
		?>

		<form id="wcr-form-self-checkin" class="wcr-form" method="post">
			<div class="wcr-field-booking-code wcr-form-field-wrapper">
				<input type="text" name="booking_code" class="wcr-form-field" placeholder="<?php esc_attr_e( 'Your booking code', 'wacara' ); ?>">
			</div>
			<div class="wcr-form-submit-wrapper">
				<button type="submit" class="wcr-button wcr-button-main wcr-login-button"><?php esc_html_e( 'Search', 'wacara' ); ?></button>
			</div>
		</form>

		<?php
		/**
		 * Wacara after self checkin form hook.
		 */
		do_action( 'wacara_after_self_checkin_form' );
		?>

	</div>

<?php
/**
 * Wacara after self checkin form hook.
 *
 * @hooked UI::modal_checkin_callback - 10
 */
do_action( 'wacara_after_self_checkin_content' );

get_footer( 'wacara' );
