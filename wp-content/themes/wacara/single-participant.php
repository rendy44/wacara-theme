<?php
/**
 * Template for rendering single participant.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) {
	the_post();
	$reg_status    = Helper::get_post_meta( 'status' );
	$register_args = [
		'id'    => get_the_ID(),
		'title' => get_the_title(),
	];
	switch ( $reg_status ) {
		case 'success':
			$template = 'success';
			break;
		case 'failed':
			$template = 'failed';
			break;
		default:
			$template = 'register-form';
			break;
	}
	echo Template::render( 'participant/' . $template, $register_args ); // phpcs:ignore
}
get_footer();
