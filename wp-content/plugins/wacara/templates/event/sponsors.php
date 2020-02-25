<?php
/**
 * Custom template to display sponsors section in landing event.
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wcr-section-content-wrapper">
	<div class="wcr-sponsors-wrapper">
		<?php
		foreach ( $sponsors as $image_id => $image_url ) {
			?>
			<div class="wcr-sponsor-column-wrapper">
				<div class="wcr-sponsor-wrapper">
					<img src="<?php echo esc_attr( wp_get_attachment_image_url( $image_id, 'medium' ) ); ?>" class="wcr-sponsor" alt="">
				</div>
			</div>
			<?php
		}
		?>
	</div>
</div>
