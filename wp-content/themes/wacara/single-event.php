<?php
/**
 * Template for displaying single event
 *
 * @author  Rendy
 * @package Wacara
 */

use Skeleton\Helper;
use Skeleton\Template;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) {
	the_post();

	// Define event date start.
	$is_event_past = Helper::is_event_past( get_the_ID() );

	if ( ! $is_event_past ) {

		/**
		 * Masthead section.
		 */
		$date_start            = Helper::get_post_meta( 'date_start' );
		$location              = Helper::get_post_meta( 'location' );
		$location_country_code = Helper::get_post_meta( 'country', $location );
		$location_province     = Helper::get_post_meta( 'province', $location );

		$masthead_args = [
			'title'                => Helper::split_title( get_the_title() ),
			'date_start'           => Helper::convert_date( $date_start, true ),
			'excerpt'              => Helper::convert_date( $date_start ) . ' - ' . $location_province . ', ' . $location_country_code,
			'background_image_url' => TEMP_URI . '/assets/img/illustration/events.svg',
		];
		echo Template::render( 'event/masthead', $masthead_args ); // phpcs:ignore

		/**
		 * About section.
		 */
		$about_args = [
			'description' => Helper::get_post_meta( 'description' ),
			'location'    => Helper::get_location_paragraph( $location ),
			'time'        => Helper::get_time_paragraph(),
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
