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
 * @param Registrant the object of the current registrant.
 */
do_action( 'wacara_after_registrant_masthead', $registrant );
