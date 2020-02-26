<?php
/**
 * Template for displaying failed page in single registration.
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wcr-alert wcr-alert-danger">
	<p><?php echo esc_html( $alert_message ); ?></p>
</div>
