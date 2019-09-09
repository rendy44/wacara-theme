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
	$location_country_code = Helper::get_post_meta( 'country', $location );
	$location_province     = Helper::get_post_meta( 'province', $location );
	// Render masthead.
	$masthead_args = [
		'title'      => Helper::split_title( get_the_title() ),
		'date_start' => Helper::convert_date( $date_start, true ),
		'excerpt'    => Helper::convert_date( $date_start ) . ' - ' . $location_province . ', ' . $location_country_code,
	];
	echo Template::render( 'event/masthead', $masthead_args ); // phpcs:ignore

	// Render about section.
	$about_args = [
		'description' => Helper::get_post_meta( 'description' ),
		'location'    => Helper::get_location_paragraph( $location ),
		'time'        => Helper::get_time_paragraph(),
	];
	echo Template::render( 'event/about', $about_args ); // phpcs:ignore

	$speakers_arr = [];
	$speakers     = Helper::get_post_meta( 'speakers' );
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
	// Render speakers section.
	$speakers_args = [
		'speakers' => $speakers_arr,
	];
	echo Template::render( 'event/speakers', $speakers_args ); // phpcs:ignore

	// Render venue section.
	$venue_args = [
		'sliders'              => Helper::get_post_meta( 'photo', $location ),
		'location_name'        => Helper::get_post_meta( 'name', $location ),
		'location_description' => Helper::get_post_meta( 'description', $location ),
	];
	echo Template::render( 'event/venue', $venue_args ); // phpcs:ignore

	// Render schedule section.
	$schedule_args = [];
	echo Template::render( 'event/schedule', $schedule_args ); // phpcs:ignore

	$pricing_arr = [];
	$pricing     = Helper::get_post_meta( 'pricing' );
	if ( ! empty( $pricing ) ) {
		foreach ( $pricing as $price ) {
			$pricing_arr[] = [
				'id'       => $price,
				'name'     => get_the_title( $price ),
				'price'    => Helper::get_post_meta( 'price', $price ),
				'currency' => Helper::get_post_meta( 'currency', $price ),
				'pros'     => Helper::get_post_meta( 'pros', $price ),
				'cons'     => Helper::get_post_meta( 'cons', $price ),
			];
		}
	}
	// Render pricing section.
	$pricing_args = [
		'price_lists' => $pricing_arr,
	];
	echo Template::render( 'event/pricing', $pricing_args ); // phpcs:ignore

}

get_footer();
