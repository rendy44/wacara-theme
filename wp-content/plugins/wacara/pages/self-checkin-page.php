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
 * Wacara before self checkin form hook.
 */
do_action( 'wacara_before_self_checkin_form' ); ?>

	<form id="wcr-form-self-checkin" class="wcr-form">
		<div class="wcr-login-field-wrapper">
			<input type="text" name="booking_code" class="wcr-login-field" placeholder="<?php esc_attr_e( 'Your booking code', 'wacara' ); ?>">
		</div>
		<div class="wcr-login-submit-wrapper">
			<button type="submit" class="wcr-button wcr-button-main wcr-login-button"><?php esc_html_e( 'Search', 'wacara' ); ?></button>
		</div>
	</form>

<?php
/**
 * Wacara after self checkin form hook.
 */
do_action( 'wacara_after_self_checkin_form' );

get_footer( 'wacara' );
