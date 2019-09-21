<?php
/**
 * Custom template for rendering direct registration section.
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<section class="bg-light pricing" id="register" data-aos="zoom-in">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 mx-auto text-center mb-3">
				<h2 class="section-heading" data-aos="fade-left" data-aos-delay="200"><?php echo esc_html__( 'Register', 'wacara' ); ?></h2>
				<p class="lead" data-aos="fade-right" data-aos-delay="400"><?php echo esc_html__( 'Go to the event directly', 'wacara' ); ?></p>
				<p class="text-success"><i class="fa fa-check-circle fa-5x"></i></p>
				<p class="lead"><?php echo esc_html__( 'This event does not require any registration, you can attend the event immediately', 'wacara' ); ?></p>
			</div>
		</div>
	</div>
</section>
