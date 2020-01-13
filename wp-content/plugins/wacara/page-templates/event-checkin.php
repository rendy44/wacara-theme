<?php
/**
 * Template Name: Checkin Page
 * Page template for displaying checkin page.
 *
 * @author  Rendy
 * @package Wacara
 */

use Wacara\Helper;
use Wacara\Template;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) {
	the_post();

	echo Template::render( 'global/search-section', [ 'logo_url' => Helper::get_site_logo_url() ] ); // phpcs:ignore

	// Render the modal.
	$modal_body = '<div id="content_before_checkin"></div>';
	echo Template::render( 'global/modal', [ 'modal_body' => $modal_body ] ); // phpcs:ignore

	// Render modal coming soon.
	echo Template::render( 'global/modal-template', // phpcs:ignore
		[
			'content_header' => __( 'Cooming Soon', 'wacara' ),
			'content_body'   => __( 'This feature will be available soon', 'wacara' ),
		]
	);
}

get_footer();