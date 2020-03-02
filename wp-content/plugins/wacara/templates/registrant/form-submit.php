<?php
/**
 * Template for displaying registrant form submit.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $submit_label ) {
	?>
	<div class="wcr-form-submit-wrapper">
		<button type="submit" class="wcr-button wcr-form-submit wcr-button-main"><?php echo esc_html( $submit_label ); ?></button>
	</div>
	<?php
}
