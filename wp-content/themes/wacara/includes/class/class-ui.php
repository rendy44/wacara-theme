<?php
/**
 * Use this class to define default layout, such as header and footer
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Skeleton\UI' ) ) {

	/**
	 * Class UI
	 *
	 * @package Skeleton
	 */
	class UI {

		/**
		 * Instance variable
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Singleton
		 *
		 * @return null|UI
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * UI constructor.
		 */
		private function __construct() {
			add_action( 'sk_header_content', [ $this, 'header_open_tag_callback' ], 10 );
			add_action( 'sk_header_content', [ $this, 'maybe_small_header_callback' ], 15 );
			add_action( 'sk_header_content', [ $this, 'header_navbar_callback' ], 20 );
			add_action( 'sk_footer_content', [ $this, 'footer_close_tag_callback' ], 50 );
			add_filter( 'sk_input_field', [ $this, 'input_field_callback' ], 10, 4 );
			add_filter( 'sk_input_field_event', [ $this, 'input_field_event_callback' ], 10, 2 );
		}

		/**
		 * Render header open tag
		 */
		public function header_open_tag_callback() {
			echo Template::render( 'global/header' ); // phpcs:ignore
		}

		/**
		 * Render small header
		 */
		public function maybe_small_header_callback() {
			if ( ! is_front_page() ) { // we don't need small header in front page, since front page already has a full-page header.
				/* translators: %s: search term */
				$header_subtitle = '';
				if ( ! isset( $header_title ) ) {
					if ( is_archive() ) {
						$header_title = get_the_archive_title();
					} elseif ( is_search() ) {
						// translators: %s: search term.
						$header_title = sprintf( __( 'Search Results for "%s"', 'wacara' ), get_search_query() );
					} elseif ( is_404() ) {
						$header_title = __( 'Not Found', 'wacara' );
					} elseif ( is_singular() ) {
						$header_title = get_the_title();
						if ( is_singular( 'participant' ) ) {
							$event_id    = Helper::get_post_meta( 'event_id', get_the_ID() );
							$event_title = get_the_title( $event_id );
							/* translators: %s: event title name */
							$header_subtitle = sprintf( __( 'You are about to register to %s', 'wacara' ), $event_title );
						}
					} else {
						$header_title = single_post_title( '', false );
					}
				}
				echo Template::render( // phpcs:ignore
					'global/header-small',
					[
						'title'    => $header_title,
						'subtitle' => $header_subtitle,
					]
				);
			}
		}

		/**
		 * Render header navbar
		 */
		public function header_navbar_callback() {
			echo Template::render( 'global/navbar', [ 'site_name' => get_bloginfo( 'name' ) ] ); // phpcs:ignore
		}

		/**
		 * Render footer close tag
		 */
		public function footer_close_tag_callback() {
			echo Template::render( 'global/footer', [ 'content' => '&copy; ' . date( 'Y' ) . ' ' . get_bloginfo( 'name' ) ] ); // phpcs:ignore
		}

		/**
		 * Callback for rendering single input field.
		 *
		 * @param string $field_id       will be used to print field id and fied name.
		 * @param string $field_type     input type for simple text field, default = 'text'.
		 * @param string $field_required whether set field as required or not.
		 * @param string $value          default value of the input.
		 *
		 * @return string
		 */
		public function input_field_callback( $field_id, $field_type = 'text', $field_required = 'required', $value = '' ) {
			$result = Template::render(
				'global/form-input-text',
				[
					'field_id'       => $field_id,
					'field_type'     => $field_type,
					'field_required' => $field_required,
					'field_value'    => $value,
				]
			);

			return $result;
		}

		/**
		 * Callback for rendering event field.
		 *
		 * @param string $event_id event id.
		 * @param string $key      field key.
		 *
		 * @return string
		 */
		public function input_field_event_callback( $event_id, $key ) {
			$result         = '';
			$use_field      = Helper::get_post_meta( $key, $event_id );
			$field_label    = Helper::get_post_meta( $key . '_field_name', $event_id );
			$field_required = Helper::get_post_meta( $key . '_required', $event_id );

			if ( $use_field ) {
				$result .= '<div class="form-group">';
				$result .= '<label for="' . $key . '">' . $field_label . '</label>';
				$result .= apply_filters( 'sk_input_field', $key, 'text', $field_required ? 'required' : '' );
				$result .= '</div>';
			}

			return $result;
		}
	}
}

UI::init();
