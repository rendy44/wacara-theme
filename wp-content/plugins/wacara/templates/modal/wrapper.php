<?php
/**
 * Template for displaying base modal wrapper.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div id="wcr-modal-<?php echo esc_attr( $modal_id ); ?>" class="wcr-modal">
	<div class="wcr-modal-content-wrapper">
		<div class="wcr-modal-header">
			<span class="wcr-modal-close">&times;</span>
			<h2 class="wcr-modal-title"><?php echo esc_html( $modal_title ); ?></h2>
		</div>
		<div class="wcr-modal-body">
			<?php echo $modal_body; // phpcs:ignore ?>
		</div>

		<div class="wcr-modal-footer">
			<button class="wcr-button wcr-button-main wcr-modal-confirm"><?php esc_html_e( 'Confirm', 'wacara' ); ?></button>
		</div>
	</div>
</div>
