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
				<p><?php esc_html_e( 'Make a transfer to one of the following bank accounts', 'wacara' ); ?></p>
				<div class="row justify-content-center bank-lists py-3">
					<?php
					if ( ! empty( $bank_accounts ) ) {
						foreach (
							$bank_accounts

							as $account
						) {
							?>
							<div class="col-lg-6 bank-item py-3">
								<?php /* translators: %1: bank name &2: branch name */ ?>
								<p class="name"><?php echo esc_html( sprintf( _x( '%1$s, %2$s', 'Dislaying bank information', 'wacara' ), $account['name'], $account['branch'] ) ); ?></p>
								<p class="number"><?php echo esc_html( $account['number'] ); ?></p>
								<p class="holder"><?php echo esc_html( $account['holder'] ); ?></p>
							</div>
							<?php
						}
					}
					?>
				</div>
				<p class="lead"><?php esc_html_e( 'Once you made a transfer, please click button below to confirm', 'wacara' ); ?></p>
				<button type="button" class="btn btn-primary btn-lg btn-block"><?php esc_html_e( 'I have made a payment', 'wacara' ); ?></button>
			</div>
		</div>
	</div>
</section>
