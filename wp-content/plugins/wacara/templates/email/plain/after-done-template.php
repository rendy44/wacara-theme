<?php
/**
 * Email template after making registration.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* translators: %s : name of the recipient */
echo sprintf( __( '<p>Hi <strong>%s</strong>,</p>', 'wacara' ), $recipient_name ); // phpcs:ignore
/* translators: %s : name of the selected event */
echo sprintf( __( '<p>Congratulation, you have secured your seat to join %s.</p>', 'wacara' ), $event_name ); // phpcs:ignore
