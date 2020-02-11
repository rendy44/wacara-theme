<?php
/**
 * Template for displaying success message after registration.
 *
 * @author  Rendy
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wcr-alert wcr-alert-success">
	<p><?php echo esc_html( $alert_message ); ?></p>
</div>
