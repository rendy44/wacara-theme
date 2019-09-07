<?php
/**
 * Template for displaying front page
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

echo '<pre>';
var_dump( Helper::get_list_of_locations() );
echo '</pre>';
// Render masthead.
$masthead_args = [
	'title'        => get_bloginfo( 'name' ),
	'description'  => get_bloginfo( 'description' ),
	'download_url' => 'https://github.com/rendy44/wp-theme-skeleton',
];
echo Template::render( 'front/masthead', $masthead_args ); // phpcs:ignore

// Render about section.
$about_args = [];
echo Template::render( 'front/about', $about_args ); // phpcs:ignore

// Render speakers section.
$speakers_args = [];
echo Template::render( 'front/speakers', $speakers_args ); // phpcs:ignore

// Render venue section.
$venue_args = [];
echo Template::render( 'front/venue', $venue_args ); // phpcs:ignore

// Render schedule section.
$schedule_args = [];
echo Template::render( 'front/schedule', $schedule_args ); // phpcs:ignore

// Render pricing section.
$pricing_args = [];
echo Template::render( 'front/pricing', $pricing_args ); // phpcs:ignore

get_footer();
