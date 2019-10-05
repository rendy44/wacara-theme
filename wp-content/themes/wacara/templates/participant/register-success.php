<?php
/**
 * Template for rendering success message after registration.
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
				<p class="text-success"><i class="fa fa-check-circle fa-5x"></i></p>
				<h2 class="mb-3"><?php esc_html_e( 'Congratulation', 'wacara' ); ?></h2>
				<p class="lead"><?php esc_html_e( 'We have recorded your registration, please check your email for the further detail', 'wacara' ); ?></p>
			</div>
		</div>
	</div>
</section>
