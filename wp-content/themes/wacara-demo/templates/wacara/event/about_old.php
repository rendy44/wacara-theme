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
		<div class="col-sm-1-1 col-md-3-9 wcr-align-items-center">
			<img src="<?php echo esc_attr( WACARA_MAYBE_THEME_URI . '/assets/img/illustration/events.svg' ); ?>">
		</div>
		<div class="col-sm-1-1 col-md-4-9">
			<div class="wcr-about-item-wrapper">
				<div class="frow">
					<div class="col-xs-1-12">
						<div class="wcr-about-item-icon-wrapper">
							<i class="ri-information-line wcr-about-item-icon"></i>
						</div>
					</div>
					<div class="col-xs-11-12">
						<div class="wcr-about-item-title-wrapper">
							<h4 class="wcr-about-item-title"><?php esc_html_e( 'What is it?', 'wacara' ); ?></h4>
						</div>
						<div class="wcr-about-item-content-wrapper">
							<p><?php echo esc_html( $description ); ?></p>
						</div>
					</div>
				</div>
			</div>
			<div class="wcr-about-item-wrapper">
				<div class="frow">
					<div class="col-xs-1-12">
						<div class="wcr-about-item-icon-wrapper">
							<i class="ri-map-pin-line wcr-about-item-icon"></i>
						</div>
					</div>
					<div class="col-xs-11-12">
						<div class="wcr-about-item-title-wrapper">
							<h4 class="wcr-about-item-title"><?php esc_html_e( 'Where?', 'wacara' ); ?></h4>
						</div>
						<div class="wcr-about-item-content-wrapper">
							<p><?php echo esc_html( $location ); ?></p>
						</div>
					</div>
				</div>
			</div>
			<div class="wcr-about-item-wrapper">
				<div class="frow">
					<div class="col-xs-1-12">
						<div class="wcr-about-item-icon-wrapper">
							<i class="ri-time-line wcr-about-item-icon"></i>
						</div>
					</div>
					<div class="col-xs-11-12">
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
	</div>
</div>
