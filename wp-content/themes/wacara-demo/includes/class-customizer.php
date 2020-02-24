<?php
/**
 * Use this class to modify the core plugin template.
 *
 * @author WPerfekt
 * @package Wacara_Theme
 * @version 0.0.1
 */

namespace Wacara_Theme;

use Wacara\Event;
use Wacara\UI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara_Theme\Customizer' ) ) {

	/**
	 * Class Customizer
	 *
	 * @package Wacara_Theme
	 */
	class Customizer {

		/**
		 * Instance vriable.
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Singleton.
		 *
		 * @return Customizer|null
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Customizer constructor.
		 */
		private function __construct() {
			$this->customize_event();
		}

		/**
		 * Customize event.
		 */
		private function customize_event() {

			// Remove the call to action alert.
			remove_action( 'wacara_before_event_masthead', [ UI::init(), 'event_cta_opening_callback' ], 10 );
			remove_action( 'wacara_before_event_masthead', [ UI::init(), 'event_cta_content_callback' ], 20 );
			remove_action( 'wacara_before_event_masthead', [ UI::init(), 'event_cta_closing_callback' ], 30 );

			// Add nav menu.
			add_action( 'wacara_before_event_masthead', [ $this, 'event_nav_menu_callback' ], 10, 1 );
			// Modify masthead class.
			add_filter( 'wacara_filter_event_opening_masthead_args', [ $this, 'event_opening_masthead_args_callback' ], 10, 2 );

			// Remove expired content.
			remove_action( 'wacara_event_expired', [ UI::init(), 'event_expired_content_callback' ], 10 );
			// Replace expired content.
			add_action( 'wacara_event_expired', [ $this, 'event_expired_content_callback' ], 10 );

			// Add args in about section.
			add_filter( 'wacara_filter_event_about_section_args', [ $this, 'event_about_args_callback' ], 10, 2 );

			// Add args in location section.
			add_filter( 'wacara_filter_event_opening_section_location_args', [ $this, 'event_opening_location_args_callback' ], 10, 2 );

		}

		/**
		 * Callback for rendering event nav menu.
		 *
		 * @param Event $event object of the current event.
		 */
		public function event_nav_menu_callback( $event ) {
			$nav_menu       = $event->get_nav_menus();
			$maybe_logo_url = $event->get_logo_url();
			$nav_args       = [
				'nav_logo_url' => $maybe_logo_url,
				'nav_items'    => $nav_menu,
			];

			Helper::render_local_template( 'event/top-nav', $nav_args, true );
		}

		/**
		 * Callback for modifying event opening masthead args.
		 *
		 * @param array $args current args.
		 * @param Event $event object of the current event.
		 *
		 * @return array
		 */
		public function event_opening_masthead_args_callback( $args, $event ) {
			$args['masthead_column'] = '4-5';

			return $args;
		}

		/**
		 * Callback for displaying expired content.
		 *
		 * @param Event $event object of the current event.
		 */
		public function event_expired_content_callback( $event ) {

			$exp_args = [
				'exp_title'   => __( 'Expired', 'wacara' ),
				'exp_content' => __( 'This event is already past', 'wacara' ),
			];

			Helper::render_local_template( 'event/expired', $exp_args, true );
		}

		/**
		 * Callback for modifying about section args.
		 *
		 * @param array $args current args.
		 * @param Event $event object of the current event.
		 *
		 * @return array
		 */
		public function event_about_args_callback( $args, $event ) {
			$location       = \Wacara\Helper::get_post_meta( 'location', $event->post_id );
			$speakers       = (array) \Wacara\Helper::get_post_meta( 'speakers', $event->post_id, true );
			$speakers_count = count( $speakers );
			/* translators: %s : total speakers */
			$args['about_user']     = sprintf( __( 'We have %s amazing and talented speaker(s)', 'wacara' ), $speakers_count );
			$args['about_location'] = \Wacara\Helper::get_location_paragraph( $location );
			$args['about_time']     = $event->get_event_date_time_paragraph();

			return $args;
		}

		/**
		 * Callback for modifying location opening section args.
		 *
		 * @param array $args current args.
		 * @param Event $event object of the current event.
		 *
		 * @return array
		 */
		public function event_opening_location_args_callback( $args, $event ) {

			// Fetch location detail.
			$location = \Wacara\Helper::get_post_meta( 'location', $event->post_id );
			$photo_id = \Wacara\Helper::get_post_meta( 'photo_id', $location );

			// Validate the location photo.
			if ( $photo_id ) {

				$image_url = wp_get_attachment_image_url( $photo_id, 'large' );

				$style_properties = [
					'background-image' => "url({$image_url})",
				];

				$inline_styles = \Wacara\Helper::convert_properties_to_inline_styles( $style_properties, false );

				$args['custom_attributes'] = [
					'style' => $inline_styles,
				];
			}

			return $args;
		}
	}

	Customizer::init();
}
