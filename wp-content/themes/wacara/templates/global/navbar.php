<?php
/**
 * Custom template for displaying top navbar
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<nav class="navbar navbar-expand-lg navbar-light fixed-top">
	<div class="container">
		<a class="navbar-brand scroll" href="#masthead">
			<img src="<?php echo esc_attr( TEMP_URI . '/assets/img/sample-logo.png' ); ?>" alt="">
		</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive"
				aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span
					class="navbar-toggler-icon"></span></button>
		<div class="collapse navbar-collapse" id="navbarResponsive">
			<ul class="navbar-nav ml-auto">
				<li class="nav-item">
					<a class="nav-link scroll" href="#about"><?php echo esc_html__( 'What is it?', 'wacara' ); ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link scroll" href="#speakers"><?php echo esc_html__( 'Speakers', 'wacara' ); ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link scroll" href="#venue"><?php echo esc_html__( 'Venue', 'wacara' ); ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link scroll" href="#schedule"><?php echo esc_html__( 'Schedule', 'wacara' ); ?></a>
				</li>
				<li class="nav-item register">
					<a class="nav-link scroll" href="#register"><?php echo esc_html__( 'Register Now', 'wacara' ); ?></a>
				</li>
			</ul>
		</div>
	</div>
</nav>
