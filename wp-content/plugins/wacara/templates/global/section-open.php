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

<section class="wcr-event-section <?php echo esc_attr( $section_class ); ?>" id="<?php echo esc_attr( "wcr-section-{$section}" ); ?>">
	<div class="frow-container">
