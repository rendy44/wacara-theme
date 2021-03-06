<?php
/**
 * Template for displaying masthead opening tag.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bg_image_url_style       = $masthead_bg_image_url ? "style='background-image: url({$masthead_bg_image_url})'" : '';
$masthead_class           = isset( $masthead_class ) ? $masthead_class : 'wcr-masthead-header wcr-plain-header';
$column_size              = isset( $masthead_column ) ? $masthead_column : '2-3';
$masthead_alignment_class = isset( $masthead_alignment_class ) ? $masthead_alignment_class : 'wcr-justify-content-center';
$column_alignment         = isset( $column_alignment ) ? $column_alignment : 'wcr-text-center';
?>

<header class="<?php echo esc_attr( $masthead_class ); ?>" <?php echo $bg_image_url_style; // phpcs:ignore ?>>
	<div class="frow-container wcr-height-100-p">
		<div class="frow wcr-align-items-center wcr-height-100-p <?php echo esc_attr( $masthead_alignment_class ); ?>">
			<div class="col-md-<?php echo esc_attr( "{$column_size} {$column_alignment}" ); ?>">
