<?php
/**
 * Template for displaying expired event.
 *
 * @author WPerfekt
 * @package Wacara_Theme
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="frow wcr-height-100-p">
	<div class="col-md-2-3 wcr-text-center">
		<div class="wcr-event-expired-wrapper">
			<div class="wcr-expired-icon-wrapper">
				<i class="ri-file-forbid-line ri-4x wcr-expired-icon wcr-text-danger"></i>
			</div>
			<div class="wcr-expired-content-wrapper">
				<h2 class="wcr-expired-title-wrapper"><?php echo esc_html( $exp_title ); ?></h2>
				<p class="wcr-expired-content-wrapper"><?php echo esc_html( $exp_content ); ?></p>
			</div>
		</div>
	</div>
</div>
