<?php
/**
 * Template for displaying waiting payment page in single registration.
 *
 * @author  Rendy
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

andalan
<!--<section class="py-0" id="registration-form">-->
<!--	<div class="container">-->
<!--		<div class="row">-->
<!--			<div class="col-lg-6 col-md-8 mx-auto text-center">-->
<!--				<p class="lead">--><?php //esc_html_e( 'In order to save your seat, please make a payment as detailed below:', 'wacara' ); ?><!--</p>-->
<!--				<h1 class="amount">--><?php //echo esc_html( sprintf( '%s%s', $currency_code, $amount ) ); ?><!--</h1>-->
<!--				<div class="alert alert-warning mb-3">-->
<!--					<i class="fa fa-exclamation-circle"></i>-->
<!--					<strong>--><?php //esc_html_e( 'Important!', 'wacara' ); ?><!--</strong>-->
<!--					--><?php //esc_html_e( 'The transfer amount must be exactly same as above, including the coma if any', 'wacara' ); ?>
<!--				</div>-->
<!--				<p>--><?php //esc_html_e( 'Select one of the following bank accounts you want to transfer to', 'wacara' ); ?><!--</p>-->
<!--				<form id="frm_confirmation" method="post">-->
<!--					<div class="row justify-content-center bank-lists">-->
<!--						--><?php
//						if ( ! empty( $bank_accounts ) ) {
//							$row_num = 0;
//							foreach ( $bank_accounts as $account ) {
//								?>
<!--								<div class="col-lg-6 bank-item py-3">-->
<!--									<input type="radio" name="selected_bank" id="bank_--><?php //echo esc_attr( $row_num ); ?><!--" value="--><?php //echo esc_attr( $row_num ); ?><!--">-->
<!--									<label for="bank_--><?php //echo esc_attr( $row_num ); ?><!--">-->
<!--										<i class="text-primary fa fa-check-circle fa-2x"></i>-->
<!--										--><?php ///* translators: %1: bank name &2: branch name */ ?>
<!--										<p class="name">--><?php //echo esc_html( sprintf( _x( '%1$s, %2$s', 'Dislaying bank information', 'wacara' ), $account['name'], $account['branch'] ) ); ?><!--</p>-->
<!--										<p class="number">--><?php //echo esc_html( $account['number'] ); ?><!--</p>-->
<!--										<p class="holder">--><?php //echo esc_html( $account['holder'] ); ?><!--</p>-->
<!--									</label>-->
<!--								</div>-->
<!--								--><?php
//								$row_num ++;
//							}
//						}
//
//						// Render current registrant id.
//						echo apply_filters( 'sk_input_field', 'registrant_id', 'hidden', '', $id ); // phpcs:ignore
//
//						// Add nonce.
//						wp_nonce_field( 'wacara_nonce', 'sk_payment' );
//						?>
<!--					</div>-->
<!--					<p class="lead">--><?php //esc_html_e( 'Once you made a transfer, please click button below to confirm', 'wacara' ); ?><!--</p>-->
<!--					<button type="submit" class="btn btn-primary btn-lg btn-block btn-go-confirm">--><?php //esc_html_e( 'I have made a payment', 'wacara' ); ?><!--</button>-->
<!--				</form>-->
<!--			</div>-->
<!--		</div>-->
<!--	</div>-->
<!--</section>-->
