<?php
/**
 * Template for displaying waiting verification page in single registration.
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<section class="py-0" id="registration-form">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-md-10 mx-auto text-center">
				<p class="text-primary"><i class="fas fa-smile fa-5x"></i></p>
				<h2 class="mb-3"><?php esc_html_e( 'Thank You', 'wacara' ); ?></h2>
				<p class="lead"><?php esc_html_e( 'Thank you for confirming your payment, we will get back to you once we have verified your payment', 'wacara' ); ?></p>
			</div>
		</div>
	</div>
</section>
