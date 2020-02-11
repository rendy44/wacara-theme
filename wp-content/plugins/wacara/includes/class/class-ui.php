<?php
/**
 * Use this class to define default layout, such as header and footer
 *
 * @author  Rendy
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
			add_action( 'wacara_before_event_section', [ $this, 'event_section_opening_callback' ], 10, 5 );
			add_action( 'wacara_before_event_section', [ $this, 'maybe_event_section_title_callback' ], 20, 5 );
			add_action( 'wacara_after_event_section', [ $this, 'event_section_closing_callback' ], 50, 5 );

			// Render the about section.
			add_action( 'wacara_event_about_section', [ $this, 'event_about_section_callback' ], 10, 1 );

			// Render the speakers section.
			add_action( 'wacara_event_speakers_section', [ $this, 'event_speakers_section_callback' ], 10, 1 );

			// Render the venue section.
			// add_action( 'wacara_venue_section', [ $this, 'event_venue_section_callback' ], 10, 1 );

			// Render the gallery section.
			add_action( 'wacara_event_gallery_section', [ $this, 'event_gallery_section_callback' ], 10, 1 );

			// Render the sponsors section.
			// add_action( 'wacara_sponsors_section', [ $this, 'event_sponsors_section_callback' ], 10, 1 );

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
			add_action( 'wacara_before_registrant_hold_content', [ $this, 'registrant_hold_opening_field_callback' ], 20, 3 );
			add_action( 'wacara_after_registrant_hold_content', [ $this, 'registrant_hold_closing_field_callback' ], 30, 3 );
			add_action( 'wacara_after_registrant_hold_content', [ $this, 'registrant_hold_submit_button_callback' ], 40, 3 );
			add_action( 'wacara_after_registrant_hold_content', [ $this, 'registrant_hold_hidden_field_callback' ], 50, 3 );
			add_action( 'wacara_after_registrant_hold_content', [ $this, 'registrant_hold_closing_callback' ], 60, 3 );
			add_action( 'wacara_before_registrant_custom_content', [ $this, 'registrant_hold_opening_callback' ], 10, 3 );
			add_action( 'wacara_after_registrant_custom_content', [ $this, 'registrant_hold_submit_button_callback' ], 40, 3 );
			add_action( 'wacara_after_registrant_custom_content', [ $this, 'registrant_hold_closing_callback' ], 50, 3 );
			add_action( 'wacara_after_registrant_content', [ $this, 'registrant_after_content_wrapper_callback' ], 40, 1 );
			add_action( 'wacara_after_registrant_content', [ $this, 'registrant_section_closing_callback' ], 50, 1 );
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
		 * @param Event $event the object of the current event.
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
		 * @param Event $event the object of the current event.
		 */
		public function event_cta_content_callback( $event ) {
			$cta_args = [
				'alert_title'   => __( 'Come Join Us', 'wacara' ),
				'alert_content' => __( 'Save your seat at the lowest price', 'wacara' ),
				'alert_button'  => __( 'Join Now', 'wacara' ),
			];

			Template::render( 'event/call-to-action', $cta_args, true );
		}

		/**
		 * Callback for displaying call to action closing tag.
		 *
		 * @param Event $event the object of the current event.
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
		 * @param Event  $event the object of the current event.
		 * @param string $header_template the id of selected header template of the current event.
		 */
		public function event_masthead_opening_callback( $event, $header_template ) {
			$masthead_args = [
				'masthead_class' => 'wcr-event-header',
			];

			Template::render( 'global/masthead-open', $masthead_args, true );
		}

		/**
		 * Callback for displaying masthead section.
		 *
		 * @param Event  $event the object of the current event.
		 * @param string $header_template the id of selected header template of the current event.
		 */
		public function event_masthead_content_callback( $event, $header_template ) {
			$date_start            = Helper::get_post_meta( 'date_start', $event->post_id );
			$location              = Helper::get_post_meta( 'location', $event->post_id );
			$location_country_code = Helper::get_post_meta( 'country', $location );
			$location_province     = Helper::get_post_meta( 'province', $location );
			$masthead_args         = [
				'title'   => $event->post_title,
				'excerpt' => Helper::convert_date( $date_start, false, true ) . ' - ' . $location_province . ', ' . $location_country_code,
			];

			Template::render( 'event/masthead', $masthead_args, true );
		}

		/**
		 * Callback for displaying masthead countdown.
		 *
		 * @param Event  $event the object of the current event.
		 * @param string $header_template the id of selected header template of the current event.
		 */
		public function event_masthead_countdown_callback( $event, $header_template ) {
			$enable_countdown = Helper::get_post_meta( 'countdown_content', $header_template );
			if ( 'on' === $enable_countdown ) {
				$date_start    = Helper::get_post_meta( 'date_start', $event->post_id );
				$masthead_args = [
					'date_start' => date( 'M j, Y H:i:s', $date_start ),
				];

				Template::render( 'event/masthead-countdown', $masthead_args, true );
			}
		}

		/**
		 * Callback for displaying masthead closing tag.
		 *
		 * @param Event  $event the object of the current event.
		 * @param string $header_template the id of selected header template of the current event.
		 */
		public function event_masthead_closing_callback( $event, $header_template ) {
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
		 * @param string $section the name of the selected section.
		 * @param Event  $event the object of the current event.
		 * @param string $section_class the css class of the selected section.
		 * @param string $section_title the title of the selected section.
		 * @param string $section_subtitle the subtitle of the selected section.
		 */
		public function event_section_opening_callback( $section, $event, $section_class, $section_title, $section_subtitle ) {
			$section_args = [
				'section_class' => $section_class,
				'section'       => $section,
			];

			Template::render( 'global/section-open', $section_args, true );
		}

		/**
		 * Callback for displaying section title.
		 *
		 * @param string $section the name of the selected section.
		 * @param Event  $event the object of the current event.
		 * @param string $section_class the css class of the selected section.
		 * @param string $section_title the title of the selected section.
		 * @param string $section_subtitle the subtitle of the selected section.
		 */
		public function maybe_event_section_title_callback( $section, $event, $section_class, $section_title, $section_subtitle ) {
			if ( $section_title || $section_subtitle ) {
				$section_args = [
					'section_title'    => $section_title,
					'section_subtitle' => $section_subtitle,
				];

				Template::render( 'global/section-title', $section_args, true );
			}
		}

		/**
		 * Callback for displaying section closing tag.
		 *
		 * @param string $section the name of the selected section.
		 * @param Event  $event the object of the current event.
		 * @param string $section_class the css class of the selected section.
		 * @param string $section_title the title of the selected section.
		 * @param string $section_subtitle the subtitle of the selected section.
		 */
		public function event_section_closing_callback( $section, $event, $section_class, $section_title, $section_subtitle ) {
			Template::render( 'global/section-close', [], true );
		}

		/**
		 * Callback for displaying about section.
		 *
		 * @param Event $event the object of current event.
		 */
		public function event_about_section_callback( $event ) {
			$location   = Helper::get_post_meta( 'location', $event->post_id );
			$about_args = [
				'description' => Helper::get_post_meta( 'description', $event->post_id ),
				'location'    => Helper::get_location_paragraph( $location ),
				'time'        => $event->get_event_date_time_paragraph(),
			];

			Template::render( 'event/about', $about_args, true );
		}

		/**
		 * Callback for displaying speakers section.
		 *
		 * @param Event $event the object of current event.
		 */
		public function event_speakers_section_callback( $event ) {
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
					'speakers' => $speakers_arr,
				];

				Template::render( 'event/speakers', $speakers_args, true );
			}
		}

		/**
		 * Callback for displaying venue section.
		 *
		 * @param Event $event the object of current event.
		 */
		public function event_venue_section_callback( $event ) {
			$location   = Helper::get_post_meta( 'location', $event->post_id );
			$venue_args = [
				'sliders'              => Helper::get_post_meta( 'photo', $location ),
				'location_name'        => Helper::get_post_meta( 'name', $location ),
				'location_description' => Helper::get_post_meta( 'description', $location ),
			];

			Template::render( 'event/venue', $venue_args, true );
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

				Template::render( 'event/schedule', $schedule_args, true );
			}
		}

		/**
		 * Callback for displaying pricing section.
		 *
		 * @param Event $event the object of current event.
		 */
		public function event_pricing_section_callback( $event ) {
			$allow_registration = Helper::get_post_meta( 'allow_register', $event->post_id );

			// Set default template name.
			$default_template = 'event/directly';

			// Prepare the args.
			$pricing_args = [
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
			$event_id    = $registrant->get_event_info();
			$event_title = get_the_title( $event_id );

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
				$maybe_value = $registrant->registrant_data[ $field ];
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
			 * @param Payment_Method|bool|mixed     $payment_class object of the selected payment method.
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
	}

	UI::init();
}
