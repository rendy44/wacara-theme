<?php
/**
 * Custom template for displaying masthead in event landing
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wcr-event-title-wrapper">
	<h1 class="wcr-event-title"><?php echo esc_html( $header_title ); ?></h1>
</div>
<div class="wcr-event-subtitle-wrapper">
	<h2 class="wcr-event-subtitle"><?php echo esc_html( $header_headline ); ?></h2>
</div>
<div class="wcr-event-highlight-wrapper">
	<p class="wcr-event-highlight"><?php echo esc_html( $header_excerpt ); ?></p>
</div>
