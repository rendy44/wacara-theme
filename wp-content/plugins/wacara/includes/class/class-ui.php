<?php
/**
 * Use this class to define default layout, such as header and footer
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
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
			// Render form field.
			add_filter( 'wacara_input_field', [ $this, 'input_field_callback' ], 10, 4 );
			add_filter( 'wacara_input_field_event', [ $this, 'input_field_event_callback' ], 10, 2 );

			// Render the expired content.
			add_action( 'wacara_before_event_expired', [ $this, 'event_expired_opening_callback' ], 10, 1 );
			add_action( 'wacara_event_expired', [ $this, 'event_expired_content_callback' ], 10, 1 );
			add_action( 'wacara_after_event_expired', [ $this, 'event_expired_closing_callback' ], 50, 1 );

			// Render call to action section.
			add_action( 'wacara_before_event_masthead', [ $this, 'event_cta_opening_callback' ], 10, 1 );
			add_action( 'wacara_before_event_masthead', [ $this, 'event_cta_content_callback' ], 20, 1 );
			add_action( 'wacara_before_event_masthead', [ $this, 'event_cta_closing_callback' ], 50, 1 );

			// Render the masthead section.
			add_action( 'wacara_event_masthead', [ $this, 'event_masthead_opening_callback' ], 10, 2 );
			add_action( 'wacara_event_masthead', [ $this, 'event_masthead_content_callback' ], 20, 2 );
			add_action( 'wacara_event_masthead', [ $this, 'event_masthead_countdown_callback' ], 30, 2 );
			add_action( 'wacara_event_masthead', [ $this, 'event_masthead_closing_callback' ], 40, 2 );

			// Render the sections.
			add_filter( 'wacara_event_section_class', [ $this, 'event_section_class_callback' ], 10, 3 );
			add_action( 'wacara_before_event_section', [ $this, 'event_section_opening_callback' ], 10, 6 );
			add_action( 'wacara_before_event_section', [ $this, 'maybe_event_section_title_callback' ], 20, 6 );
			add_action( 'wacara_after_event_section', [ $this, 'event_section_closing_callback' ], 50, 6 );

			// Render the about section.
			add_action( 'wacara_event_about_section', [ $this, 'event_about_section_callback' ], 10, 1 );

			// Render the speakers section.
			add_action( 'wacara_event_speakers_section', [ $this, 'event_speakers_section_callback' ], 10, 1 );

			// Render the location section.
			add_action( 'wacara_event_location_section', [ $this, 'event_location_section_callback' ], 10, 1 );

			// Render the gallery section.
			add_action( 'wacara_event_gallery_section', [ $this, 'event_gallery_section_callback' ], 10, 1 );

			// Render the sponsors section.
			add_action( 'wacara_event_sponsors_section', [ $this, 'event_sponsors_section_callback' ], 10, 1 );

			// Render the schedule section.
			add_action( 'wacara_event_schedule_section', [ $this, 'event_schedule_section_callback' ], 10, 1 );

			// Render the pricing section.
			add_action( 'wacara_event_pricing_section', [ $this, 'event_pricing_section_callback' ], 10, 1 );

			// Render registrant.
			add_action( 'wacara_before_registrant_masthead', [ $this, 'registrant_masthead_opening_callback' ], 10, 1 );
			add_action( 'wacara_registrant_masthead', [ $this, 'registrant_masthead_content_callback' ], 10, 2 );
			add_action( 'wacara_after_registrant_masthead', [ $this, 'registrant_masthead_closing_callback' ], 50, 1 );
			add_action( 'wacara_before_registrant_content', [ $this, 'registrant_section_opening_callback' ], 10, 1 );
			add_action( 'wacara_before_registrant_content', [ $this, 'registrant_before_content_wrapper_callback' ], 20, 1 );
			add_action( 'wacara_before_registrant_form_content', [ $this, 'registrant_form_opening_callback' ], 10, 1 );
			add_action( 'wacara_after_registrant_form_content', [ $this, 'registrant_form_closing_callback' ], 50, 1 );
			add_action( 'wacara_before_registrant_hold_content', [ $this, 'registrant_hold_opening_callback' ], 10, 3 );
			add_action( 'wacara_before_registrant_hold_content', [ $this, 'registrant_invoice_callback' ], 20, 3 );
			add_action( 'wacara_before_registrant_hold_content', [ $this, 'registrant_hold_opening_field_callback' ], 30, 3 );
			add_action( 'wacara_after_registrant_hold_content', [ $this, 'registrant_hold_closing_field_callback' ], 30, 3 );
			add_action( 'wacara_after_registrant_hold_content', [ $this, 'registrant_hold_submit_button_callback' ], 40, 3 );
			add_action( 'wacara_after_registrant_hold_content', [ $this, 'registrant_hold_hidden_field_callback' ], 50, 3 );
			add_action( 'wacara_after_registrant_hold_content', [ $this, 'registrant_hold_closing_callback' ], 60, 3 );
			add_action( 'wacara_before_registrant_custom_content', [ $this, 'registrant_hold_opening_callback' ], 10, 3 );
			add_action( 'wacara_after_registrant_custom_content', [ $this, 'registrant_hold_submit_button_callback' ], 40, 3 );
			add_action( 'wacara_after_registrant_custom_content', [ $this, 'registrant_hold_closing_callback' ], 50, 3 );
			add_action( 'wacara_after_registrant_content', [ $this, 'registrant_after_content_wrapper_callback' ], 40, 1 );
			add_action( 'wacara_after_registrant_content', [ $this, 'registrant_section_closing_callback' ], 50, 1 );

			// Render email template.
			add_action( 'wacara_header_global_email_template', [ $this, 'header_global_email_template' ], 10, 1 );
		}

		/**
		 * Callback for displaying single input field.
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
		 * Callback for displaying event field.
		 *
		 * @param Event  $event object of the selected event.
		 * @param string $key field key.
		 *
		 * @return string
		 */
		public function input_field_event_callback( $event, $key ) {
			$result         = '';
			$use_field      = Helper::get_post_meta( $key, $event->post_id );
			$field_label    = Helper::get_post_meta( $key . '_field_name', $event->post_id );
			$field_required = Helper::get_post_meta( $key . '_required', $event->post_id );

			if ( $use_field ) {
				$result .= "<div class='wcr-field-{$key} wcr-form-field-wrapper'>";
				$result .= '<label for="' . $key . '">' . $field_label . '</label>';
				$result .= apply_filters( 'wacara_input_field', $key, 'text', $field_required ? 'required' : '' );
				$result .= '</div>';
			}

			return $result;
		}

		/**
		 * Callback for displaying expired event opening tag.
		 *
		 * @param Event $event the object of the current event.
		 */
		public function event_expired_opening_callback( $event ) {
			$section_args = [
				'section_class' => 'wcr-section-expired',
				'section'       => 'expired',
			];

			/**
			 * Wacara filter opening event expired.
			 *
			 * @param array $section_args current args.
			 * @param Event $event object of the current event.
			 */
			$section_args = apply_filters( 'wacara_filter_event_opening_expired_args', $section_args, $event );

			Template::render( 'global/section-open', $section_args, true );
		}

		/**
		 * Callback for displaying expired event content.
		 *
		 * @param Event $event the object of the current event.
		 */
		public function event_expired_content_callback( $event ) {
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
				'section_title'    => $expired_title,
				'section_subtitle' => $expired_content,
			];

			/**
			 * Wacara filter event expired args.
			 *
			 * @param array $expired_args current args.
			 * @param Event $event object of the current event.
			 */
			$expired_args = apply_filters( 'wacara_filter_event_content_expired_args', $expired_args, $event );

			Template::render( 'global/section-title', $expired_args, true );
		}

		/**
		 * Callback for displaying expired event closing tag.
		 *
		 * @param Event $event the object of the current event.
		 */
		public function event_expired_closing_callback( $event ) {
			Template::render( 'global/section-close', [], true );
		}

		/**
		 * Callback for displaying call to action opening tag.
		 *
		 * @param Event $event object of the current event.
		 */
		public function event_cta_opening_callback( $event ) {
			?>
			<div class="wcr-event-alert wcr-alert-with-cta">
			<div class="frow-container">
			<div class="wcr-event-alert-content-wrapper">
			<?php
		}

		/**
		 * Callback for displaying call to action.
		 *
		 * @param Event $event object of the current event.
		 */
		public function event_cta_content_callback( $event ) {
			$cta_args = [
				'alert_title'   => __( 'Come Join Us', 'wacara' ),
				'alert_content' => __( 'Save your seat at the lowest price', 'wacara' ),
				'alert_button'  => __( 'Join Now', 'wacara' ),
			];

			/**
			 * Wacara filter event cta expired.
			 *
			 * @param array $cta_args current args.
			 * @param Event $event object of the current event.
			 */
			$cta_args = apply_filters( 'wacara_filter_event_cta_args', $cta_args, $event );

			Template::render( 'event/call-to-action', $cta_args, true );
		}

		/**
		 * Callback for displaying call to action closing tag.
		 *
		 * @param Event $event object of the current event.
		 */
		public function event_cta_closing_callback( $event ) {
			?>
			</div>
			</div>
			</div>
			<?php
		}

		/**
		 * Callback for displaying masthead opening tag.
		 *
		 * @param Event        $event the object of the current event.
		 * @param Event_Header $header object of the selected header template of the current event.
		 */
		public function event_masthead_opening_callback( $event, $header ) {

			// Get background image url of the event.
			$maybe_bg_image_url = $event->get_background_image_url( 'large' );

			// Adjust header class.
			switch ( $header->get_content_alignment() ) {
				case 'left':
					$masthead_alignment = 'wcr-justify-content-start';
					$column_alignment   = 'wcr-text-left';
					break;
				case 'right':
					$masthead_alignment = 'wcr-justify-content-end';
					$column_alignment   = 'wcr-text-right';
					break;
				default:
					$masthead_alignment = 'wcr-justify-content-center';
					$column_alignment   = 'wcr-text-center';
					break;
			}

			$masthead_args = [
				'masthead_class'           => 'wcr-event-header',
				'masthead_bg_image_url'    => $maybe_bg_image_url,
				'masthead_alignment_class' => $masthead_alignment,
				'column_alignment'         => $column_alignment,
			];

			// Maybe header using darken mode.
			if ( 'on' === $header->is_darken() ) {
				$masthead_args['masthead_class'] .= ' wcr-header-darken';
			}

			/**
			 * Wacara event masthead opening args filter hook.
			 *
			 * @param array $masthead_args current args.
			 * @param Event $event object of the current event.
			 */
			$masthead_args = apply_filters( 'wacara_filter_event_opening_masthead_args', $masthead_args, $event );

			Template::render( 'global/masthead-open', $masthead_args, true );
		}

		/**
		 * Callback for displaying masthead section.
		 *
		 * @param Event        $event object of the current event.
		 * @param Event_Header $header object of the selected header template of the current event.
		 */
		public function event_masthead_content_callback( $event, $header ) {

			// Instance location.
			$location = $event->get_location_object();

			// Get details.
			$headline = $event->get_headline();

			$masthead_args = [
				'header_title'    => $event->post_title,
				'header_headline' => $headline,
				'header_excerpt'  => $event->get_date_start() . ' - ' . $location->get_location_province() . ', ' . $location->get_location_country(),
			];

			// Use default content if headline is not defined.
			if ( ! $headline ) {
				$masthead_args['header_title']    = __( 'The Conference', 'wacara' );
				$masthead_args['header_headline'] = $event->post_title;
			}

			/**
			 * Wacara event masthead content args filter hook.
			 *
			 * @param array $masthead_args current args.
			 * @param Event $event object of the current event.
			 */
			$masthead_args = apply_filters( 'wacara_filter_event_content_masthead_args', $masthead_args, $event );

			Template::render( 'event/masthead', $masthead_args, true );
		}

		/**
		 * Callback for displaying masthead countdown.
		 *
		 * @param Event        $event object of the current event.
		 * @param Event_Header $header object of selected header template of the current event.
		 */
		public function event_masthead_countdown_callback( $event, $header ) {
			if ( $header->is_countdown_content() ) {
				$masthead_args = [
					'date_start' => $event->get_date_start( 'M j, Y H:i:s' ),
				];

				/**
				 * Wacara event countdown masthead args.
				 *
				 * @param array $masthead_args current args.
				 * @param Event $event object of the current event.
				 */
				$masthead_args = apply_filters( 'wacara_filter_event_countdown_masthead_args', $masthead_args, $event );

				Template::render( 'event/masthead-countdown', $masthead_args, true );
			}
		}

		/**
		 * Callback for displaying masthead closing tag.
		 *
		 * @param Event        $event object of the current event.
		 * @param Event_Header $header object of selected header template of the current event.
		 */
		public function event_masthead_closing_callback( $event, $header ) {
			Template::render( 'global/masthead-close', [], true );
		}

		/**
		 * Callback for filtering section classes.
		 *
		 * @param string $section_class current class of the section.
		 * @param string $section the name of the current section.
		 * @param int    $section_num ordering number of the current section.
		 *
		 * @return string
		 */
		public function event_section_class_callback( $section_class, $section, $section_num ) {
			$section_class .= " wcr-section-{$section} section-{$section_num}";

			return $section_class;
		}

		/**
		 * Callback for displaying section opening tag.
		 *
		 * @param string $section name of the selected section.
		 * @param Event  $event object of the current event.
		 * @param string $section_class css class of the selected section.
		 * @param string $section_title title of the selected section.
		 * @param string $section_subtitle subtitle of the selected section.
		 * @param string $section_description description of the selected section.
		 */
		public function event_section_opening_callback( $section, $event, $section_class, $section_title, $section_subtitle, $section_description ) {
			$section_args = [
				'section_class' => $section_class,
				'section'       => $section,
			];

			/**
			 * Wacara event section args filter hook.
			 *
			 * @param array $section_args current args.
			 * @param Event $event object of the current event.
			 */
			$section_args = apply_filters( "wacara_filter_event_opening_section_{$section}_args", $section_args, $event );

			Template::render( 'global/section-open', $section_args, true );
		}

		/**
		 * Callback for displaying section title.
		 *
		 * @param string $section name of the selected section.
		 * @param Event  $event object of the current event.
		 * @param string $section_class css class of the selected section.
		 * @param string $section_title title of the selected section.
		 * @param string $section_subtitle subtitle of the selected section.
		 * @param string $section_description description of the selected section.
		 */
		public function maybe_event_section_title_callback( $section, $event, $section_class, $section_title, $section_subtitle, $section_description ) {
			if ( $section_title || $section_subtitle ) {
				$section_args = [
					'section_title'       => $section_title,
					'section_subtitle'    => $section_subtitle,
					'section_description' => $section_description,
				];

				/**
				 * Wacara event title args filter hook.
				 *
				 * @param array $section_args current args.
				 * @param Event $event object of the current event.
				 */
				$section_args = apply_filters( "wacara_filter_event_title_section_{$section}_args", $section_args, $event );

				Template::render( 'global/section-title', $section_args, true );
			}
		}

		/**
		 * Callback for displaying section closing tag.
		 *
		 * @param string $section name of the selected section.
		 * @param Event  $event object of the current event.
		 * @param string $section_class css class of the selected section.
		 * @param string $section_title title of the selected section.
		 * @param string $section_subtitle subtitle of the selected section.
		 * @param string $section_description description of the selected section.
		 */
		public function event_section_closing_callback( $section, $event, $section_class, $section_title, $section_subtitle, $section_description ) {
			Template::render( 'global/section-close', [], true );
		}

		/**
		 * Callback for displaying about section.
		 *
		 * @param Event $event the object of current event.
		 */
		public function event_about_section_callback( $event ) {
			$about_args = [
				'description' => Helper::get_post_meta( 'about_description', $event->post_id ),
				'location'    => $event->get_location_object()->get_location_paragraph(),
				'time'        => $event->get_event_date_time_paragraph(),
			];

			/**
			 * Wacara about section args filter hook.
			 *
			 * @param array $about_args current args.
			 * @param Event $event object of the current event.
			 */
			$about_args = apply_filters( 'wacara_filter_event_about_section_args', $about_args, $event );

			Template::render( 'event/about', $about_args, true );
		}

		/**
		 * Callback for displaying speakers section.
		 *
		 * @param Event $event the object of current event.
		 */
		public function event_speakers_section_callback( $event ) {
			$speakers_arr = [];
			$speakers     = $event->get_speaker_ids();

			// Loop speaker.
			if ( ! empty( $speakers ) ) {
				foreach ( $speakers as $speaker_id ) {

					// Instance speaker.
					$speaker = new Event_Speaker( $speaker_id );

					$speakers_arr[] = [
						'image'     => has_post_thumbnail( $speaker_id ) ? get_the_post_thumbnail_url( $speaker_id ) : WACARA_URI . '/assets/img/user-placeholder.jpg',
						'name'      => $speaker->post_title,
						'position'  => $speaker->get_position(),
						'facebook'  => $speaker->get_facebook_url(),
						'twitter'   => $speaker->get_twitter_url(),
						'website'   => $speaker->get_website_url(),
						'linkedin'  => $speaker->get_linkedin_url(),
						'instagram' => $speaker->get_instagram_url(),
						'youtube'   => $speaker->get_youtube_url(),
					];
				}

				$speakers_args = [
					'speakers' => $speakers_arr,
				];

				/**
				 * Wacara speakers section args filter hook.
				 *
				 * @param array $speakers_args current args.
				 * @param Event $event object of the current event.
				 */
				$speakers_args = apply_filters( 'wacara_filter_event_speakers_section_args', $speakers_args, $event );

				Template::render( 'event/speakers', $speakers_args, true );
			}
		}

		/**
		 * Callback for displaying location section.
		 *
		 * @param Event $event the object of current event.
		 */
		public function event_location_section_callback( $event ) {
			// Instance location.
			$location = $event->get_location_object();

			$location_args = [
				'location_name'        => $location->get_location_name(),
				'location_description' => $location->get_location_description(),
				'location_address'     => $location->get_location_paragraph( false ),
			];

			// Add image url.
			if ( $location->get_location_photo_id() ) {
				$location_args['location_image'] = $location->get_location_photo_url( 'wacara-location-image' );
			}

			/**
			 * Wacara event location args filter hook.
			 *
			 * @param array $location_args current args.
			 * @param Event $event object of the current event.
			 */
			$location_args = apply_filters( 'wacara_filter_event_location_section_args', $location_args, $event );

			Template::render( 'event/location', $location_args, true );
		}

		/**
		 * Callback for displaying gallery section.
		 *
		 * @param Event $event the object of current event.
		 */
		public function event_gallery_section_callback( $event ) {
			$gallery = Helper::get_post_meta( 'gallery', $event->post_id );
			if ( ! empty( $gallery ) ) {
				$gallery_args = [
					'gallery' => $gallery,
				];

				/**
				 * Wacara gallery section args filter hook.
				 *
				 * @param array $gallery_args current args.
				 * @param Event $event object of the current event.
				 */
				$gallery_args = apply_filters( 'wacara_filter_gallery_section_args', $gallery_args, $event );

				Template::render( 'event/gallery', $gallery_args, true );
			}
		}

		/**
		 * Callback for displaying sponsors section.
		 *
		 * @param Event $event the object of current event.
		 */
		public function event_sponsors_section_callback( $event ) {
			$sponsors = Helper::get_post_meta( 'sponsors', $event->post_id );
			if ( ! empty( $sponsors ) ) {
				$sponsors_args = [
					'sponsors' => $sponsors,
				];

				/**
				 * Wacara sponsors section args filter hook.
				 *
				 * @param array $sponsors_args current args.
				 * @param Event $event object of the current event.
				 */
				$sponsors_args = apply_filters( 'wacara_filter_sponsors_section_args', $sponsors_args, $event );

				Template::render( 'event/sponsors', $sponsors_args, true );
			}
		}

		/**
		 * Callback for displaying schedule section.
		 *
		 * @param Event $event the object of current event.
		 */
		public function event_schedule_section_callback( $event ) {
			$schedules = Helper::get_post_meta( 'schedules', $event->post_id );
			if ( ! empty( $schedules ) ) {
				$schedule_args = [
					'schedules' => $schedules,
				];

				/**
				 * Wacara schedule section args filter hook.
				 *
				 * @param array $schedule_args current args.
				 * @param Event $event object of the current event.
				 */
				$schedule_args = apply_filters( 'wacara_filter_schedule_section_args', $schedule_args, $event );

				Template::render( 'event/schedule', $schedule_args, true );
			}
		}

		/**
		 * Callback for displaying pricing section.
		 *
		 * @param Event $event the object of current event.
		 */
		public function event_pricing_section_callback( $event ) {

			// Set default template name.
			$default_template = 'event/directly';

			// Set default message.
			$direct_message = __( 'This event does not require any registration, you can attend the event immediately', 'wacara' );

			/**
			 * Wacara event direct registration message filter hook.
			 *
			 * @param string $direct_message default message.
			 * @param Event $event object of the current message.
			 */
			$direct_message = apply_filters( 'wacara_filter_event_direct_registration_message', $direct_message, $event );

			// Prepare the args.
			$pricing_args = [
				'event_id'       => $event->post_id,
				'direct_message' => $direct_message,
			];

			// Only render the pricing section if registration is required to join the event.
			if ( $event->is_event_allows_register() ) {

				// Get pricing ids.
				$pricing_arr = [];
				$pricing_ids = $event->get_pricing_ids();

				// Loop all pricing ids.
				if ( ! empty( $pricing_ids ) ) {
					foreach ( $pricing_ids as $pricing_id ) {

						// Instance pricing.
						$pricing = new Event_Pricing( $pricing_id );

						$pricing_arr[] = [
							'id'          => $pricing->post_id,
							'name'        => $pricing->post_title,
							'price'       => $pricing->get_price(),
							'symbol'      => $pricing->get_currency_symbol(),
							'recommended' => $pricing->is_recommended(),
							'pros'        => $pricing->get_pros(),
							'cons'        => $pricing->get_cons(),
						];
					}
				}

				// Add more args.
				$pricing_args['price_lists'] = $pricing_arr;

				// Alter the default template.
				$default_template = 'event/pricing';
			}

			/**
			 * Wacara pricing section args filter hook.
			 *
			 * @param array $pricing_args current args.
			 * @param Event $event object of the current event.
			 */
			$pricing_args = apply_filters( 'wacara_filter_pricing_section_args', $pricing_args, $event );

			Template::render( $default_template, $pricing_args, true );
		}

		/**
		 * Callback for displaying masthead opening tag.
		 *
		 * @param Registrant $registrant the object of the current registrant.
		 */
		public function registrant_masthead_opening_callback( $registrant ) {
			$masthead_args = [
				'masthead_class' => 'wcr-header wcr-registrant-header',
			];

			/**
			 * Wacara registrant masthead args filter hook.
			 *
			 * @param array $masthead_args current args.
			 * @param Registrant object of the current registrant.
			 */
			$masthead_args = apply_filters( 'wacara_filter_registrant_opening_masthead_args', $masthead_args, $registrant );

			Template::render( 'global/masthead-open', $masthead_args, true );
		}

		/**
		 * Callback for displaying registrant masthead content.
		 *
		 * @param Registrant $registrant the object of the current registrant.
		 * @param string     $registrant_status the status of the registrant.
		 */
		public function registrant_masthead_content_callback( $registrant, $registrant_status ) {

			// Get event info.
			$event_title = $registrant->get_event_object()->post_title;

			/* translators: %s: event name */
			$masthead_desc = sprintf( __( 'You are about to register to %s', 'wacara' ), $event_title );

			switch ( $registrant_status ) {
				case 'done':
					/* translators: %s: event name */
					$masthead_desc = sprintf( __( 'You are successfully registered to %s', 'wacara' ), $event_title );
					break;
			}

			$masthead_args = [
				'masthead_title' => $registrant->post_title,
				'masthead_desc'  => $masthead_desc,
			];

			/**
			 * Wacara registrant masthead filter hook.
			 *
			 * @param array $masthead_args current args.
			 * @param Registrant $registrant object of the current registrant.
			 */
			$masthead_args = apply_filters( 'wacara_filter_registrant_masthead_args', $masthead_args, $registrant );

			Template::render( 'global/masthead-content', $masthead_args, true );
		}

		/**
		 * Callback for displaying masthead closing tag.
		 *
		 * @param Registrant $registrant the object of the current registrant.
		 */
		public function registrant_masthead_closing_callback( $registrant ) {
			Template::render( 'global/masthead-close', [], true );
		}

		/**
		 * Callback for displaying registrant section opening tag.
		 *
		 * @param Registrant $registrant the object of the current registrant.
		 */
		public function registrant_section_opening_callback( $registrant ) {
			$section_args = [
				'section_class' => 'wcr-registrant-section',
				'section'       => 'registrant-form',
			];

			Template::render( 'global/section-open', $section_args, true );
		}

		/**
		 * Callback for displaying extra wrapper before registrant content.
		 *
		 * @param Registrant $registrant the object of the current registrant.
		 */
		public function registrant_before_content_wrapper_callback( $registrant ) {
			Template::render( 'registrant/before-content', [], true );
		}

		/**
		 * Callback for displaying registrant form opening tag.
		 *
		 * @param Registrant $registrant object of the current registrant.
		 */
		public function registrant_form_opening_callback( $registrant ) {
			$form_args = [
				'form_class' => 'wcr-registrant-form',
				'form_id'    => 'wcr-form-' . $registrant->post_id,
			];

			Template::render( 'registrant/form-open', $form_args, true );
		}

		/**
		 * Callback for displaying registrant form closing tag.
		 *
		 * @param Registrant $registrant object of the current registrant.
		 */
		public function registrant_form_closing_callback( $registrant ) {
			$form_args = [
				'registrant_id' => $registrant->post_id,
			];

			Template::render( 'registrant/form-close', $form_args, true );
		}

		/**
		 * Callback for displaying hold registrant form opening tag.
		 *
		 * @param Registrant                $registrant object of the current registrant.
		 * @param Payment_Method|bool|mixed $payment_class object of the selected payment method.
		 * @param string                    $reg_status status of the current registrant.
		 */
		public function registrant_hold_opening_callback( $registrant, $payment_class, $reg_status ) {
			$custom_checkout_class = $payment_class->custom_checkout ? "custom-checkout-{$payment_class->id}" : 'normal-checkout';
			$hold_args             = [
				'form_class' => "wcr-{$reg_status}-registrant-form wcr-{$custom_checkout_class}",
				'form_id'    => "wcr-{$reg_status}-form-{$payment_class->id}-{$registrant->post_id}",
			];

			Template::render( 'registrant/form-open', $hold_args, true );
		}

		/**
		 * Callback for displaying registrant invoice.
		 *
		 * @param Registrant                $registrant object of the current registrant.
		 * @param Payment_Method|bool|mixed $payment_class object of the selected payment method.
		 * @param string                    $reg_status status of the current registrant.
		 */
		public function registrant_invoice_callback( $registrant, $payment_class, $reg_status ) {

			// Instance event.
			$event = $registrant->get_event_object();

			// Prepare the args.
			$invoice_args = [
				'event_logo_url'  => $event->get_logo_url(),
				'event_name'      => $event->post_title,
				'pricing_name'    => $registrant->get_pricing_name(),
				'invoice_details' => [
					[
						'field' => __( 'Amount', 'wacara' ),
						'value' => number_format_i18n( $registrant->get_pricing_price_in_cent() / 100, 2 ),
					],
				],
			];

			// Maybe add unique number.
			if ( $registrant->get_pricing_unique_number() ) {
				$invoice_args['invoice_details'][] = [
					'field' => __( 'Unique number', 'wacara' ),
					'value' => number_format_i18n( $registrant->get_pricing_unique_number() / 100, 2 ),
				];
			}

			// Calculate total.
			$invoice_args['invoice_details'][] = [
				'field' => __( 'Total', 'wacara' ),
				'value' => $registrant->get_total_pricing_in_html(),
			];

			Template::render( 'registrant/invoice', $invoice_args, true );
		}

		/**
		 * Callback for displaying opening field for hold registrant.
		 *
		 * @param Registrant                $registrant object of the current registrant.
		 * @param Payment_Method|bool|mixed $payment_class object of the selected payment method.
		 */
		public function registrant_hold_opening_field_callback( $registrant, $payment_class ) {
			?>
			<div class="wcr-field-payment wcr-form-field-wrapper">
			<label><?php esc_html_e( 'Payment', 'wacara' ); ?></label>
			<?php
		}

		/**
		 * Callback for displaying closing field for hold registrant.
		 *
		 * @param Registrant                $registrant object of the current registrant..
		 * @param Payment_Method|bool|mixed $payment_class object of the selected payment method.
		 */
		public function registrant_hold_closing_field_callback( $registrant, $payment_class ) {
			?>
			</div>
			<?php
		}

		/**
		 * Callback for displaying hidden fields for hold registrant.
		 *
		 * @param Registrant                $registrant object of the current registrant.
		 * @param Payment_Method|bool|mixed $payment_class object of the selected payment method.
		 * @param string                    $reg_status status of the current registrant.
		 */
		public function registrant_hold_hidden_field_callback( $registrant, $payment_class, $reg_status ) {

			// Prepare used fields.
			$used_fields = [ 'name', 'email' ];

			// Render hidden fields.
			foreach ( $used_fields as $field ) {
				$maybe_value = $registrant->get_data( $field );
				echo apply_filters( 'wacara_input_field', $field, 'hidden', '', $maybe_value ); // phpcs:ignore
			}
		}

		/**
		 * Callback for displaying hold registrant form submit.
		 *
		 * @param Registrant                $registrant object of the current registrant.
		 * @param Payment_Method|bool|mixed $payment_class id of the selected payment.
		 * @param string                    $reg_status status of the current registrant.
		 */
		public function registrant_hold_submit_button_callback( $registrant, $payment_class, $reg_status ) {

			// Prepare default variable.
			$submit_label = __( 'Checkout', 'wacara' );

			/**
			 * Wacara registrant submit form button filter hook.
			 *
			 * @param string $submit_label current submit label.
			 * @param Registrant $registrant object of the current registrant.
			 * @param Payment_Method|bool|mixed $payment_class object of the selected payment method.
			 * @param string $reg_status status of the current registrant.
			 */
			$submit_label = apply_filters( 'wacara_filter_form_registrant_submit_label', $submit_label, $registrant, $payment_class, $reg_status );

			$submit_args = [
				'submit_label' => $submit_label,
			];

			Template::render( 'registrant/form-submit', $submit_args, true );
		}

		/**
		 * Callback for displaying hold registrant form closing tag.
		 *
		 * @param Registrant                $registrant object of the current registrant.
		 * @param Payment_Method|bool|mixed $payment_class object of the selected payment method.
		 * @param string                    $reg_status status of the current registrant.
		 */
		public function registrant_hold_closing_callback( $registrant, $payment_class, $reg_status ) {
			$form_args = [
				'registrant_id' => $registrant->post_id,
			];

			Template::render( 'registrant/form-close', $form_args, true );
		}

		/**
		 * Callback for displaying extra wrapper closing tag after registrant content.
		 *
		 * @param Registrant $registrant the object of the current registrant.
		 */
		public function registrant_after_content_wrapper_callback( $registrant ) {
			Template::render( 'registrant/after-content', [], true );
		}

		/**
		 * Callback for displaying registrant section closing tag.
		 *
		 * @param Registrant $registrant the object of the current registrant.
		 */
		public function registrant_section_closing_callback( $registrant ) {
			Template::render( 'global/section-close', [], true );
		}

		/**
		 * Callback for rendering header in global email template.
		 *
		 * @param Registrant $registrant object of the current registrant.
		 */
		public function header_global_email_template( $registrant ) {

			// Instance event.
			$event = $registrant->get_event_object();
			?>
			<table role="presentation" class="main" style="margin-bottom: 20px">
				<tr>
					<td class="wrapper">
						<table role="presentation" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td style="text-align: center;">
									<?php /* translators: %s : event name */ ?>
									<img src="<?php echo esc_attr( $event->get_logo_url() ); ?>" alt="<?php echo esc_html( sprintf( __( '%s logo', 'wacara' ), $event->post_title ) ); ?>" style="max-width: 150px; max-height: 50px;">
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<?php
		}
	}

	UI::init();
}
