<?php
/**
 * Email template after making registration.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

use Wacara\Registrant;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wacara before new registrant email template hook.
 *
 * @param Registrant $registrant object of the current registrant.
 */
do_action( 'wacara_before_new_registrant_email_template', $registrant );

/* translators: %s : name of the recipient */
echo sprintf( __( '<p>Hi <strong>%s</strong>,</p>', 'wacara' ), $recipient_name ); // phpcs:ignore
/* translators: %s : name of the selected event */
echo sprintf( __( '<p>Thank you for registering to %s, please finalize your registration to secure your seat.</p>', 'wacara' ), $event_name ); // phpcs:ignore

/**
 * Wacara after new registrant email template hook.
 *
 * @param Registrant $registrant object of the current registrant.
 */
do_action( 'wacara_after_new_registrant_email_template', $registrant );
