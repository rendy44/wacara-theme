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

<div class="row no-gutters justify-content-center gallery-list">
	<?php
	foreach ( $gallery as $image_id => $image_url ) {
		?>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <a href="<?php echo esc_attr( $image_url ); ?>"
               style="background-image: url(<?php echo esc_attr( wp_get_attachment_image_url( $image_id, 'wacara_gallery_thumbnail' ) ); ?>)"><i
                        class="fa fa-search"></i></a>
        </div>
		<?php
	}
	?>
</div>
