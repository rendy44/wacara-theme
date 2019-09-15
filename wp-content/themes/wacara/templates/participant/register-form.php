<?php
/**
 * Template for rendering registration form in single registration.
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
			<div class="col-lg-8 col-md-10 mx-auto">
				<form id="frm_register" method="post">
					<h3 class="form-block-title mb-3">Basic Information</h3>
					<div class="form-group">
						<label for="full_name">Full Name</label>
						<input class="form-control form-control-lg" id="full_name" type="text" name="full_name" required>
					</div>
					<div class="form-group">
						<label for="email_address">Email Address</label>
						<input class="form-control form-control-lg" id="email_address" type="email" name="email_address" required>
					</div>
					<h3 class="form-block-title mb-3 mt-4">Additional Information</h3>
					<div class="form-group">
						<label for="company">Company</label>
						<input class="form-control form-control-lg" id="company" type="text" name="company" required>
					</div>
					<div class="form-group">
						<label for="position">Position</label>
						<input class="form-control form-control-lg" id="position" type="text" name="position" required>
					</div>
					<div class="form-group">
						<label for="id_number">ID Number</label>
						<input class="form-control form-control-lg" id="id_number" type="text" name="id_number" required>
					</div>
					<div class="form-group">
						<label for="phone">Phone</label>
						<input class="form-control form-control-lg" id="phone" type="text" name="phone" required>
					</div>
					<input class="btn btn-primary btn-lg btn-submit-reg" type="submit" value="Register">
				</form>
			</div>
		</div>
	</div>
</section>
