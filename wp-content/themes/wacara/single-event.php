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

	$date_start            = Helper::get_post_meta( 'date_start' );
	$location              = Helper::get_post_meta( 'location' );
	$location_province     = Helper::get_post_meta( 'province', $location );
	$location_country_code = Helper::get_post_meta( 'country', $location );
	// Render masthead.
	$masthead_args = [
		'title'      => Helper::split_title( get_the_title() ),
		'date_start' => Helper::convert_date( $date_start, true ),
		'excerpt'    => Helper::convert_date( $date_start ) . ' - ' . $location_province . ', ' . $location_country_code,
	];
	echo Template::render( 'front/masthead', $masthead_args ); // phpcs:ignore

	// Render about section.
	$about_args = [];
//	echo Template::render( 'front/about', $about_args ); // phpcs:ignore

	// Render speakers section.
	$speakers_args = [];
//	echo Template::render( 'front/speakers', $speakers_args ); // phpcs:ignore

	// Render venue section.
	$venue_args = [];
//	echo Template::render( 'front/venue', $venue_args ); // phpcs:ignore

	// Render schedule section.
	$schedule_args = [];
//	echo Template::render( 'front/schedule', $schedule_args ); // phpcs:ignore

	// Render pricing section.
	$pricing_args = [];
//	echo Template::render( 'front/pricing', $pricing_args ); // phpcs:ignore

}

get_footer();
