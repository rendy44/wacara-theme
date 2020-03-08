<?php
/**
 * Class to manage the event.
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Event' ) ) {

	/**
	 * Class Event
	 *
	 * @package Wacara
	 */
	class Event extends Post {

		/**
		 * Whether the event is single day.
		 *
		 * @var bool
		 */
		private $is_single_day = false;

		/**
		 * Event date start in timestamp.
		 *
		 * @var int
		 */
		private $date_start_timestamp = 0;

		/**
		 * Event date start in readable format.
		 *
		 * @var string
		 */
		private $date_start = '';

		/**
		 * Event date end in timestamp.
		 *
		 * @var int
		 */
		private $date_end_timestamp = 0;

		/**
		 * Event date end if readable format.
		 *
		 * @var string
		 */
		private $date_end = '';

		/**
		 * Maybe time end.
		 *
		 * @var string
		 */
		private $maybe_time_end = '';

		/**
		 * Event constructor.
		 *
		 * @param string $event_id event id.
		 * @param bool   $get_detail whether get detail or not.
		 */
		public function __construct( $event_id, $get_detail = false ) {
			parent::__construct( $event_id, 'event' );

			// Validate the event.
			if ( $this->success ) {

				// Parse more detail.
				if ( $get_detail ) {

					// Set object properties.
					$this->set_date_start();
					$this->set_date_end();
				}
			}
		}

		/**
		 * Set event date start.
		 */
		private function set_date_start() {
			$is_single_day              = $this->get_meta( 'single_day' );
			$this->date_start_timestamp = $this->get_meta( 'date_start' );
			$this->date_start           = Helper::convert_date( $this->date_start_timestamp, true );
			$this->is_single_day        = 'on' === $is_single_day ? true : false;
		}

		/**
		 * Set event date end.
		 */
		private function set_date_end() {
			$maybe_multi_date_end_timestamp = $this->get_meta( 'date_end' );
			$this->maybe_time_end           = $this->get_meta( 'time_end' );
			$this->date_end                 = $this->is_single_day ? Helper::convert_date( $this->date_start_timestamp ) . ' ' . $this->maybe_time_end : Helper::convert_date( $maybe_multi_date_end_timestamp );
			$this->date_end_timestamp       = $this->is_single_day ? strtotime( $this->date_end ) : $maybe_multi_date_end_timestamp;
		}

		/**
		 * Get event logo url.
		 *
		 * @return bool|false|string
		 */
		public function get_logo_url() {
			$main_logo = $this->get_meta( 'main_logo_id' );

			return $main_logo ? wp_get_attachment_image_url( $main_logo, 'medium' ) : Helper::get_site_logo_url();
		}

		/**
		 * Get event background image url.
		 *
		 * @param string $size size of the image.
		 *
		 * @return bool|false|string
		 */
		public function get_background_image_url( $size = 'large' ) {
			$backgorund_image_id = $this->get_meta( 'background_image_id' );

			// Validate background image.
			if ( $backgorund_image_id ) {
				$result = wp_get_attachment_image_url( $backgorund_image_id, $size );
			} else {
				$result = $this->get_header_object()->get_default_image_url( $size );
			}

			return $result;
		}

		/**
		 * Get event headline.
		 *
		 * @return array|bool|mixed
		 */
		public function get_headline() {
			return $this->get_meta( 'headline' );
		}

		/**
		 * Get status whether event is held in a single or multiple days.
		 *
		 * @return bool
		 */
		public function is_single_day() {
			return $this->is_single_day;
		}

		/**
		 * Get event date start.
		 *
		 * @param string $date_format date format.
		 *
		 * @return false|string
		 */
		public function get_date_start( $date_format = '' ) {
			return $date_format ? date( $date_format, $this->date_start_timestamp ) : $this->date_start;
		}

		/**
		 * Get event date end.
		 *
		 * @param string $date_format date format.
		 *
		 * @return false|string
		 */
		public function get_date_end( $date_format = '' ) {
			return $date_format ? date( $date_format, $this->date_end_timestamp ) : $this->date_end;
		}

		/**
		 * Get list of menus.
		 *
		 * @return array
		 */
		public function get_nav_menus() {

			// Prepare empty result.
			$result = [];

			// Fetch sections ordering.
			$sections = $this->get_meta( 'section_order' );
			if ( ! empty( $sections ) ) {
				foreach ( $sections as $section ) {
					$maybe_section_title = $this->get_meta( $section . '_title' );

					// Save conditional nav title.
					$result[ $section ] = $maybe_section_title ? $maybe_section_title : ucfirst( $section );
				}
			}

			return $result;
		}

		/**
		 * Get full event time info
		 *
		 * @return string
		 */
		public function get_event_date_time_paragraph() {
			$end = $this->is_single_day ? $this->maybe_time_end : $this->date_end;
			$utc = Helper::get_readable_utc();

			return $this->date_start . ' - ' . $end . ' ' . $utc;
		}

		/**
		 * Check whether the event is past or not.
		 *
		 * @return bool
		 */
		public function is_event_past() {
			$result            = true;
			$current_date_time = current_time( 'timestamp' );
			if ( $this->date_start_timestamp && $current_date_time < $this->date_start_timestamp ) {
				$result = false;
			}

			return $result;
		}

		/**
		 * Check whether registrant is allowed to register to this event or not.
		 *
		 * @return bool
		 */
		public function is_event_allows_register() {
			$result         = false;
			$allow_register = $this->get_meta( 'allow_register' );
			if ( 'on' === $allow_register ) {
				$result = true;
			}

			return $result;
		}

		/**
		 * Check whether now is correct to to do checkin.
		 *
		 * @return bool
		 */
		public function is_in_checkin_period() {
			$result        = false;
			$now_timestamp = current_time( 'timestamp' );
			if ( $now_timestamp < $this->date_end_timestamp ) {
				$result = true;
			}

			return $result;
		}

		/**
		 * Check whether event is limiting registration.
		 *
		 * @return bool
		 */
		public function is_registration_limited() {
			$result = false;
			$limit  = $this->get_meta( 'limit_register' );
			if ( 'on' === $limit ) {
				$result = true;
			}

			return $result;
		}

		/**
		 * Get event location id.
		 *
		 * @return array|bool|mixed
		 */
		public function get_location_id() {
			return $this->get_meta( 'location' );
		}

		/**
		 * Get event location object.
		 *
		 * @return Event_Location
		 */
		public function get_location_object() {
			return new Event_Location( $this->get_location_id() );
		}

		/**
		 * Get event header id.
		 *
		 * @return array|bool|mixed
		 */
		public function get_header_id() {
			return $this->get_meta( 'header' );
		}

		/**
		 * Get event header object.
		 *
		 * @return Event_Header
		 */
		public function get_header_object() {
			return new Event_Header( $this->get_header_id() );
		}

		/**
		 * Check whether the event already exceeded the registration.
		 */
		private function check_exceeding_registration() {
			$is_limited = $this->is_registration_limited();

			// Check whether the event is limiting registration or not.
			if ( $is_limited ) {

				// Save limitation info into variables.
				$maybe_limited_by_number                 = (int) $this->get_meta( 'max_registrant' );
				$current_number_of_registered_registrant = (int) $this->get_meta( 'number_of_registrant' );
				$maybe_limited_by_date                   = $this->get_meta( 'max_date' );
				$current_timestamp                       = current_time( 'timestamp' );

				// Check maybe limited by number of registrant.
				if ( $maybe_limited_by_number ) {
					if ( $current_number_of_registered_registrant >= $maybe_limited_by_number ) {

						// Update the result.
						$this->success = false;
						$this->message = __( 'This event already exceeded the maximum number of registrant', 'wacara' );
					}
				}

				// Check maybe limited by date.
				if ( $maybe_limited_by_date ) {
					if ( $current_timestamp >= $maybe_limited_by_date ) {

						// Update the result.
						$this->success = false;
						$this->message = __( 'This event already past the date of allowed registration', 'wacara' );
					}
				}
			}
		}

		/**
		 * Check whether the event is available to be used for registration.
		 */
		public function validate_event() {

			// Is already past.
			$is_event_past = $this->is_event_past();
			if ( ! $is_event_past ) {

				// Is allowed to register.
				$allow_register = $this->is_event_allows_register();
				if ( $allow_register ) {

					// Is date_start assigned.
					if ( $this->get_date_start() ) {

						// Is date end or time end assigned.
						if ( $this->get_date_end() ) {

							// Instance location.
							$location = $this->get_location_object();

							// Validate the location.
							if ( $location->success ) {

								// Everything seems ok.
								$this->success = true;

								// Maybe check limitation.
								$this->check_exceeding_registration();

							} else {
								$this->success = false;
								$this->message = $location->message;
							}
						} else {
							$this->success = false;
							$this->message = __( 'This event is not completed yet, the event end period has not been assigned yet', 'wacara' );
						}
					} else {
						$this->success = false;
						$this->message = __( 'This event is not completed yet, the starting date has not been assigned yet', 'wacara' );
					}
				} else {
					$this->success = false;
					$this->message = __( 'This event does not allow registration', 'wacara' );
				}
			} else {
				$this->success = false;
				$this->message = __( 'Event already past or the starting date has not been defined yet', 'wacara' );
			}
		}

		/**
		 * Maybe update the limitation.
		 */
		public function maybe_recount_limitation() {
			$current_number_of_registered_registrant = (int) $this->get_meta( 'number_of_registrant' );
			$new_number_of_registered_registrant     = $current_number_of_registered_registrant + 1;
			$this->save_meta( [ 'number_of_registrant' => $new_number_of_registered_registrant ] );
		}

		/**
		 * Generate all registrants.
		 */
		public function get_all_done_registrants() {
			$key = WACARA_PREFIX;
			$this->get_all_registrants(
				[
					'meta_query' => [
						[
							'key'   => $key . 'reg_status',
							'value' => 'done',
						],
						[
							'key'   => $key . 'event_id',
							'value' => $this->post_id,
						],
					],
				]
			);
		}

		/**
		 * Get all registrants by registration status.
		 *
		 * @param int $page the current page.
		 */
		public function get_all_registrants_by_registration_status( $page = 1 ) {
			$key = WACARA_PREFIX;
			$this->get_all_registrants(
				[
					'paged'          => $page,
					'posts_per_page' => 50,
					'meta_key'       => $key . 'reg_status', // phpcs:ignore
					'orderby'        => 'meta_value',
				]
			);
		}

		/**
		 * Generate all registrants.
		 *
		 * @param array $custom_args override args.
		 */
		public function get_all_registrants( $custom_args = [] ) {
			$key          = WACARA_PREFIX;
			$default_args = [
				'post_type'      => 'registrant',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'orderby'        => 'date',
				'order'          => 'desc',
				'fields'         => 'ids',
				'meta_query'     => [ // phpcs:ignore
									  [
										  'key'   => $key . 'event_id',
										  'value' => $this->post_id,
									  ],
				],
			];

			$args = wp_parse_args( $custom_args, $default_args );

			// Prepare the transient_key.
			$serialized_args = maybe_serialize( $custom_args );
			$transient_key   = 'get_all_registrants_' . $this->post_id . '_' . sanitize_title( $serialized_args );

			// Maybe parse from transient.
			$query_all_registrants = get_transient( $transient_key );

			// Parse the transient.
			if ( false === $query_all_registrants ) {

				// Instance a new query.
				$query_all_registrants = new WP_Query( $args );

				// Save the query to transient.
				set_transient( $transient_key, $query_all_registrants, HOUR_IN_SECONDS / 2 );
			}

			// Update the maximum num pages variable.
			$this->max_num_pages = $query_all_registrants->max_num_pages;

			if ( $query_all_registrants->have_posts() ) {
				while ( $query_all_registrants->have_posts() ) {
					$query_all_registrants->the_post();

					// Instance the registrant.
					$this->items[] = new Registrant( get_the_ID() );
				}
				$this->success = true;
			} else {
				$this->success = false;
				$this->message = __( 'No registrant found', 'wacara' );
			}
			wp_reset_postdata();
		}
	}
}
