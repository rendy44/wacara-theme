<?php
/**
 * Custom template for displaying single event.
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
 * Wacara before event outer content hook.
 */
do_action( 'wacara_before_event_outer_content' );

while ( have_posts() ) {
	the_post();

	Helper::load_template( 'event-content', true );
}

/**
 * Wacara after event outer content hook.
 */
do_action( 'wacara_after_event_outer_content' );

get_footer( 'wacara' );
