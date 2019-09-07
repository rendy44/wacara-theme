<?php
/**
 * Custom template for displaying masthead in event landing
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<header class="masthead" id="masthead" data-aos="zoom-in">
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-lg-6">
                <h1 class="mb-4" data-aos="fade-right" data-aos-delay="300"><?php echo $title; // phpcs:ignore ?></h1>
                <p class="lead mb-4" data-aos="fade-left" data-aos-delay="500"><?php echo esc_html( $excerpt ); ?></p>
                <div class="countdown d-flex mb-4" id="countdown" data-date="<?php echo esc_attr( $date_start ); ?>">
                    <div class="time" data-aos="fade-up-right" data-aos-delay="700">
                        <span id="cd_d" class="value">00</span>
                        <span><?php echo esc_html__( 'Days', 'wacara' ); ?></span>
                    </div>
                    <div class="time pl-4" data-aos="fade-up-right" data-aos-delay="900">
                        <span id="cd_h" class="value">00</span>
                        <span><?php echo esc_html__( 'Hours', 'wacara' ); ?></span>
                    </div>
                    <div class="time pl-4" data-aos="fade-up-right" data-aos-delay="1100">
                        <span id="cd_m" class="value">00</span>
                        <span><?php echo esc_html__( 'Minutes', 'wacara' ); ?></span>
                    </div>
                    <div class="time pl-4" data-aos="fade-up-right" data-aos-delay="1300">
                        <span id="cd_s" class="value">00</span>
                        <span><?php echo esc_html__( 'Seconds', 'wacara' ); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
