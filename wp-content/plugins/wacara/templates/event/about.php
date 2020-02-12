<?php
/**
 * Custom template for displaying about section in event landing
 *
 * @author  Rendy
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wcr-section-content-wrapper">
	<div class="frow">
		<div class="col-sm-4-10">
			<div class="wcr-about-item-wrapper">
				<div class="wcr-about-item-title-wrapper">
					<h4 class="wcr-about-item-title"><?php esc_html_e( 'What is it?', 'wacara' ); ?></h4>
				</div>
				<div class="wcr-about-item-content-wrapper">
					<p><?php echo esc_html( $description ); ?></p>
				</div>
			</div>
		</div>
		<div class="col-sm-3-10">
			<div class="wcr-about-item-wrapper">
				<div class="wcr-about-item-title-wrapper">
					<h4 class="wcr-about-item-title"><?php esc_html_e( 'Where?', 'wacara' ); ?></h4>
				</div>
				<div class="wcr-about-item-content-wrapper">
					<p><?php echo esc_html( $location ); ?></p>
				</div>
			</div>
		</div>
		<div class="col-sm-3-10">
			<div class="wcr-about-item-wrapper">
				<div class="wcr-about-item-title-wrapper">
					<h4 class="wcr-about-item-title"><?php esc_html_e( 'When?', 'wacara' ); ?></h4>
				</div>
				<div class="wcr-about-item-content-wrapper">
					<p><?php echo esc_html( $time ); ?></p>
				</div>
			</div>
		</div>
	</div>
</div>
