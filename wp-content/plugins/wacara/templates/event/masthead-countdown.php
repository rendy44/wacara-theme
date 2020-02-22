<?php
/**
 * Custom template for displaying countdown in masthead.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wcr-event-counter-wrapper" data-target="<?php echo esc_attr( $date_start ); ?>">
	<div class="wcr-event-counter-item wcr-event-counter-day">
		<span class="wcr-event-count-value">99</span>
		<span class="wcr-event-count-label"><?php echo esc_html_x( 'Day', 'Days of the countdown', 'wacara' ); ?></span>
	</div>
	<div class="wcr-event-counter-item wcr-event-counter-hour">
		<span class="wcr-event-count-value">99</span>
		<span class="wcr-event-count-label"><?php echo esc_html_x( 'Hour', 'Hours of the countdown', 'wacara' ); ?></span>
	</div>
	<div class="wcr-event-counter-item wcr-event-counter-minute">
		<span class="wcr-event-count-value">99</span>
		<span class="wcr-event-count-label"><?php echo esc_html_x( 'Min', 'Minutes of countdown', 'wacara' ); ?></span>
	</div>
	<div class="wcr-event-counter-item wcr-event-counter-second">
		<span class="wcr-event-count-value">99</span>
		<span class="wcr-event-count-label"><?php echo esc_html_x( 'Sec', 'Seconds of countdown', 'wacara' ); ?></span>
	</div>
</div>
