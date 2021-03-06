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
use Wacara\Event_Location;
use Wacara\Registrant;
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
			$this->customize_registrant();
			$this->customize_other();
			$this->customize_metabox();
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
		 * Customize registrant.
		 */
		private function customize_registrant() {

			// Add top nav in registrant.
			add_action( 'wacara_before_registrant_masthead', [ $this, 'registrant_top_nav_callback' ], 5, 1 );

			// Add args in registrant masthead.
			add_filter( 'wacara_filter_registrant_opening_masthead_args', [ $this, 'registrant_opening_masthead_args_callback' ], 10, 2 );
		}

		/**
		 * Other customization.
		 */
		private function customize_other() {

			// Add opening container before self checkin.
			add_action( 'wacara_before_self_checkin_content', [ $this, 'checkin_form_opening_container_callback' ], 10 );
			add_action( 'wacara_before_self_checkin_form', [ $this, 'site_logo_callback' ], 10 );

			// Add closing container after self checkin.
			add_action( 'wacara_after_self_checkin_content', [ $this, 'checkin_form_closing_container_callback' ], 50 );
		}

		/**
		 * Customize metabox.
		 */
		private function customize_metabox() {
			add_filter( 'wacara_filter_event_design_metabox_tabs_args', [ $this, 'event_design_args_callback' ], 10, 1 );
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

			// Fetch details.
			$speakers       = (array) \Wacara\Helper::get_post_meta( 'speakers', $event->post_id, true );
			$speakers_count = count( $speakers );
			/* translators: %s : total speakers */
			$args['about_user']     = sprintf( __( 'We have %s amazing and talented speaker(s)', 'wacara' ), $speakers_count );
			$args['about_location'] = $event->get_location_object()->get_location_paragraph();
			$args['about_time']     = $event->get_event_date_time_paragraph();
			$args['about_cta_url']  = Helper::build_event_add_to_calendar_url( $event );

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

			// Instance location.
			$location = $event->get_location_object();

			// Validate the location photo.
			if ( $location->get_location_photo_id() ) {

				$style_properties = [
					'background-image' => "url({$location->get_location_photo_url('large')})",
				];

				$inline_styles = \Wacara\Helper::convert_properties_to_inline_styles( $style_properties, false );

				$args['custom_attributes'] = [
					'style' => $inline_styles,
				];
			}

			return $args;
		}

		/**
		 * Callback for adding top nav in registrant page.
		 *
		 * @param Registrant $registrant object of the current registrant.
		 */
		public function registrant_top_nav_callback( $registrant ) {

			// Instance event.
			$event = $registrant->get_event_object();

			$nav_args = [
				'nav_logo_url' => $event->get_logo_url(),
				'nav_home_url' => $event->post_url,
			];

			Helper::render_local_template( 'registrant/top-nav', $nav_args, true );
		}

		/**
		 * Callback for modifying registrant masthead args.
		 *
		 * @param array      $args current args.
		 * @param Registrant $registrant object of the current registrant.
		 *
		 * @return array
		 */
		public function registrant_opening_masthead_args_callback( $args, $registrant ) {

			// Get registrant detail.
			$event              = $registrant->get_event_object();
			$maybe_bg_image_url = $event->get_background_image_url( 'large' );

			// Embed background image url.
			$args['masthead_bg_image_url'] = $maybe_bg_image_url;

			return $args;
		}

		/**
		 * Callback for adding opening container in checkin form.
		 */
		public function checkin_form_opening_container_callback() {
			?>
			<div class="frow-container">
			<?php
		}

		public function site_logo_callback() {
			?>
			<div class="wcr-checkin-logo-wrapper">
				<?php
				/* translators: %1$s : site logo url, %2$s : site name */
				echo sprintf( '<img src="%1$s" class="wcr-image wcr-checkin-logo" alt="%2$s">', \Wacara\Helper::get_site_logo_url(), get_bloginfo( 'name' ) );
				?>
			</div>
			<?php
		}

		/**
		 * Callback for adding closing container in checkin form.
		 */
		public function checkin_form_closing_container_callback() {
			?>
			</div>
			<?php
		}

		/**
		 * Callback for modifying event design metabox args.
		 *
		 * @param array $args current args.
		 *
		 * @return array
		 */
		public function event_design_args_callback( $args ) {
			unset( $args['tabs'][3]['fields'][0] ); // Remove section title.
			unset( $args['tabs'][3]['fields'][1] ); // Remove section subtitle.
			unset( $args['tabs'][3]['fields'][2] ); // Remove section description.

			return $args;
		}
	}

	Customizer::init();
}
