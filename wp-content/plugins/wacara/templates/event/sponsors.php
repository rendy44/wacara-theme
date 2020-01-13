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
