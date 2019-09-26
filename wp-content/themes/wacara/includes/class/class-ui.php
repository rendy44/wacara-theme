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
			// we don't need small header in front page, since front page already has a full-page header.
			if ( ! is_front_page() && ! is_page_template( 'page-templates/event-checkin.php' ) ) {
				$header_subtitle = '';
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
					} elseif ( is_singular( 'event' ) ) {
						// Remove title for event landing page because we do not need this small header.
						$header_title = '';
					}
				} else {
					$header_title = single_post_title( '', false );
				}

				/**
				 * Perform the filter to modify page title.
				 *
				 * @param string $header_title current page title.
				 */
				$header_title = apply_filters( 'wacara_filter_page_title', $header_title );

				// Only render the small header id header title is actually defined.
				if ( $header_title ) {

					// Render the header.
					echo Template::render( // phpcs:ignore
						'global/header-small',
						[
							'title'    => $header_title,
							'subtitle' => $header_subtitle,
						]
					);
				}
			}
		}

		/**
		 * Render header navbar
		 */
		public function header_navbar_callback() {
			// Hide navbar on landing page.
			if ( ! is_page_template( 'page-templates/event-checkin.php' ) ) {
				$use_full_nav = true;
				$post_id      = get_the_ID();
				if ( is_singular( 'participant' ) ) {
					$use_full_nav = false;
					$post_id      = Helper::get_post_meta( 'event_id', get_the_ID() );
				}
				$logo_url  = Helper::get_event_logo_url( $post_id );
				$nav_class = '';

				/**
				 * Perform filter to modify navbar extra class.
				 *
				 * @param string $nav_class original navbar class.
				 */
				$final_nav_class = apply_filters( 'wacara_navbar_extra_class', $nav_class );

				echo Template::render( // phpcs:ignore
					'global/navbar',
					[
						'nav_class'    => $final_nav_class,
						'logo_url'     => $logo_url,
						'use_full_nav' => $use_full_nav,
						'home_link'    => ! $use_full_nav ? get_permalink( $post_id ) : '#masthead',
					]
				);
			}
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

		/**
		 * Get header extra classes.
		 *
		 * @param string $width     half|center header width.
		 * @param string $scheme    light|dark header color scheme.
		 * @param string $alignment left|center|right header alignment.
		 *
		 * @return array
		 */
		public static function get_header_extra_class( $width, $scheme, $alignment ) {
			$result = [ $width, '', '' ];
			if ( 'center' === $width ) {
				$result[0] .= ' ' . $scheme;
				$result[1] .= 'col-lg-8 col-md-10 mx-auto';
			} else {
				$result[1] .= 'col-lg-6';
				if ( 'right' === $alignment ) {
					$result[0] .= ' right';
					$result[2] .= 'justify-content-end';
				}
			}
			$result[0] .= ' text-' . $alignment;

			return $result;
		}

		/**
		 * Generate header background image style for event.
		 *
		 * @param string $event_background_image  event background image.
		 * @param string $header_background_image header background image.
		 *
		 * @return string
		 */
		public static function generate_header_background_image( $event_background_image, $header_background_image ) {
			$result   = '';
			$image_id = $event_background_image ? $event_background_image : $header_background_image;
			if ( $image_id ) {
				$image_url = wp_get_attachment_image_url( $image_id, 'large' );
				$result    = 'background-image:url(' . $image_url . ')';
			}

			return $result;
		}
	}
}

UI::init();
