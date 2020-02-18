<?php
/**
 * Custom template for displaying single registrant.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

use Wacara\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'wacara' );

/**
 * Wacara before registrant outer content hook.
 */
do_action( 'wacara_before_registrant_outer_content' );

while ( have_posts() ) {
	the_post();

	Helper::load_template( 'registrant-content', true );
}

/**
 * Wacara after registrant outer content hook.
 */
do_action( 'wacara_after_registrant_outer_content' );

get_footer( 'wacara' );
