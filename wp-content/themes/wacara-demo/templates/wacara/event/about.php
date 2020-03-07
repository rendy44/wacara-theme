<?php
/**
 * Custom template for overriding about section.
 *
 * @author  WPerfekt
 * @package Wacara_Theme
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wcr-section-content-wrapper">
	<div class="frow">
		<div class="col-sm-1-3">
			<div class="wcr-about-item-wrapper">
				<div class="wcr-about-icon-wrapper">
					<i class="ri-user-follow-fill wcr-about-icon"></i>
				</div>
				<div class="wcr-about-content-wrapper">
					<p class="wcr-about-content"><?php echo esc_html( $about_user ); ?></p>
				</div>
			</div>
		</div>
		<div class="col-sm-1-3">
			<div class="wcr-about-item-wrapper">
				<div class="wcr-about-icon-wrapper">
					<i class="ri-time-fill wcr-about-icon"></i>
				</div>
				<div class="wcr-about-content-wrapper">
					<p class="wcr-about-content"><?php echo esc_html( $about_time ); ?></p>
				</div>
			</div>
		</div>
		<div class="col-sm-1-3">
			<div class="wcr-about-item-wrapper">
				<div class="wcr-about-icon-wrapper">
					<i class="ri-map-pin-fill wcr-about-icon"></i>
				</div>
				<div class="wcr-about-content-wrapper">
					<p class="wcr-about-content"><?php echo esc_html( $about_location ); ?></p>
				</div>
			</div>
		</div>
	</div>
    <div class="frow">
        <div class="col-sm-1-3 wcr-text-center">
            <div class="wcr-section-about-cta-wrapper">
                <a href="<?php echo esc_attr($about_cta_url); ?>" target="_blank" class="wcr-button wcr-button-main wcr-section-about-cta"><?php esc_attr_e('Add To My Calendar','wacara'); ?></a>
            </div>
        </div>
    </div>
</div>
