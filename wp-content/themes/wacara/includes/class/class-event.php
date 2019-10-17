<?php
/**
 * Class to manage the event.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Skeleton\Event' ) ) {
	/**
	 * Class Event
	 *
	 * @package Skeleton
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
		 * @param string $event_id   event id.
		 * @param bool   $get_detail whether get detail or not.
		 */
		public function __construct( $event_id, $get_detail = false ) {
			parent::__construct( $event_id, 'event' );

			// Validate the event.
			if ( $this->success ) {
				// Parse more detail.
				if ( $get_detail ) {
					$is_single_day              = parent::get_meta( 'single_day' );
					$this->date_start_timestamp = parent::get_meta( 'date_start' );
					$this->date_start           = Helper::convert_date( $this->date_start_timestamp, true );
					$this->is_single_day        = 'on' === $is_single_day ? true : false;

					$this->set_date_end();
				}
			}
		}

		/**
		 * Get event date end.
		 */
		private function set_date_end() {
			$maybe_multi_date_end_timestamp = parent::get_meta( 'date_end' );
			$this->maybe_time_end           = parent::get_meta( 'time_end' );
			$this->date_end                 = $this->is_single_day ? Helper::convert_date( $this->date_start_timestamp ) . ' ' . $this->maybe_time_end : Helper::convert_date( $maybe_multi_date_end_timestamp );
			$this->date_end_timestamp       = $this->is_single_day ? strtotime( $this->date_end ) : $maybe_multi_date_end_timestamp;
		}

		/**
		 * Get event logo url.
		 *
		 * @return string
		 */
		public function get_logo_url() {
			$main_logo = parent::get_meta( 'main_logo_id' );

			return $main_logo ? wp_get_attachment_image_url( $main_logo, 'medium' ) : Helper::get_site_logo_url();
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
		 * Check whether participant is allowed to register to this event or not.
		 *
		 * @return bool
		 */
		public function is_event_allows_register() {
			$result         = false;
			$allow_register = parent::get_meta( 'allow_register' );
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
		 * Check whether the event already exceeded the registration.
		 */
		private function check_exceeding_registration() {
			$is_limited = $this->is_registration_limited();

			// Check whether the event is limiting registration or not.
			if ( $is_limited ) {

				// Save limitation info into variables.
				$maybe_limited_by_number                  = (int) $this->get_meta( 'max_participant' );
				$current_number_of_registered_participant = (int) $this->get_meta( 'number_of_participant' );
				$maybe_limited_by_date                    = $this->get_meta( 'max_date' );
				$current_timestamp                        = current_time( 'timestamp' );

				// Check maybe limited by number of participant.
				if ( $maybe_limited_by_number ) {
					if ( $current_number_of_registered_participant >= $maybe_limited_by_number ) {

						// Update the result.
						$this->success = false;
						$this->message = __( 'This event already exceeded the maximum number of participant', 'wacara' );
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
					if ( $this->date_start ) {

						// Is date end or time end assigned.
						$event_end = $this->is_single_day ? $this->maybe_time_end : $this->date_end;
						if ( $event_end ) {

							// Is location assigned.
							$location = parent::get_meta( 'location' );
							if ( $location ) {

								// Validate location.
								$validate_location = Helper::is_location_valid( $location );

								if ( $validate_location->success ) {
									$this->success = true;

									// Maybe check limitation.
									$this->check_exceeding_registration();

								} else {
									$this->success = false;
									$this->message = $validate_location->message;
								}
							} else {
								$this->success = false;
								$this->message = __( 'This event is not completed yet, the event location has not been assigned yet', 'wacara' );
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
			$current_number_of_registered_participant = (int) $this->get_meta( 'number_of_participant' );
			$new_number_of_registered_participant     = $current_number_of_registered_participant + 1;
			$this->save_meta( [ 'number_of_participant' => $new_number_of_registered_participant ] );
		}

		/**
		 * Generate all participants.
		 */
		public function get_all_participants() {
			$key        = TEMP_PREFIX;
			$query_args = [
				'post_type'      => 'participant',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'orderby'        => 'date',
				'order'          => 'desc',
				'meta_query'     => [ // phpcs:ignore
					[
						'key'   => $key . 'reg_status',
						'value' => 'done',
					],
					[
						'key'   => $key . 'event_id',
						'value' => $this->post_id,
					],
				],
			];

			$query = new WP_Query( $query_args );
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$post_id       = get_the_ID();
					$this->items[] = Helper::get_post_meta(
						[
							'name',
							'email',
							'company',
							'position',
							'phone',
							'id_number',
						],
						$post_id
					);
				}
				$this->success = true;
			} else {
				$this->success = false;
				$this->message = __( 'No participant found', 'wacara' );
			}
			wp_reset_postdata();
		}
	}
}
