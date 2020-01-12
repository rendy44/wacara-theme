<?php
/**
 * Use this class to define default layout, such as header and footer
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\UI' ) ) {

	/**
	 * Class UI
	 *
	 * @package Wacara
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
//			add_action( 'sk_header_content', [ $this, 'header_open_tag_callback' ], 10 );
//			add_action( 'sk_header_content', [ $this, 'maybe_small_header_callback' ], 15 );
//			add_action( 'sk_header_content', [ $this, 'header_navbar_callback' ], 20 );
//			add_action( 'sk_footer_content', [ $this, 'footer_close_tag_callback' ], 50 );
			add_filter( 'sk_input_field', [ $this, 'input_field_callback' ], 10, 4 );
			add_filter( 'sk_input_field_event', [ $this, 'input_field_event_callback' ], 10, 2 );

			// Render the expired content.
			add_action( 'wacara_before_displaying_event_expired', [ $this, 'render_expired_opening_callback' ], 10, 1 );
			add_action( 'wacara_before_displaying_event_expired', [ $this, 'render_expired_content_callback' ], 20, 1 );
			add_action( 'wacara_after_displaying_event_expired', [ $this, 'render_expired_closing_callback' ], 50, 1 );

			// Render the masthead section.
			add_action( 'wacara_render_masthead_section', [ $this, 'render_masthead_opening_callback' ], 10, 2 );
			add_action( 'wacara_render_masthead_section', [ $this, 'render_masthead_content_callback' ], 20, 2 );
			add_action( 'wacara_render_masthead_section', [ $this, 'render_masthead_countdown_callback' ], 30, 2 );
			add_action( 'wacara_render_masthead_section', [ $this, 'render_masthead_closing_callback' ], 40, 2 );

			// Render the about section.
			add_action( 'wacara_render_about_section', [ $this, 'render_about_section_callback' ], 10, 4 );

			// Render the speakers section.
			add_action( 'wacara_render_speakers_section', [ $this, 'render_speakers_section_callback' ], 10, 4 );

			// Render the venue section.
			add_action( 'wacara_render_venue_section', [ $this, 'render_venue_section_callback' ], 10, 4 );

			// Render the gallery section.
			add_action( 'wacara_render_gallery_section', [ $this, 'render_gallery_section_callback' ], 10, 4 );

			// Render the sponsors section.
			add_action( 'wacara_render_sponsors_section', [ $this, 'render_sponsors_section_callback' ], 10, 4 );

			// Render the schedule section.
			add_action( 'wacara_render_schedule_section', [ $this, 'render_schedule_section_callback' ], 10, 4 );

			// Render the pricing section.
			add_action( 'wacara_render_pricing_section', [ $this, 'render_pricing_section_callback' ], 10, 4 );
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
					if ( is_singular( 'registrant' ) ) {
						$header_title = __( 'Payment', 'wacara' );
						$event_id     = Helper::get_post_meta( 'event_id', get_the_ID() );
						$event_title  = get_the_title( $event_id );
						/* translators: %s: event title name */
						$header_subtitle = sprintf( __( 'You are about to register to %s', 'wacara' ), $event_title );
					} elseif ( is_singular( 'event' ) ) {
						$header_title = get_the_title();
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
				if ( is_singular( 'registrant' ) ) {
					$use_full_nav = false;
					$post_id      = Helper::get_post_meta( 'event_id', get_the_ID() );
				}
				$logo_url  = Helper::get_event_logo_url( $post_id );
				$nav_class = '';
				$nav_items = [];

				/**
				 * Perform filter to modify navbar extra class.
				 *
				 * @param string $nav_class original navbar class.
				 */
				$final_nav_class = apply_filters( 'wacara_navbar_extra_class', $nav_class );

				/**
				 * Apply filters to modify navbar items.
				 *
				 * @param array $nav_items original navbar items.
				 */
				$nav_items = apply_filters( 'wacara_navbar_items', $nav_items );

				echo Template::render( // phpcs:ignore
					'global/navbar',
					[
						'nav_items'    => $nav_items,
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
		 * @param string $field_id will be used to print field id and fied name.
		 * @param string $field_type input type for simple text field, default = 'text'.
		 * @param string $field_required whether set field as required or not.
		 * @param string $value default value of the input.
		 *
		 * @return string
		 */
		public function input_field_callback( $field_id, $field_type = 'text', $field_required = 'required', $value = '' ) {
			return Template::render(
				'global/form-input-text',
				[
					'field_id'       => $field_id,
					'field_type'     => $field_type,
					'field_required' => $field_required,
					'field_value'    => $value,
				]
			);
		}

		/**
		 * Callback for rendering event field.
		 *
		 * @param string $event_id event id.
		 * @param string $key field key.
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
		 * Callback for rendering expired event opening tag.
		 *
		 * @param Event $event the object of the current event.
		 */
		public function render_expired_opening_callback( $event ) {
			?>
            <section class="bg-light expired">
            <div class="container h-100">
            <div class="row h-100 align-items-center">
            <div class="col-lg-8 mx-auto text-center mb-3">
			<?php
		}

		/**
		 * Callback for rendering expired event content.
		 *
		 * @param Event $event the object of the current event.
		 */
		public function render_expired_content_callback( $event ) {
			$expired_title   = __( 'Expired', 'wacara' );
			$expired_content = __( 'This event is already past', 'wacara' );

			/**
			 * Wacara expired title filter.
			 *
			 * @param string $expired_title the current used title.
			 * @param Event $event the object of the current event.
			 */
			$expired_title = apply_filters( 'wacara_filter_expired_title', $expired_title, $event );

			/**
			 * Wacara expired content filter.
			 *
			 * @param string $expired_content the current used content.
			 * @param Event $expired_content the object of the current event.
			 */
			$expired_content = apply_filters( 'wacara_filter_expired_content', $expired_content, $event );

			$expired_args = [
				'expired_title'   => $expired_title,
				'expired_content' => $expired_content,
			];

			Template::render( 'event/expired', $expired_args, true );
		}

		/**
		 * Callback for rendering expired event closing tag.
		 *
		 * @param Event $event the object of the current event.
		 */
		public function render_expired_closing_callback( $event ) {
			?>
            </div>
            </div>
            </div>
            </section>
			<?php

		}

		/**
		 * Callback for rendering masthead opening tag.
		 *
		 * @param Event $event the object of the current event.
		 * @param string $header_template the id of selected header template of the current event.
		 */
		public function render_masthead_opening_callback( $event, $header_template ) {
			$header_width           = Helper::get_post_meta( 'content_width', $header_template );
			$header_scheme          = Helper::get_post_meta( 'color_scheme', $header_template );
			$header_alignment       = Helper::get_post_meta( 'content_alignment', $header_template );
			$header_extra_class     = self::get_header_extra_class( $header_width, $header_scheme, $header_alignment );
			$event_background_image = Helper::get_post_meta( 'background_image_id', $event->post_id );
			$header_default_image   = Helper::get_post_meta( 'default_image_id', $header_template );
			$background_image       = self::generate_header_background_image( $event_background_image, $header_default_image );
			?>
            <header class="masthead <?php echo esc_attr( $header_extra_class[0] ); ?>" id="masthead" data-aos="zoom-in"
            style="<?php echo esc_attr( $background_image ); ?>">
            <div class="container h-100">
            <div class="row h-100 align-items-center <?php echo esc_attr( $header_extra_class[2] ); ?>">
            <div class="<?php echo esc_attr( $header_extra_class[1] ); ?>">
			<?php
		}

		/**
		 * Callback for rendering masthead section.
		 *
		 * @param Event $event the object of the current event.
		 * @param string $header_template the id of selected header template of the current event.
		 */
		public function render_masthead_content_callback( $event, $header_template ) {
			$date_start            = Helper::get_post_meta( 'date_start', $event->post_id );
			$location              = Helper::get_post_meta( 'location', $event->post_id );
			$location_country_code = Helper::get_post_meta( 'country', $location );
			$location_province     = Helper::get_post_meta( 'province', $location );
			$event_title           = get_the_title( $event->post_id );
			$masthead_args         = [
				'title'   => Helper::split_title( $event_title ),
				'excerpt' => Helper::convert_date( $date_start, false, true ) . ' - ' . $location_province . ', ' . $location_country_code,
			];
			Template::render( 'event/masthead', $masthead_args, true );
		}

		/**
		 * Callback for rendering masthead countdown.
		 *
		 * @param Event $event the object of the current event.
		 * @param string $header_template the id of selected header template of the current event.
		 */
		public function render_masthead_countdown_callback( $event, $header_template ) {
			$enable_countdown = Helper::get_post_meta( 'countdown_content', $header_template );
			if ( 'on' === $enable_countdown ) {
				$date_start    = Helper::get_post_meta( 'date_start', $event->post_id );
				$masthead_args = [
					'date_start' => $date_start,
				];
				Template::render( 'event/masthead-countdown', $masthead_args, true );
			}
		}

		/**
		 * Callback for rendering masthead closing tag.
		 *
		 * @param Event $event the object of the current event.
		 * @param string $header_template the id of selected header template of the current event.
		 */
		public function render_masthead_closing_callback( $event, $header_template ) {
			?>
            </div>
            </div>
            </div>
            </header>
			<?php
		}

		/**
		 * Callback for rendering about section.
		 *
		 * @param Event $event the object of current event.
		 * @param string $section_class the css class of section.
		 * @param string $section_title the title of section.
		 * @param string $section_subtitle the subtitle of section.
		 */
		public function render_about_section_callback( $event, $section_class, $section_title, $section_subtitle ) {
			$location   = Helper::get_post_meta( 'location', $event->post_id );
			$about_args = [
				'class'       => $section_class,
				'title'       => $section_title,
				'subtitle'    => $section_subtitle,
				'description' => Helper::get_post_meta( 'description', $event->post_id ),
				'location'    => Helper::get_location_paragraph( $location ),
				'time'        => $event->get_event_date_time_paragraph(),
			];
			echo Template::render( 'event/about', $about_args ); // phpcs:ignore
		}

		/**
		 * Callback for rendering speakers section.
		 *
		 * @param Event $event the object of current event.
		 * @param string $section_class the css class of section.
		 * @param string $section_title the title of section.
		 * @param string $section_subtitle the subtitle of section.
		 */
		public function render_speakers_section_callback( $event, $section_class, $section_title, $section_subtitle ) {
			$speakers_arr = [];
			$speakers     = Helper::get_post_meta( 'speakers', $event->post_id );
			if ( ! empty( $speakers ) ) {
				foreach ( $speakers as $speaker ) {
					$speakers_arr[] = [
						'image'     => has_post_thumbnail( $speaker ) ? get_the_post_thumbnail_url( $speaker ) : WACARA_URI . '/assets/img/user-placeholder.jpg',
						'name'      => get_the_title( $speaker ),
						'position'  => Helper::get_post_meta( 'position', $speaker ),
						'facebook'  => Helper::get_post_meta( 'facebook', $speaker ),
						'twitter'   => Helper::get_post_meta( 'twitter', $speaker ),
						'website'   => Helper::get_post_meta( 'website', $speaker ),
						'linkedin'  => Helper::get_post_meta( 'linkedin', $speaker ),
						'instagram' => Helper::get_post_meta( 'instagram', $speaker ),
						'youtube'   => Helper::get_post_meta( 'youtube', $speaker ),
					];
				}

				$speakers_args = [
					'class'    => $section_class,
					'title'    => $section_title,
					'subtitle' => $section_subtitle,
					'speakers' => $speakers_arr,
				];
				echo Template::render( 'event/speakers', $speakers_args ); // phpcs:ignore
			}
		}

		/**
		 * Callback for rendering venue section.
		 *
		 * @param Event $event the object of current event.
		 * @param string $section_class the css class of section.
		 * @param string $section_title the title of section.
		 * @param string $section_subtitle the subtitle of section.
		 */
		public function render_venue_section_callback( $event, $section_class, $section_title, $section_subtitle ) {
			$location   = Helper::get_post_meta( 'location', $event->post_id );
			$venue_args = [
				'class'                => $section_class,
				'title'                => $section_title,
				'subtitle'             => $section_subtitle,
				'sliders'              => Helper::get_post_meta( 'photo', $location ),
				'location_name'        => Helper::get_post_meta( 'name', $location ),
				'location_description' => Helper::get_post_meta( 'description', $location ),
			];
			echo Template::render( 'event/venue', $venue_args ); // phpcs:ignore
		}

		/**
		 * Callback for rendering gallery section.
		 *
		 * @param Event $event the object of current event.
		 * @param string $section_class the css class of section.
		 * @param string $section_title the title of section.
		 * @param string $section_subtitle the subtitle of section.
		 */
		public function render_gallery_section_callback( $event, $section_class, $section_title, $section_subtitle ) {
			$gallery = Helper::get_post_meta( 'gallery', $event->post_id );
			if ( ! empty( $gallery ) ) {
				$gallery_args = [
					'class'    => $section_class,
					'title'    => $section_title,
					'subtitle' => $section_subtitle,
					'gallery'  => $gallery,
				];
				echo Template::render( 'event/gallery', $gallery_args ); // phpcs:ignore
			}
		}

		/**
		 * Callback for rendering sponsors section.
		 *
		 * @param Event $event the object of current event.
		 * @param string $section_class the css class of section.
		 * @param string $section_title the title of section.
		 * @param string $section_subtitle the subtitle of section.
		 */
		public function render_sponsors_section_callback( $event, $section_class, $section_title, $section_subtitle ) {
			$sponsors = Helper::get_post_meta( 'sponsors', $event->post_id );
			if ( ! empty( $sponsors ) ) {
				$sponsors_args = [
					'class'    => $section_class,
					'title'    => $section_title,
					'subtitle' => $section_subtitle,
					'sponsors' => $sponsors,
				];
				echo Template::render( 'event/sponsors', $sponsors_args ); // phpcs:ignore
			}
		}

		/**
		 * Callback for rendering schedule section.
		 *
		 * @param Event $event the object of current event.
		 * @param string $section_class the css class of section.
		 * @param string $section_title the title of section.
		 * @param string $section_subtitle the subtitle of section.
		 */
		public function render_schedule_section_callback( $event, $section_class, $section_title, $section_subtitle ) {
			$schedules = Helper::get_post_meta( 'schedules', $event->post_id );
			if ( ! empty( $schedules ) ) {
				$schedule_args = [
					'class'     => $section_class,
					'title'     => $section_title,
					'subtitle'  => $section_subtitle,
					'schedules' => $schedules,
				];
				echo Template::render( 'event/schedule', $schedule_args ); // phpcs:ignore
			}
		}

		/**
		 * Callback for rendering pricing section.
		 *
		 * @param Event $event the object of current event.
		 * @param string $section_class the css class of section.
		 * @param string $section_title the title of section.
		 * @param string $section_subtitle the subtitle of section.
		 */
		public function render_pricing_section_callback( $event, $section_class, $section_title, $section_subtitle ) {
			$allow_registration = Helper::get_post_meta( 'allow_register', $event->post_id );

			// Set default template name.
			$default_template = 'event/directly';

			// Prepare the args.
			$pricing_args = [
				'class'    => $section_class,
				'title'    => $section_title,
				'subtitle' => $section_subtitle,
				'event_id' => $event->post_id,
			];

			// Only render the pricing section if registration is required to join the event.
			if ( 'on' === $allow_registration ) {
				$pricing_arr = [];
				$pricing     = Helper::get_post_meta( 'pricing', $event->post_id );
				if ( ! empty( $pricing ) ) {
					foreach ( $pricing as $price ) {
						$currency_code = Helper::get_post_meta( 'currency', $price );
						$pricing_arr[] = [
							'id'       => $price,
							'name'     => get_the_title( $price ),
							'price'    => Helper::get_post_meta( 'price', $price ),
							'currency' => $currency_code,
							'symbol'   => Helper::get_currency_symbol_by_code( $currency_code ),
							'pros'     => Helper::get_post_meta( 'pros', $price ),
							'cons'     => Helper::get_post_meta( 'cons', $price ),
						];
					}
				}

				// Add more args.
				$pricing_args['price_lists'] = $pricing_arr;

				// Alter the default template.
				$default_template = 'event/pricing';
			}

			// Render the pricing section.
			echo Template::render( $default_template, $pricing_args ); // phpcs:ignore
		}

		/**
		 * Get header extra classes.
		 *
		 * @param string $width half|center header width.
		 * @param string $scheme light|dark header color scheme.
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
		 * @param string $event_background_image event background image.
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

	UI::init();
}
