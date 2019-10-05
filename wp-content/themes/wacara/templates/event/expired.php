<?php
/**
 * Custom template for rendering expired event..
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<section class="bg-light expired" data-aos="zoom-in">
	<div class="container h-100">
		<div class="row h-100 align-items-center">
			<div class="col-lg-8 mx-auto text-center mb-3">
				<h2 class="section-heading" data-aos="fade-left" data-aos-delay="200"><?php esc_html_e( 'Expired', 'wacara' ); ?></h2>
				<p class="lead" data-aos="fade-right" data-aos-delay="400"><?php esc_html_e( 'Sorry this event already past', 'wacara' ); ?></p>
			</div>
		</div>
	</div>
</section>
