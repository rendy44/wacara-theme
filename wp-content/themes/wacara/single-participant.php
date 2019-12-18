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

	// Render form if no payment attempt has been made.
	if ( ! $reg_status && 'done' !== $reg_status ) {
		$validate_pricing                          = Helper::is_pricing_valid( $register_args['pricing_id'], true );
		$register_args['use_payment']              = $validate_pricing->success;
		$register_args['maybe_auto_error_message'] = Helper::get_post_meta( 'maybe_auto_error_message' );
		$register_args['payment_methods']          = Register_Payment::get_registered();
		echo Template::render( 'participant/register-form', $register_args ); // phpcs:ignore
	} elseif ( 'done' === $reg_status ) {

		// Display content for success page.
		echo Template::render( 'participant/register-success', $register_args ); // phpcs:ignore
	} else {

		// Display content based on payment.
		$selected_payment     = Helper::get_post_meta( 'payment_method' );
		$selected_payment_obj = Register_Payment::get_payment_method_class( $selected_payment );
		if ( $selected_payment_obj ) {
			echo $selected_payment_obj->maybe_page_after_payment( get_the_ID(), $reg_status, $register_args['pricing_id'], $register_args['event_id'] ); // phpcs:ignore
		}
	}
}
get_footer();
