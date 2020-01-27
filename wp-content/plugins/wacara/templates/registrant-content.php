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
do_action( 'wacara_before_displaying_registrant_masthead', $registrant );

/**
 * Wacara registrant masthead hook.
 *
 * @param Registrant $registrant the object of the current registrant.
 */
do_action( 'wacara_render_registrant_masthead_section', $registrant );

do_action( 'wacara_after_displaying_registrant_masthead', $registrant );