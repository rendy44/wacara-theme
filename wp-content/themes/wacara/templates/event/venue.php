<?php
/**
 * Custom template to render venue section in event landing.
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<section class="<?php echo esc_attr( $class ); ?> venue" id="venue" data-aos="zoom-in">
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
		<div class="row">
			<div class="col-lg-6 col-map">
				<div class="embed-responsive" data-aos="zoom-in" data-aos-delay="600">
					<iframe class="embed-responsive-item" src="<?php echo esc_url( "https://maps.google.com/maps?q={$location_name}&amp;t=&amp;z=15&amp;ie=UTF8&amp;iwloc=&amp;output=embed" ); ?>"></iframe>
				</div>
			</div>
			<div class="col-lg-6 d-flex flex-column">
				<h3 data-aos="fade-left" data-aos-delay="800"><?php echo esc_html( $location_name ); ?></h3>
				<p data-aos="fade-left" data-aos-delay="1000"><?php echo esc_html( $location_description ); ?></p>
				<div class="carousel slide" id="location_images" data-ride="carousel" data-aos="fade-left" data-aos-delay="1200">
					<div class="carousel-inner">
						<?php
						$slide_num = 0;
						foreach ( $sliders as $image_id => $image_url ) {
							?>
							<div class="carousel-item <?php echo 0 === $slide_num ? esc_attr( 'active' ) : ''; ?>">
								<img src="<?php echo esc_attr( wp_get_attachment_image_url( $image_id, 'large' ) ); ?>" alt="">
							</div>
							<?php
							$slide_num ++;
						}
						?>
					</div>
					<!-- Left and right controls-->
					<a class="carousel-control-prev" href="#location_images" data-slide="prev">
						<span class="carousel-control-prev-icon"></span>
					</a>
					<a class="carousel-control-next" href="#location_images" data-slide="next">
						<span class="carousel-control-next-icon"></span>
					</a>
				</div>
			</div>
		</div>
	</div>
</section>

