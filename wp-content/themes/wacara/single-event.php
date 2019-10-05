<?php
/**
 * Template for displaying single event
 *
 * @author  Rendy
 * @package Wacara
 */

use Skeleton\Event;
use Skeleton\Helper;
use Skeleton\Template;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Save all sections alias into variable.
$sections = Helper::get_post_meta( 'section_order' );

// If sections is empty, add pricing as the default.
if ( empty( $sections ) ) {
	$sections[] = 'pricing';
}

$header_template = Helper::get_post_meta( 'header' );
$header_width    = Helper::get_post_meta( 'content_width', $header_template );
$header_scheme   = Helper::get_post_meta( 'color_scheme', $header_template );

// Add filter to the navbar.
add_filter(
	'wacara_navbar_extra_class',
	function () use ( $header_width, $header_scheme ) {
		return 'center' === $header_width ? $header_scheme : '';
	}
);

// Add filter to add navbar items.
$nav_items = [];
foreach ( $sections as $section ) {
	$nav_label = Helper::get_post_meta( $section . '_nav_title' );
	if ( $nav_label ) {
		$nav_items[ $section ] = $nav_label;
	}
}
add_filter(
	'wacara_navbar_items',
	function () use ( $nav_items ) {
		return $nav_items;
	}
);

get_header();

while ( have_posts() ) {
	the_post();

	// Save the event id into variable.
	$event_id = get_the_ID();

	// Fetch event object.
	$event = new Event( $event_id, true );

	// Define event date start.
	$is_event_past = $event->is_event_past();

	if ( ! $is_event_past ) {

		/**
		 * Perform actions to render masthead.
		 *
		 * @param Event $event the object of current event.
		 * @param string the id of header template.
		 */
		do_action( 'wacara_render_masthead_section', $event, $header_template );

		/**
		 * Render all sections.
		 */
		$section_num = 1;
		foreach ( $sections as $section ) {

			// Define section class based on odd or even position.
			$section_class    = 0 === $section_num % 2 ? 'bg-white' : 'bg-light';
			$section_title    = Helper::get_post_meta( $section . '_title' );
			$section_subtitle = Helper::get_post_meta( $section . '_subtitle' );

			/**
			 * Perform action to render selected section.
			 *
			 * @param Event  $event         the object of current event.
			 * @param string $section_class the css class of selected section.
			 */
			do_action( "wacara_render_{$section}_section", $event, $section_class, $section_title, $section_subtitle );

			$section_num ++;
		}
	} else {

		// Tell the visitor that this event is no longer accessible.
		echo Template::render( 'event/expired' ); // phpcs:ignore
	}
}

get_footer();
