<?php
/**
 * Template Name: Checkin Page
 * Page template for displaying checkin page.
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

	echo Template::render( 'global/search-section', [ 'logo_url' => Helper::get_site_logo_url() ] ); // phpcs:ignore
}

get_footer();
