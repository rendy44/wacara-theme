<?php
/**
 * Template for displaying masthead content.
 *
 * @author Rendy
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wcr-masthead-title-wrapper">
	<h1 class="wcr-masthead-title"><?php echo esc_html( $masthead_title ); ?></h1>
</div>
<div class="wcr-masthead-desc-wrapper">
	<p class="wcr-masthead-desc"><?php echo esc_html( $masthead_desc ); ?></p>
</div>
