<?php
/**
 * Custom template for rendering expired event..
 *
 * @author  Rendy
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<h2 class="section-heading" data-aos="fade-left" data-aos-delay="200"><?php echo esc_html( $expired_title ); ?></h2>
<p class="lead" data-aos="fade-right" data-aos-delay="400"><?php echo esc_html( $expired_content ); ?></p>
