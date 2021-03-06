<?php
/**
 * Template for displaying single event
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

use Wacara\Event;
use Wacara\Event_Header;
use Wacara\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Fetch event object.
$event = new Event( get_the_ID(), true );

// Instance header.
$header = $event->get_header_object();

// Define event's variables based on its properties.
$is_event_past = $event->is_event_past();

// Check the event's validity.
if ( ! $is_event_past ) {

	/**
	 * Wacara before event masthead hook.
	 *
	 * @param Event $event the object of the current event.
	 *
	 * @hooked UI::event_cta_opening_callback - 10
	 * @hooked UI::event_cta_content_callback - 20
	 * @hooked UI::event_cta_closing_callback - 50
	 */
	do_action( 'wacara_before_event_masthead', $event );

	/**
	 * Wacara event masthead hook.
	 *
	 * @param Event $event object of the current event.
	 * @param Event_Header $header object of the selected header template of the current event.
	 *
	 * @hooked UI::event_masthead_opening_callback - 10
	 * @hooked UI::event_masthead_content_callback - 20
	 * @hooked UI::event_masthead_countdown_callback - 30
	 * @hooked UI::event_masthead_closing_callback - 40
	 */
	do_action( 'wacara_event_masthead', $event, $header );

	/**
	 * Wacara after event masthead hook.
	 *
	 * @param Event $event object of the current event.
	 */
	do_action( 'wacara_after_event_masthead', $event );

	/**
	 * Render all sections.
	 */
	$sections    = Helper::get_post_meta( 'section_order' );
	$section_num = 1;
	foreach ( $sections as $section ) {

		// Define section class based on odd or even position.
		$section_class       = 'wcr-event-section';
		$section_title       = Helper::get_post_meta( $section . '_title' );
		$section_subtitle    = Helper::get_post_meta( $section . '_subtitle' );
		$section_description = Helper::get_post_meta( $section . '_description' );

		/**
		 * Wacara section class filter.
		 *
		 * @param string $section_class current class of the section.
		 * @param string $section the name of the current section.
		 * @param int $section_num ordering number of the current section.
		 */
		$section_class = apply_filters( 'wacara_event_section_class', $section_class, $section, $section_num );

		/**
		 * Wacara before section hook.
		 *
		 * @param string $section name of the selected section.
		 * @param Event $event object of the current event.
		 * @param string $section_class css class of the selected section.
		 * @param string $section_title title of the selected section.
		 * @param string $section_subtitle subtitle of the selected section.
		 * @param string $section_description description of the selected section.
		 *
		 * @hooked UI::event_section_opening_callback - 10
		 * @hooked UI::maybe_event_section_title_callback - 20
		 */
		do_action( 'wacara_before_event_section', $section, $event, $section_class, $section_title, $section_subtitle, $section_description );

		/**
		 * Wacara single section hook.
		 *
		 * @param Event $event the object of the current event.
		 */
		do_action( "wacara_event_{$section}_section", $event );

		/**
		 * Wacara after section hook.
		 *
		 * @param string $section name of the selected section.
		 * @param Event $event object of the current event.
		 * @param string $section_class css class of the selected section.
		 * @param string $section_title title of the selected section.
		 * @param string $section_subtitle subtitle of the selected section.
		 * @param string $section_description description of the selected section.
		 *
		 * @hooked UI::event_section_closing_callback - 50;
		 */
		do_action( 'wacara_after_event_section', $section, $event, $section_class, $section_title, $section_subtitle, $section_description );

		$section_num ++;
	}
} else {

	/**
	 * Wacara before displaying expired event content hook.
	 *
	 * @param Event $event the object of the current event.
	 *
	 * @hooked UI::event_expired_opening_callback - 10
	 */
	do_action( 'wacara_before_event_expired', $event );

	/**
	 * Wacara render event expired hook.
	 *
	 * @param Event $event the object of the current event.
	 *
	 * @hooked UI::event_expired_content_callback - 10
	 */
	do_action( 'wacara_event_expired', $event );

	/**
	 * Wacara after displaying expired event content hook.
	 *
	 * @param Event $event the object of the current event.
	 *
	 * @hooked UI::event_expired_closing_callback - 50
	 */
	do_action( 'wacara_after_event_expired', $event );
}
