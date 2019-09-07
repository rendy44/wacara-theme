<?php
/**
 * Custom template for displaying about section in event landing
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<section class="bg-light info" id="about" data-aos="zoom-in">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-12 col-lg-5 info-item" data-aos="fade-left" data-aos-delay="200">
				<i class="fa fa-volume-up fa-3x text-primary"></i>
				<h3><?php echo esc_html__( 'What is all about?', 'wacara' ); ?></h3>
				<p><?php echo esc_html( $description ); ?></p>
			</div>
			<div class="col-md-6 col-lg-4 info-item" data-aos="fade-left" data-aos-delay="400">
				<i class="fa fa-map-marker-alt fa-3x text-primary"></i>
				<h3><?php echo esc_html__( 'Venue', 'wacara' ); ?></h3>
				<p><?php echo esc_html( $location ); ?></p>
			</div>
			<div class="col-md-6 col-lg-3 info-item" data-aos="fade-left" data-aos-delay="600">
				<i class="fa fa-calendar-alt fa-3x text-primary"></i>
				<h3><?php echo esc_html__( 'When', 'wacara' ); ?></h3>
				<p><?php echo esc_html( $time ); ?></p>
			</div>
		</div>
	</div>
</section>
