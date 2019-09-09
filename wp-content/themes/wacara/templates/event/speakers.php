<?php
/**
 * Custom template to display speakers section in landing event.
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<section class="speakers" id="speakers" data-aos="zoom-in">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 mx-auto text-center mb-3">
				<h2 class="section-heading" data-aos="fade-left" data-aos-delay="200">Meet The Speakers</h2>
				<p class="lead" data-aos="fade-right" data-aos-delay="400">These are amazing people who will share their
					thoughts to us</p>
			</div>
		</div>
		<div class="row justify-content-center">
			<?php
			foreach ( $speakers as $speaker ) {
				?>
				<div class="col-lg-4 col-md-6 mb-4 speaker-item">
					<div class="card border-0 shadow" data-aos="fade-up" data-aos-delay="600">
						<img class="card-img-top" src="<?php echo esc_attr( $speaker['image'] ); ?>" alt="<?php echo esc_attr( $speaker['name'] ); ?>">
						<div class="card-body text-center">
							<h5 class="card-title mb-0"><?php echo esc_html( $speaker['name'] ); ?></h5>
							<div class="card-text"><?php echo esc_html( $speaker['position'] ); ?></div>
							<div class="card-social">
								<?php
								if ( ! empty( $speaker['website'] ) ) {
									echo '<a href="' . $speaker['website'] . '" target="_blank"><i class="fas fa-globe"></i></a>';
								}
								if ( ! empty( $speaker['facebook'] ) ) {
									echo '<a href="' . $speaker['facebook'] . '" target="_blank"><i class="fab fa-facebook"></i></a>';
								}
								if ( ! empty( $speaker['linkedin'] ) ) {
									echo '<a href="' . $speaker['linkedin'] . '" target="_blank"><i class="fab fa-linkedin"></i></a>';
								}
								if ( ! empty( $speaker['twitter'] ) ) {
									echo '<a href="' . $speaker['twitter'] . '" target="_blank"><i class="fab fa-twitter"></i></a>';
								}
								if ( ! empty( $speaker['instagram'] ) ) {
									echo '<a href="' . $speaker['instagram'] . '" target="_blank"><i class="fab fa-instagram"></i></a>';
								}
								if ( ! empty( $speaker['youtube'] ) ) {
									echo '<a href="' . $speaker['youtube'] . '" target="_blank"><i class="fab fa-youtube"></i></a>';
								}
								?>
<!--                                <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>-->
<!--                                <a href="#" target="_blank"><i class="fab fa-linkedin-in"></i></a>-->
<!--                                <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>-->
<!--                                <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>-->
<!--                                <a href="#" target="_blank"><i class="fab fa-youtube"></i></a>-->
							</div>
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	</div>
</section>
