<?php
/**
 * Custom template for displaying direct registration section.
 *
 * @author  Rendy
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="frow">
	<div class="col-2-3">
		<div class="wcr-event-direct-wrapper">
			<div class="wcr-alert wcr-alert-success">
				<p><?php echo esc_html( $direct_message ); ?></p>
			</div>
		</div>
	</div>
</div>
