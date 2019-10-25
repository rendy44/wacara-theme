<?php
/**
 * Custom template for displaying modal for detail participant in wp-admin
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
<div class="participant_detail">
	<div class="detail_row">
		<div class="detail_title"><?php esc_html_e( 'Invoice', 'wacara' ); ?></div>
		<?php $total_cent = $maybe_price_in_cent_with_unique / 100; ?>
		<div class="detail_content"><?php echo esc_html( $currency . number_format_i18n( $total_cent ) ); ?></div>
	</div>
	<div class="detail_row">
		<div class="detail_title"><?php esc_html_e( 'Date', 'wacara' ); ?></div>
		<div class="detail_content"><?php echo esc_html( $confirmation_date_time ); ?></div>
	</div>
	<div class="detail_row">
		<div class="detail_title"><?php esc_html_e( 'Bank', 'wacara' ); ?></div>
		<div class="detail_content">
			<?php echo esc_html( $selected_bank_account['name'] . ', ' . $selected_bank_account['branch'] ); ?><br/>
			<?php echo esc_html( $selected_bank_account['number'] . ' - ' . $selected_bank_account['holder'] ); ?>
		</div>
	</div>
</div>
