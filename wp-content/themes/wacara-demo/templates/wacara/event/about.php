<?php
/**
 * Custom template for displaying about section in event landing
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wcr-section-content-wrapper">
	<div class="frow wcr-justify-content-start">
		<div class="col-xs-1-1 col-md-1-12"></div>
		<div class="col-sm-1-1 col-sm-3-5 col-md-5-12">
			<div class="wcr-section-content-inner-wrapper">
				<div class="wcr-about-title-wrapper">
					<h4 class="wcr-about-title"><?php echo esc_html( $title ); ?></h4>
				</div>
				<div class="wcr-about-content-wrapper">
					<p class="wcr-about-content"><?php echo esc_html( $description ); ?></p>
					<div class="wcr-about-content-details-wrapper">
						<div class="wcr-about-content-detail-wrapper">
							<div class="frow">
								<div class="col-xs-1-12">
									<div class="wcr-about-icon-detail-wrapper">
										<i class="ri-time-line wcr-about-item-icon"></i>
									</div>
								</div>
								<div class="col-xs-11-12">
									<div class="wcr-about-value-detail-wrapper">
										<p><?php echo esc_html( $time ); ?></p>
									</div>
								</div>
							</div>
						</div>
						<div class="wcr-about-content-detail-wrapper">
							<div class="frow">
								<div class="col-xs-1-12">
									<div class="wcr-about-icon-detail-wrapper">
										<i class="ri-map-pin-line wcr-about-item-icon"></i>
									</div>
								</div>
								<div class="col-xs-11-12">
									<div class="wcr-about-value-detail-wrapper">
										<p><?php echo esc_html( $location ); ?></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
