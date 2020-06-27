<?php
/**
 * Email template after making registration.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.2
 */

use Wacara\Registrant;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wacara before done registrant email template hook.
 *
 * @param Registrant $registrant object of the current registrant.
 */
do_action( 'wacara_before_done_registrant_email_template', $registrant );

/* translators: %s : name of the recipient */
echo sprintf( __( '<p>Hi <strong>%s</strong>,</p>', 'wacara' ), $recipient_name ); // phpcs:ignore
/* translators: %s : name of the selected event */
echo sprintf( __( '<p>Congratulation, you have secured your seat to join %s.</p>', 'wacara' ), $event_name ); // phpcs:ignore
echo __('<p>Below is your booking code:</p>','wacara'); // phpcs:ignore
?>
<div style="padding: 1rem; margin-bottom: 1rem; text-align: center; border: 2px dashed #777777">
	<p style="font-size: 32px; color: #333; margin: 0; line-height: 1; font-weight: 700"><?php echo esc_html( $registrant->get_booking_code() ); ?></p>
</div>
<p style="margin-bottom: 1rem"><?php esc_html_e( 'You can use your qrcode below as an alternative', 'wacara' ); ?></p>
<div style="text-align: center">
	<img src="<?php echo esc_attr( $registrant->get_qrcode_url() ); ?>" alt="QR Code" style="max-width: 300px; max-height: 300px; text-align: center">
</div>
<?php
/**
 * Wacara after done registrant email template hook.
 *
 * @param Registrant $registrant object of the current registrant.
 */
do_action( 'wacara_after_done_registrant_email_template', $registrant );
