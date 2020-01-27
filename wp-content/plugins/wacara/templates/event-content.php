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

// Fetch event object.
$event = new Event( get_the_ID(), true );

// Define event's variables based on its properties.
$is_event_past   = $event->is_event_past();
$header_template = Helper::get_post_meta( 'header' );

if ( ! $is_event_past ) {

	/**
	 * Wacara before event masthead hook.
	 *
	 * @param Event $event the object of the current event.
	 *
	 * @hooked render_cta_opening_callback - 10
	 * @hooked render_cta_content_callback - 20
	 * @hooked render_cta_closing_callback - 50
	 */
	do_action( 'wacara_before_event_masthead', $event );

	/**
	 * Wacara event masthead hook.
	 *
	 * @param Event $event the object of the current event.
	 * @param string $header_template the id of selected header template of the current event.
	 *
	 * @hooked render_masthead_opening_callback - 10
	 * @hooked render_masthead_content_callback - 20
	 * @hooked render_masthead_countdown_callback - 30
	 * @hooked render_masthead_closing_callback - 40
	 */
	do_action( 'wacara_event_masthead', $event, $header_template );

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
		$section_class    = 0 === $section_num % 2 ? 'wcr-section-even' : 'wcr-section-odd';
		$section_title    = Helper::get_post_meta( $section . '_title' );
		$section_subtitle = Helper::get_post_meta( $section . '_subtitle' );

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
		 * @param string $section the name of the selected section.
		 * @param Event $event the object of the current event.
		 * @param string $section_class the css class of the selected section.
		 * @param string $section_title the title of the selected section.
		 * @param string $section_subtitle the subtitle of the selected section.
		 *
		 * @hooked render_section_opening_callback - 10
		 * @hooked maybe_section_title_callback - 20
		 */
		do_action( 'wacara_before_event_section', $section, $event, $section_class, $section_title, $section_subtitle );

		/**
		 * Wacara single section hook.
		 *
		 * @param Event $event the object of the current event.
		 */
		do_action( "wacara_event_{$section}_section", $event );

		/**
		 * Wacara after section hook.
		 *
		 * @param string $section the name of the selected section.
		 * @param Event $event the object of the current event.
		 * @param string $section_class the css class of the selected section.
		 * @param string $section_title the title of the selected section.
		 * @param string $section_subtitle the subtitle of the selected section.
		 *
		 * @hooked render_section_closing_callback - 50;
		 */
		do_action( 'wacara_after_event_section', $section, $event, $section_class, $section_title, $section_subtitle );

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
	do_action( 'wacara_before_event_expired', $event );

	/**
	 * Wacara render event expired hook.
	 *
	 * @param Event $event the object of the current event.
	 *
	 * @hooked render_expired_content_callback - 10
	 */
	do_action( 'wacara_event_expired', $event );

	/**
	 * Wacara after displaying expired event content hook.
	 *
	 * @param Event $event the object of the current event.
	 *
	 * @hooked render_expired_closing_callback - 50
	 */
	do_action( 'wacara_after_event_expired', $event );
}
