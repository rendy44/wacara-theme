<?php
/**
 * Use this class to modify the core plugin template.
 *
 * @author Rendy
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

			// Remove expired content.
			remove_action( 'wacara_event_expired', [ UI::init(), 'event_expired_content_callback' ], 10 );
			// Replace expired content.
			add_action( 'wacara_event_expired', [ $this, 'event_expired_content_callback' ], 10 );

			// Add args in about section.
			add_filter( 'wacara_filter_about_section_args', [ $this, 'event_about_args_callback' ], 10, 2 );
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
			$args['title'] = $event->post_title;

			return $args;
		}
	}

	Customizer::init();
}
