<?php
/**
 * Template for displaying section title and subtitle.
 *
 * @author Rendy
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<h2 class="section-heading" data-aos="fade-left" data-aos-delay="200"><?php echo esc_html( $section_title ); ?></h2>
<p class="lead" data-aos="fade-right" data-aos-delay="400"><?php echo esc_html( $section_subtitle ); ?></p>
