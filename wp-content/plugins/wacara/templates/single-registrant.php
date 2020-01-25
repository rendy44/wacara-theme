<?php
/**
 * Custom template for displaying single registrant.
 *
 * @author Rendy
 * @package Wacara
 * @version 0.0.1
 */

use Wacara\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'wacara' );

/**
 * Wacara before displaying registrant content hook.
 */
do_action( 'wacara_before_displaying_registrant_content' );

while ( have_posts() ) {
	the_post();

	Helper::load_template( 'registrant-content', true );
}

/**
 * Wacara after displaying registrant content hook.
 */
do_action( 'wacara_after_displaying_registrant_content' );

get_footer( 'wacara' );
