<?php
/**
 * Template for displaying section opening tag.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$custom_attribute_txt = '';

// Fetch custom attributes.
if ( ! empty( $custom_attributes ) && isset( $custom_attributes ) ) {
	foreach ( $custom_attributes as $attribute_key => $attribute_value ) {
		$custom_attribute_txt .= " {$attribute_key}='" . esc_attr( $attribute_value ) . "'";
	}
} ?>

<section class="wcr-section <?php echo esc_attr( $section_class ); ?>" id="<?php echo esc_attr( "wcr-section-{$section}" ); ?>" <?php echo $custom_attribute_txt; // phpcs:ignore ?>>
	<div class="frow-container">
