<?php
/**
 * Template for displaying registrant invoice,
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wcr-registrant-invoice-wrapper">
	<div class="wcr-registrant-invoice-product-wrapper">
		<div class="wcr-registrant-invoice-product-image-wrapper">
			<img src="<?php echo esc_attr( $event_logo_url ); ?>" alt="<?php esc_html_e( 'Event logo', 'wacara' ); ?>" class="wcr-registrant-invoice-product-image"/>
		</div>
		<div class="wcr-registrant-invoice-product-detail-wrapper">
			<p class="wcr-registrant-invoice-product-detail"><?php echo esc_html( $event_name ); ?><span class="wcr-registrant-invoice-product-subinfo"><?php echo esc_html( $pricing_name ); ?></span></p>
		</div>
	</div>
	<div class="wcr-registrant-invoice-detail-wrapper">
		<?php foreach ( $invoice_details as $detail ) { ?>
			<div class="wcr-registrant-invoice-detail">
                <label class="wcr-registrant-invoice-field"><?php echo $detail['field']; // phpcs:ignore ?></label>
                <span class="wcr-registrant-invoice-value"><?php echo $detail['value']; // phpcs:ignore ?></span>
			</div>
		<?php } ?>
	</div>
</div>
