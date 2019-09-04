<?php
/**
 * Template for displaying front page
 *
 * @author  Rendy
 * @package Wacara
 */

use Skeleton\Template;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

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

get_footer();
