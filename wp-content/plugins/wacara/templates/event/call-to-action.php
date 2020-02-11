<?php
/**
 * Template for displaying call to action.
 *
 * @author Rendy
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>


<div class="wcr-event-alert-block-wrapper wcr-event-alert-title-wrapper">
	<span class="wcr-event-alert-title"><?php echo esc_html( $alert_title ); ?></span>
</div>
<div class="wcr-event-alert-block-wrapper wcr-event-alert-content-wrapper">
	<span class="wcr-event-alert-content"><?php echo esc_html( $alert_content ); ?></span>
</div>
<div class="wcr-event-alert-block-wrapper wcr-event-alert-cta-wrapper">
	<a class="wcr-event-alert-cta" href="#wcr-section-pricing"><?php echo esc_html( $alert_button ); ?></a>
</div>
