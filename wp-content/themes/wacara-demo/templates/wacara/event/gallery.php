<?php
/**
 * Custom template to displaying gallery section.
 *
 * @author  Rendy
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wcr-section-content-wrapper">
	<div class="wcr-gallery-items-wrapper">
		<?php
		foreach ( $gallery as $image_id => $image_url ) {
			?>
			<div class="wcr-gallery-item">
				<div class="wcr-gallery-image-wrapper">
					<a href="<?php echo esc_url( $image_url ); ?>">
						<img src="<?php echo esc_attr( wp_get_attachment_image_url( $image_id, 'medium' ) ); ?>" class="wcr-gallery-image" alt="Image Gallery">
					</a>
				</div>
			</div>
			<?php
		}
		?>
	</div>
</div>
