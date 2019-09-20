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
	$reg_status    = Helper::get_post_meta( 'reg_status' );
	$register_args = [
		'id'         => get_the_ID(),
		'title'      => get_the_title(),
		'pricing_id' => Helper::get_post_meta( 'pricing_id' ),
		'event_id'   => Helper::get_post_meta( 'event_id' ),
	];
	switch ( $reg_status ) {
		case 'done':
			$template = 'register-success';
			break;
		case 'fail':
		default:
			$validate_pricing               = Helper::is_pricing_valid( $register_args['pricing_id'], true );
			$register_args['use_payment']   = $validate_pricing->success;
			$register_args['error_message'] = Helper::get_post_meta( 'charge_error_message' );
			$template                       = 'register-form';
			break;
	}
	echo Template::render( 'participant/' . $template, $register_args ); // phpcs:ignore
}
get_footer();
