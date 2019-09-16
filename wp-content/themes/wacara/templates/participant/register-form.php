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
					<?php
					// Maybe render name field.
					echo apply_filters( 'sk_input_field_event', $event_id, 'name' );
					// Maybe render email field.
					echo apply_filters( 'sk_input_field_event', $event_id, 'email' );
					// Maybe render company field.
					echo apply_filters( 'sk_input_field_event', $event_id, 'company' );
					// Maybe render position field.
					echo apply_filters( 'sk_input_field_event', $event_id, 'position' );
					// Maybe render id number field.
					echo apply_filters( 'sk_input_field_event', $event_id, 'id_number' );
					// Maybe render phone field.
					echo apply_filters( 'sk_input_field_event', $event_id, 'phone' );
					?>
                    <input class="btn btn-primary btn-lg btn-submit-reg" type="submit" value="Register">
                </form>
            </div>
        </div>
    </div>
</section>
