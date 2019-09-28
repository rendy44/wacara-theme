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

	// Fetch event object.
	$event = new Event( get_the_ID(), true );

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
		 * About section.
		 */
		$about_args = [
			'description' => Helper::get_post_meta( 'description' ),
			'location'    => Helper::get_location_paragraph( $location ),
			'time'        => $event->get_event_date_time_paragraph(),
		];
		echo Template::render( 'event/about', $about_args ); // phpcs:ignore

		/**
		 * Speakers section.
		 */
		$speakers_arr = [];
		$speakers     = Helper::get_post_meta( 'speakers' );
		if ( ! empty( $speakers ) ) {
			foreach ( $speakers as $speaker ) {
				$speakers_arr[] = [
					'image'     => has_post_thumbnail( $speaker ) ? get_the_post_thumbnail_url( $speaker ) : TEMP_URI . '/assets/img/user-placeholder.jpg',
					'name'      => get_the_title( $speaker ),
					'position'  => Helper::get_post_meta( 'position', $speaker ),
					'facebook'  => Helper::get_post_meta( 'facebook', $speaker ),
					'twitter'   => Helper::get_post_meta( 'twitter', $speaker ),
					'website'   => Helper::get_post_meta( 'website', $speaker ),
					'linkedin'  => Helper::get_post_meta( 'linkedin', $speaker ),
					'instagram' => Helper::get_post_meta( 'instagram', $speaker ),
					'youtube'   => Helper::get_post_meta( 'youtube', $speaker ),
				];
			}

			$speakers_args = [
				'speakers' => $speakers_arr,
			];
			echo Template::render( 'event/speakers', $speakers_args ); // phpcs:ignore
		}

		/**
		 * Venue section.
		 */
		$venue_args = [
			'sliders'              => Helper::get_post_meta( 'photo', $location ),
			'location_name'        => Helper::get_post_meta( 'name', $location ),
			'location_description' => Helper::get_post_meta( 'description', $location ),
		];
		echo Template::render( 'event/venue', $venue_args ); // phpcs:ignore

		/**
		 * Gallery section.
		 */
		$gallery = Helper::get_post_meta( 'gallery' );
		if ( ! empty( $gallery ) ) {
			$gallery_args = [
				'gallery' => $gallery,
			];
			echo Template::render( 'event/gallery', $gallery_args ); // phpcs:ignore
		}

		/**
		 * Sponsors section.
		 */
		$sponsors = Helper::get_post_meta( 'sponsors' );
		if ( ! empty( $sponsors ) ) {
			$sponsors_args = [
				'sponsors' => $sponsors,
			];
			echo Template::render( 'event/sponsors', $sponsors_args ); // phpcs:ignore
		}

		/**
		 * Schedule section.
		 */
		$schedules = Helper::get_post_meta( 'schedules' );
		if ( ! empty( $schedules ) ) {
			$schedule_args = [
				'schedules' => $schedules,
			];
			echo Template::render( 'event/schedule', $schedule_args ); // phpcs:ignore
		}

		/**
		 * Pricing section
		 */
		$allow_registration = Helper::get_post_meta( 'allow_register' );

		// Only render the pricing section if registration is required to join the event.
		if ( 'on' === $allow_registration ) {
			$pricing_arr = [];
			$pricing     = Helper::get_post_meta( 'pricing' );
			if ( ! empty( $pricing ) ) {
				foreach ( $pricing as $price ) {
					$currency_code = Helper::get_post_meta( 'currency', $price );
					$pricing_arr[] = [
						'id'       => $price,
						'name'     => get_the_title( $price ),
						'price'    => Helper::get_post_meta( 'price', $price ),
						'currency' => $currency_code,
						'symbol'   => Helper::get_currency_symbol_by_code( $currency_code ),
						'pros'     => Helper::get_post_meta( 'pros', $price ),
						'cons'     => Helper::get_post_meta( 'cons', $price ),
					];
				}
			}
			$pricing_args = [
				'price_lists' => $pricing_arr,
				'event_id'    => get_the_ID(),
			];
			echo Template::render( 'event/pricing', $pricing_args ); // phpcs:ignore
		} else {
			echo Template::render( 'event/directly' ); // phpcs:ignore
		}
	} else {

		// Tell the visitor that this event is no longer accessible.
		echo Template::render( 'event/expired' ); // phpcs:ignore
	}
}

get_footer();
