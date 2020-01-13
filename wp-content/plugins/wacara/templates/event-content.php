<?php
/**
 * Template for displaying single event
 *
 * @author  Rendy
 * @package Wacara
 * @version 0.0.1
 */

use Wacara\Event;
use Wacara\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wacara before event main content hook.
 */
do_action( 'wacara_before_displaying_event_main_content' );

// Fetch event object.
$event = new Event( get_the_ID(), true );

// Define event's variables based on its properties.
$is_event_past   = $event->is_event_past();
$header_template = Helper::get_post_meta( 'header' );

if ( ! $is_event_past ) {

	/**
	 * Wacara before event masthead hook.
	 */
	do_action( 'wacara_before_displaying_event_masthead' );

	/**
	 * Perform actions to render masthead.
	 *
	 * @param Event $event the object of the current event.
	 * @param string $header_template the id of selected header template of the current event.
	 *
	 * @hooked render_masthead_opening_callback - 10
	 * @hooked render_masthead_content_callback - 20
	 * @hooked render_masthead_countdown_callback - 30
	 * @hooked render_masthead_closing_callback - 40
	 */
	do_action( 'wacara_render_masthead_section', $event, $header_template );

	/**
	 * Wacara after event masthead hook.
	 */
	do_action( 'wacara_after_displaying_event_masthead' );

	/**
	 * Render all sections.
	 */
	$sections    = Helper::get_post_meta( 'section_order' );
	$section_num = 1;
	foreach ( $sections as $section ) {

		// Define section class based on odd or even position.
		$section_class    = 0 === $section_num % 2 ? 'bg-white' : 'bg-light';
		$section_title    = Helper::get_post_meta( $section . '_title' );
		$section_subtitle = Helper::get_post_meta( $section . '_subtitle' );

		/**
		 * Wacara section class filter.
		 *
		 * @param string $section_class current class of the section.
		 * @param string $section the name of the current section.
		 * @param string $section_num ordering number of the current section.
		 */
		$section_class = apply_filters( "wacara_{$section}_class", $section_class, $section, $section_num );

		/**
		 * Perform action to render selected section.
		 *
		 * @param Event $event the object of current event.
		 * @param string $section_class the css class of selected section.
		 */
		do_action( "wacara_render_{$section}_section", $event, $section_class, $section_title, $section_subtitle );

		$section_num ++;
	}
} else {

	/**
	 * Wacara before displaying expired event content hook.
	 *
	 * @param Event $event the object of the current event.
	 *
	 * @hooked render_expired_opening_callback - 10
	 */
	do_action( 'wacara_before_displaying_event_expired', $event );

	/**
	 * Wacara render event expired hook.
	 *
	 * @param Event $event the object of the current event.
	 */
	do_action( 'wacara_render_event_expired', $event );

	/**
	 * Wacara after displaying expired event content hook.
	 *
	 * @param Event $event the object of the current event.
	 */
	do_action( 'wacara_after_displaying_event_expired', $event );
}

/**
 * Wacara after event main content hook.
 */
do_action( 'wacara_after_displaying_event_main_content' );
