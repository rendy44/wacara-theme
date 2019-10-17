<?php
/**
 * Template for rendering waiting payment page in single registration.
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<section class="py-0" id="registration-form">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-8 mx-auto text-center">
                <p class="lead"><?php esc_html_e( 'In order to save your seat, please make a payment as detailed below:', 'wacara' ); ?></p>
                <h1 class="amount">IDR 50.000</h1>
                <div class="alert alert-warning mb-3">
                    <i class="fa fa-exclamation-circle"></i>
                    <strong><?php esc_html_e( 'Penting!', 'wacara' ); ?></strong>
					<?php esc_html_e( 'The transfer amount must be exactly same as above, including the last 3 digits', 'wacara' ); ?>
                </div>
                <p><?php esc_html_e( 'Make a transfer to one of the following bank accounts, before %s', 'wacara' ); ?></p>
                <div class="row justify-content-center bank-lists">
                    <div class="col-lg-4 col-md-6 bank-item">
                        <p class="name">BANK BNI, Jakarta</p>
                        <p class="number">098 23230 0001</p>
                        <p class="holder">Rendy de Puniet</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
