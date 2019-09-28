<?php
/**
 * Custom template to rendering sponsors section.
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<section class="<?php echo esc_attr( $class ); ?> sponsors <?php echo esc_attr( ! $title && ! $subtitle ? 'py-3' : '' ); ?>" id="sponsors" data-aos="zoom-in">
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
			<div class="col">
				<div class="sponsor-logos slider">
					<?php
					foreach ( $sponsors as $sponsor_id => $sponsor_url ) {
						echo '<div class="slide d-flex align-items-center py-3 px-2"><img src="' . esc_attr( wp_get_attachment_image_url( $sponsor_id, 'medium' ) ) . '"></div>';
					}
					?>
				</div>
			</div>
		</div>
	</div>
</section>
