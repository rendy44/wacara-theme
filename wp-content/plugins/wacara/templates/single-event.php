<?php
/**
 * Custom template for displaying single event.
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
 * Wacara before event content hook.
 */
do_action( 'wacara_before_displaying_event_content' );

while ( have_posts() ) {
	the_post();

	Helper::load_template( 'event-content', true );
}

/**
 * Wacara after event content hook.
 */
do_action( 'wacara_after_displaying_event_content' );

get_footer( 'wacara' );
