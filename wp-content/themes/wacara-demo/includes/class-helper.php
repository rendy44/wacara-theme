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

			// Add attributes based on event details.
			return add_query_arg(
				[
					'text'    => $event->post_title,
					'details' => $event->get_headline(),
				// 'dates'   => $event->get_date_start( 'c' ) . '/' . $event->get_date_end( 'c' ),
				],
				$base_url
			);
		}
	}
}
