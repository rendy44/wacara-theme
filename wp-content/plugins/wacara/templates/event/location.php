<?php
/**
 * Custom template to display location section.
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wcr-section-content-wrapper">
    <div class="wcr-section-content-inner-wrapper">
        <div class="frow">
            <div class="col-sm-2-3">
                <div class="wcr-location-images-wrapper">
                    <div class="frow">
						<?php
						if ( ! empty( $location_sliders ) ) {
							foreach ( $location_sliders as $img_id => $img_url ) {
								?>
                                <div class="col-xs-1-1 col-sm-1-2 col-md-1-3">
                                    <div class="wcr-location-image-wrapper">
                                        <img src="<?php echo esc_attr( wp_get_attachment_image_url( $img_id, 'thumbnail' ) ); ?>" class="wcr-location-image" alt="">
                                    </div>
                                </div>
								<?php
							}
						}
						?>
                    </div>
                </div>
            </div>
            <div class="col-sm-1-3">
                <div class="wcr-location-content-wrapper">
                    <div class="wcr-location-title-wrapper">
                        <h4 class="wcr-location-title"><?php echo esc_html( $location_name ); ?></h4>
                    </div>
                    <div class="wcr-location-description-wrapper">
                        <p class="wcr-location-description"><?php echo esc_html( $location_description ); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="wcr-location-map-wrapper">
        <iframe class="wcr-location-map" src="<?php echo esc_url( "https://maps.google.com/maps?q={$location_name}&amp;t=&amp;z=15&amp;ie=UTF8&amp;iwloc=&amp;output=embed" ); ?>"></iframe>
    </div>
</div>

