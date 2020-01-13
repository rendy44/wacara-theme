<?php
/**
 * Template for displaying section opening tag.
 *
 * @author Rendy
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<section class="<?php echo esc_attr( $section_class ); ?>" id="<?php echo esc_attr( "section-{$section}" ); ?>"
         data-aos="zoom-in">
    <div class="container">
