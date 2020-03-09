<?php
/**
 * List of helpful functions.
 *
 * @author WPerfekt
 * @package Wacara_Theme
 * @version 0.0.1
 */

namespace Wacara_Theme;

use Wacara\Event;
use Wacara\Template;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara_Theme\Helper' ) ) {

	/**
	 * Class Helper
	 *
	 * @package Wacara_Theme
	 */
	class Helper {

		/**
		 * Extend function to render template from local theme.
		 *
		 * @param string $template name of the template.
		 * @param array  $args list of variables.
		 * @param bool   $echo whether render or save as variable.
		 *
		 * @return string|void
		 */
		public static function render_local_template( $template, $args = [], $echo = false ) {

			// Override the template folder.
			Template::override_folder( __FILE__, false );

			// Render the template.
			$result = Template::render( $template, $args );

			// Reset the template folder.
			Template::reset_folder();

			if ( $echo ) {
				echo $result; //phpcs:ignore
			} else {
				return $result;
			}
		}

		/**
		 * Get event add to google calendar url.
		 *
		 * @param Event $event object of the current event.
		 *
		 * @return string
		 */
		public static function build_event_add_to_calendar_url( $event ) {
			$base_url = 'https://calendar.google.com/calendar/r/eventedit';

			// Prepare iso dates.
			$date_format    = 'Ymd';
			$time_format    = 'His';
			$date_start_iso = $event->get_date_start( $date_format );
			$time_start_iso = $event->get_date_start( $time_format );
			$date_end_iso   = $event->get_date_end( $date_format );
			$time_end_iso   = $event->get_date_end( $time_format );
			// TODO: fetch date and time iso correctly.

			// Add attributes based on event details.
			return add_query_arg(
				[
					'text'     => $event->post_title,
					'details'  => $event->get_headline(),
					'dates'    => $date_start_iso . 'T' . $time_start_iso . '/' . $date_end_iso . 'T' . $time_end_iso,
					'location' => $event->get_location_object()->get_location_name(),
				],
				$base_url
			);
		}
	}
}
