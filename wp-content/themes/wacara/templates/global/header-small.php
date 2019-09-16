<?php
/**
 * Custom template for displaying header small
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<header class="masthead small" id="masthead" data-aos="zoom-in">
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1><?php echo esc_html( $title ); ?></h1>
				<?php
				if ( isset( $subtitle ) && $subtitle ) {
					echo '<p class="lead">' . esc_html( $subtitle ) . '</p>';
				}
				?>
            </div>
        </div>
    </div>
</header>
