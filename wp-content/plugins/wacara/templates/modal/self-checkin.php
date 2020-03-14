<?php
/**
 * Template for displaying registrant info in modal.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wcr-registrant-detail-wrapper">
	<p><?php echo esc_html( $registrant_text ); ?></p>
	<div class="wcr-registrant-name-wrapper">
		<p class="wcr-registrant-name"><?php echo esc_html( $registrant_name ); ?></p>
	</div>
	<div class="wcr-registrant-email-wrapper">
		<p class="wcr-registrant-email"><?php echo esc_html( $registrant_email ); ?></p>
	</div>
</div>
