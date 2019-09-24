<?php
/**
 * Template for rendering search section.
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
            <div class="col-lg-8 col-md-10 mx-auto text-center"><img class="img-fluid logo mb-5" src="../img/logo.png">
                <div class="row mb-3">
                    <div class="col-md-9 form-group">
                        <input class="form-control form-control-lg form-control-underlined" type="email" placeholder="Enter your booking code">
                    </div>
                    <div class="col-md-3 form-group">
                        <button class="btn btn-primary btn-lg btn-block shadow"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="btn-group shadow" role="group"><a class="btn btn-light" href="#">Scan QRCode</a><a class="btn btn-light" href="#">Need Help?</a></div>
                </div>
            </div>
        </div>
    </div>
</section>
