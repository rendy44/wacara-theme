<?php
/**
 * Template for rendering single registrant.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

while ( have_posts() ) {
	the_post();

	$registrant_id = get_the_ID();

	// Instance the registrant.
	$registrant = new Registrant( $registrant_id );

	// Fetch invoice detail.
	$invoice_detail = $registrant->get_invoicing_info();

	$register_args = [
		'id'         => $registrant_id,
		'title'      => $registrant->post_title,
		'pricing_id' => $invoice_detail['pricing_id'],
		'event_id'   => $registrant->get_event_info(),
	];

	// Render form if no payment attempt has been made.
	if ( ! $registrant->get_registration_status() && 'done' !== $registrant->get_registration_status() ) {
		$validate_pricing                 = Helper::is_pricing_valid( $register_args['pricing_id'], true );
		$register_args['use_payment']     = $validate_pricing->success;
		$register_args['payment_methods'] = Register_Payment::get_registered();
		echo Template::render( 'registrant/register-form', $register_args ); // phpcs:ignore
	} elseif ( 'done' === $registrant->get_registration_status() ) {

		// Display content for success page.
		echo Template::render( 'registrant/register-success', $register_args ); // phpcs:ignore
	} else {

		// Display content based on payment.
		$selected_payment     = Helper::get_post_meta( 'payment_method' );
		$selected_payment_obj = Register_Payment::get_payment_method_class( $selected_payment );
		if ( $selected_payment_obj ) {
			echo $selected_payment_obj->maybe_page_after_payment( $registrant, $registrant->get_registration_status(), $invoice_detail['pricing_id'], $invoice_detail['price_in_cent'], $invoice_detail['currency'] ); // phpcs:ignore
		}
	}
}
