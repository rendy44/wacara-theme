<?php
/**
 * Template for displaying single registrant content.
 *
 * @author Rendy
 * @package Wacara
 * @version 0.0.1
 */

use Wacara\Registrant;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Fetch registrant object.
$registrant = new Registrant( get_the_ID() );

// Fetch registration status.
$reg_status = $registrant->get_registration_status();

/**
 * Wacara before registrant masthead hook.
 *
 * @param Registrant $registrant the object of the current registrant.
 */
do_action( 'wacara_before_registrant_masthead', $registrant );

/**
 * Wacara registrant masthead hook.
 *
 * @param Registrant $registrant the object of the current registrant.
 */
do_action( 'wacara_registrant_masthead', $registrant );

/**
 * Wacara after registrant masthead hook.
 *
 * @param Registrant $registrant the object of the current registrant.
 */
do_action( 'wacara_after_registrant_masthead', $registrant );

/**
 * Wacara before registrant content hook.
 *
 * @param Registrant $registrant the object of the current registrant.
 */
do_action( 'wacara_before_registrant_content', $registrant );

/**
 * Wacara registrant content hook.
 *
 * @param Registrant $registrant the object of the current registrant.
 * @param string $reg_status the status of the registration.
 *                               '' for fresh participant.
 *                               'done' for participant who is ready to get the qrcode.
 *                               also you can customize your own status to meet your needs.
 */
do_action( 'wacara_registrant_content', $registrant, $reg_status );

/**
 * Wacara after registrant content hook.
 *
 * @param Registrant $registrant the object of the current registrant.
 */
do_action( 'wacara_after_registrant_content', $registrant );
