<?php
/**
 * Template for displaying search section.
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<section class="checkin">
	<div class="container h-100">
		<div class="row h-100">
			<div class="col-lg-8 col-md-10 mx-auto text-center">
				<img class="img-fluid logo mb-5" src="<?php echo esc_attr( $logo_url ); ?>">
				<div class="row mb-3">
					<div class="col-md-9 form-group">
						<input id="input_find_registrant" class="form-control form-control-lg form-control-underlined" type="email" placeholder="<?php esc_attr_e( 'Enter your booking code', 'wacara' ); ?>">
					</div>
					<div class="col-md-3 form-group">
						<button type="button" id="btn_find_registrant" class="btn btn-primary btn-lg btn-block shadow"><i class="fas fa-search"></i></button>
					</div>
				</div>
				<div class="row justify-content-center">
					<div class="btn-group shadow" role="group">
						<a class="btn btn-light" id="link-qrcode" href="#modalTemplate" data-toggle="modal"><?php esc_html_e( 'Scan QR Code', 'wacara' ); ?></a><a class="btn btn-light" id="link-faq" href="#"><?php esc_html_e( 'Need Help?', 'wacara' ); ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
