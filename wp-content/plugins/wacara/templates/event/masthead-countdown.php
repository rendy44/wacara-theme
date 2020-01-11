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

<div class="countdown d-flex mb-4" id="countdown" data-date="<?php echo esc_attr( $date_start ); ?>">
    <div class="time" data-aos="fade-up-right" data-aos-delay="700">
        <span id="cd_d" class="value">00</span>
        <span><?php esc_html_e( 'Days', 'wacara' ); ?></span>
    </div>
    <div class="time pl-4" data-aos="fade-up-right" data-aos-delay="900">
        <span id="cd_h" class="value">00</span>
        <span><?php esc_html_e( 'Hours', 'wacara' ); ?></span>
    </div>
    <div class="time pl-4" data-aos="fade-up-right" data-aos-delay="1100">
        <span id="cd_m" class="value">00</span>
        <span><?php esc_html_e( 'Minutes', 'wacara' ); ?></span>
    </div>
    <div class="time pl-4" data-aos="fade-up-right" data-aos-delay="1300">
        <span id="cd_s" class="value">00</span>
        <span><?php esc_html_e( 'Seconds', 'wacara' ); ?></span>
    </div>
</div>
