<?php
/**
 * Custom template for displaying modal.
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="modal fade" id="modalBeforeCheckin" data-id="">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<div class="form-group">
					<label><?php esc_html_e( 'Booking code', 'wacara' ); ?>:</label>
					<span class="registrant_booking_code">...</span>
				</div>
				<div class="form-group">
					<label><?php esc_html_e( 'Name', 'wacara' ); ?>:</label>
					<span class="registrant_name">...</span>
				</div>
				<div class="form-group">
					<label><?php esc_html_e( 'Email', 'wacara' ); ?>:</label>
					<span class="registrant_email">...</span>
				</div>
			</div>
			<!-- Modal footer -->
			<div class="modal-footer">
				<button id="btn_go_checkin" type="button" class="btn btn-primary"><?php esc_html_e( 'Check-in', 'wacara' ); ?></button>
				<button type="button" class="btn btn-light" data-dismiss="modal"><?php esc_html_e( 'Cancel', 'wacara' ); ?></button>
			</div>
		</div>
	</div>
</div>
