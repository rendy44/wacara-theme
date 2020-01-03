<?php
/**
 * Custom template to rendering gallery section.
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<section class="<?php echo esc_attr( $class ); ?> gallery <?php echo esc_attr( ! $title && ! $subtitle ? 'pt-0' : '' ); ?> pb-0" id="gallery" data-aos="zoom-in">
	<?php
	if ( $title || $subtitle ) {
		?>
		<div class="container">
			<div class="row">
				<div class="col-lg-8 mx-auto text-center mb-3">
					<h2 class="section-heading" data-aos="fade-left" data-aos-delay="200"><?php echo esc_html( $title ); ?></h2>
					<p class="lead" data-aos="fade-right" data-aos-delay="400"><?php echo esc_html( $subtitle ); ?></p>
				</div>
			</div>
		</div>
		<?php
	}
	?>
	<div class="container-wrap">
		<div class="row no-gutters justify-content-center gallery-list">
			<?php
			foreach ( $gallery as $image_id => $image_url ) {
				?>
				<div class="col-lg-3 col-md-4 col-sm-6">
					<a href="<?php echo esc_attr( $image_url ); ?>" style="background-image: url(<?php echo esc_attr( wp_get_attachment_image_url( $image_id, 'wacara_gallery_thumbnail' ) ); ?>)"><i class="fa fa-search"></i></a>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</section>
