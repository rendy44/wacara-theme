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

<section class="bg-light gallery py-0" id="gallery" data-aos="zoom-in">
	<div class="container-wrap">
		<div class="row no-gutters">
			<?php
			foreach ( $gallery as $image_id => $image_url ) {
				?>
				<div class="col-lg-3 col-md-4 col-sm-6">
					<a href="<?php echo esc_attr( $image_url ); ?>">
						<img class="img-fluid" src="<?php echo esc_attr( wp_get_attachment_image_url( $image_id, 'wacara_gallery_thumbnail' ) ); ?>">
					</a>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</section>
