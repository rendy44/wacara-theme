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
		<h3 class="wcr-registrant-name"><?php echo esc_html( $registrant_name ); ?></h3>
	</div>
	<div class="wcr-registrant-email-wrapper">
		<h4 class="wcr-registrant-email"><?php echo esc_html( $registrant_email ); ?></h4>
	</div>
</div>
