<?php
/**
 * Custom template for displaying modal for detail registrant in wp-admin
 *
 * @author  WPerfekt
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
<div class="registrant_detail">
	<div class="detail_row">
		<div class="detail_title"><?php esc_html_e( 'Invoice', 'wacara' ); ?></div>
		<?php $total_cent = $maybe_price_in_cent_with_unique / 100; ?>
		<div class="detail_content"><?php echo esc_html( $currency_symbol . number_format_i18n( $total_cent, 2 ) ); ?></div>
	</div>
	<div class="detail_row">
		<div class="detail_title"><?php esc_html_e( 'Confirmation date', 'wacara' ); ?></div>
		<div class="detail_content"><?php echo esc_html( $confirmation_date_time ); ?></div>
	</div>
	<div class="detail_row">
		<div class="detail_title"><?php esc_html_e( 'Transferred to', 'wacara' ); ?></div>
		<div class="detail_content">
			<?php echo esc_html( $selected_bank_account['name'] . ', ' . $selected_bank_account['branch'] ); ?><br/>
			<?php echo esc_html( $selected_bank_account['number'] . ' - ' . $selected_bank_account['holder'] ); ?>
		</div>
	</div>
	<div class="detail_row action">
		<div class="detail_title"></div>
		<div class="detail_content">
			<button type="button" class="button button-primary btn-do-payment-action done" data-id="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Verify', 'wacara' ); ?></button>
			<button type="button" class="button button-danger btn-do-payment-action fail" data-id="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Reject', 'wacara' ); ?></button>
			<p class="disclaimer">*) <?php esc_html_e( 'This is one time action, once you click the action, you can not undo', 'wacara' ); ?></p>
		</div>
	</div>
</div>
