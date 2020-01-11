<?php
/**
 * Custom template for displaying masthead in event landing
 *
 * @author  Rendy
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<h1 class="mb-4" data-aos="fade-right" data-aos-delay="300"><?php echo $title; // phpcs:ignore ?></h1>
<p class="lead mb-4" data-aos="fade-left" data-aos-delay="500"><?php echo esc_html( $excerpt ); ?></p>
