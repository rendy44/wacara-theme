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

<section class="<?php echo esc_attr( $class ); ?> info" id="about" data-aos="zoom-in">
	<div class="container">
		<?php
		if ( $title || $subtitle ) {
			?>
			<div class="row">
				<div class="col-lg-8 mx-auto text-center mb-3">
					<h2 class="section-heading" data-aos="fade-left" data-aos-delay="200"><?php echo esc_html( $title ); ?></h2>
					<p class="lead" data-aos="fade-right" data-aos-delay="400"><?php echo esc_html( $subtitle ); ?></p>
				</div>
			</div>
			<?php
		}
		?>
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
