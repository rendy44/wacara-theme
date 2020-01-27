<?php
/**
 * Custom template for displaying countdown in masthead.
 *
 * @author Rendy
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wcr-event-counter-wrapper" data-target="<?php echo esc_attr( $date_start ); ?>">
	<div class="wcr-event-counter-item wcr-event-counter-day">
		<span class="wcr-event-count-value">99</span>
		<span class="wcr-event-count-label">Days</span>
	</div>
	<div class="wcr-event-counter-item wcr-event-counter-hour">
		<span class="wcr-event-count-value">99</span>
		<span class="wcr-event-count-label">Hours</span>
	</div>
	<div class="wcr-event-counter-item wcr-event-counter-minute">
		<span class="wcr-event-count-value">99</span>
		<span class="wcr-event-count-label">Minutes</span>
	</div>
	<div class="wcr-event-counter-item wcr-event-counter-second">
		<span class="wcr-event-count-value">99</span>
		<span class="wcr-event-count-label">Seconds</span>
	</div>
</div>
