<?php
/**
 * Template for rendering single participant.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) {
	the_post();

	$participant_id = get_the_ID();

	// Instance the participant.
	$participant = new Participant( $participant_id );

	// Fetch invoice detail.
	$invoice_detail = $participant->get_invoicing_info();

	$register_args = [
		'id'         => $participant_id,
		'title'      => $participant->post_title,
		'pricing_id' => $invoice_detail['pricing_id'],
		'event_id'   => $participant->get_event_info(),
	];

	// Render form if no payment attempt has been made.
	if ( ! $participant->get_registration_status() && 'done' !== $participant->get_registration_status() ) {
		$validate_pricing                 = Helper::is_pricing_valid( $register_args['pricing_id'], true );
		$register_args['use_payment']     = $validate_pricing->success;
		$register_args['payment_methods'] = Register_Payment::get_registered();
		echo Template::render( 'participant/register-form', $register_args ); // phpcs:ignore
	} elseif ( 'done' === $participant->get_registration_status() ) {

		// Display content for success page.
		echo Template::render( 'participant/register-success', $register_args ); // phpcs:ignore
	} else {

		// Display content based on payment.
		$selected_payment     = Helper::get_post_meta( 'payment_method' );
		$selected_payment_obj = Register_Payment::get_payment_method_class( $selected_payment );
		if ( $selected_payment_obj ) {
			echo $selected_payment_obj->maybe_page_after_payment( $participant, $participant->get_registration_status(), $invoice_detail['pricing_id'], $invoice_detail['price_in_cent'], $invoice_detail['currency'] ); // phpcs:ignore
		}
	}
}
get_footer();
