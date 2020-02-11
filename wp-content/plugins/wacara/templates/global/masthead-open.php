<?php
/**
 * Template for displaying masthead opening tag.
 *
 * @author Rendy
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$column_size = isset( $masthead_column ) ? $masthead_column : '2-3';
?>

<header class="<?php echo esc_attr( $masthead_class ); ?>">
	<div class="frow-container wcr-height-100-p">
		<div class="frow wcr-align-items-center wcr-height-100-p">
			<div class="col-md-<?php echo esc_attr( $column_size ); ?> wcr-text-center">
