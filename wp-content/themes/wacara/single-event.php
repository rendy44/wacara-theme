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
use Skeleton\UI;

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
		 * Masthead section.
		 */
		$header_alignment       = Helper::get_post_meta( 'content_alignment', $header_template );
		$header_default_image   = Helper::get_post_meta( 'default_image_id', $header_template );
		$header_countdown       = Helper::get_post_meta( 'countdown_content', $header_template );
		$date_start             = Helper::get_post_meta( 'date_start' );
		$location               = Helper::get_post_meta( 'location' );
		$location_country_code  = Helper::get_post_meta( 'country', $location );
		$location_province      = Helper::get_post_meta( 'province', $location );
		$event_background_image = Helper::get_post_meta( 'background_image_id' );
		$masthead_args          = [
			'header_extra_class' => UI::get_header_extra_class( $header_width, $header_scheme, $header_alignment ),
			'title'              => Helper::split_title( get_the_title() ),
			'date_start'         => Helper::convert_date( $date_start, true ),
			'excerpt'            => Helper::convert_date( $date_start ) . ' - ' . $location_province . ', ' . $location_country_code,
			'background_image'   => UI::generate_header_background_image( $event_background_image, $header_default_image ),
			'show_countdown'     => 'on' === $header_countdown ? true : false,
		];
		echo Template::render( 'event/masthead', $masthead_args ); // phpcs:ignore

		/**
		 * Render all sections.
		 */
		$section_num = 1;
		foreach ( $sections as $section ) {

			// Define section class based on odd or even position.
			$section_class = 0 === $section_num % 2 ? 'bg-white' : 'bg-light';

			/**
			 * Render the selected section.
			 *
			 * @param Event  $event         the object of current event.
			 * @param string $section_class the css class of selected section.
			 */
			do_action( "wacara_render_{$section}_section", $event, $section_class );

			$section_num ++;
		}
	} else {

		// Tell the visitor that this event is no longer accessible.
		echo Template::render( 'event/expired' ); // phpcs:ignore
	}
}

get_footer();
