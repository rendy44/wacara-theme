<?php
/**
 * Use this class to manage participants.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

use QRcode;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Skeleton\Participant' ) ) {
	/**
	 * Class Participant
	 *
	 * @package Skeleton
	 */
	class Participant extends Result {
		/**
		 * Participant id.
		 *
		 * @var bool
		 */
		public $participant_id = false;

		/**
		 * Participant key.
		 *
		 * @var string
		 */
		public $participant_key = '';

		/**
		 * Participant booking code that will be used for checking in.
		 *
		 * @var string
		 */
		public $booking_code = '';

		/**
		 * Participant permalink.
		 *
		 * @var string
		 */
		public $participant_url = '';

		/**
		 * Participant data.
		 *
		 * @var array
		 */
		public $participant_data = [];

		/**
		 * Participant constructor.
		 *
		 * @param bool  $participant_id leave it empty to create a new participant,
		 *                              and assign with participant id to fetch the participant's detail.
		 * @param array $args           arguments to create a new participant.
		 */
		public function __construct( $participant_id = false, $args = [] ) {
			parent::__construct();

			// Create a new participant.
			if ( ! $participant_id ) {

				// Prepare default args.
				$default_args = [
					'event_id'   => false,
					'pricing_id' => false,
				];

				// Parse the arguments.
				$args = wp_parse_args( $args, $default_args );

				// Validate inputs.
				if ( $args['event_id'] && $args['pricing_id'] ) {

					// Save event id to variable.
					$event_id = $args['event_id'];

					// Generate unique key.
					$participant_key = wp_generate_password( 12, false );

					/**
					 * Perform the filter to modify participant key.
					 *
					 * @param string $participant_key participant random key.
					 */
					apply_filters( 'wacara_filter_participant_key', $participant_key );

					/**
					 * Perform action before creating participant
					 *
					 * @param array $args setting for creating new post.
					 */
					do_action( 'wacara_before_creating_participant', $args );

					// Proceed creating participant.
					$new_participant = wp_insert_post(
						[
							'post_type'   => 'participant',
							'post_title'  => strtoupper( $participant_key ),
							'post_name'   => sanitize_title( $participant_key ),
							'post_status' => 'publish',
						]
					);

					/**
					 * Perform action after creating participant
					 *
					 * @param array        $args            setting for creating new post.
					 * @param int|WP_Error $new_participant result of newly created participant.
					 */
					do_action( 'wacara_after_creating_participant', $args, $new_participant );

					// Validate after creating participant.
					if ( is_wp_error( $new_participant ) ) {

						// Update result.
						$this->message = $new_participant->get_error_messages();
					} else {

						// Create participant booking code.
						$event_and_id_length = strlen( $event_id . $new_participant );
						$unique_length       = 8 - $event_and_id_length;
						$booking_code        = strtoupper( $event_id . wp_generate_password( $unique_length, false ) . $new_participant );

						/**
						 * Perform filter to modify participant publishable key.
						 *
						 * @param string $booking_code    the original publishable key.
						 * @param string $event_id        event id of the participant.
						 * @param int    $new_participant id number of newly created participant.
						 */
						$booking_code = apply_filters( 'wacara_filter_participant_booking_code', $booking_code, $event_id, $new_participant );

						// Add publishable key to participant meta.
						$args['booking_code'] = $booking_code;

						// Update class object.
						$this->success          = true;
						$this->participant_id   = $new_participant;
						$this->participant_key  = $participant_key;
						$this->booking_code     = $booking_code;
						$this->participant_url  = get_permalink( $new_participant );
						$this->participant_data = $args;

						// Create qrcode for participant.
						$this->save_qrcode_to_participant();

						// Update participant meta after successfully being created.
						$this->save_meta( $args );
					}
				} else {

					// Update result.
					$this->message = __( 'Please use valid input', 'wacara' );
				}
			} else {
				// TODO: Fetching participant.
			}
		}

		/**
		 * Update participant meta data.
		 *
		 * @param array $meta_data participant meta data.
		 */
		private function save_meta( array $meta_data ) {
			$participant_id = $this->participant_id;
			Helper::save_post_meta( $participant_id, $meta_data );
		}

		/**
		 * Save qrcode image locally.
		 */
		private function generate_qrcode_locally() {
			$qrcode_name = $this->participant_key;
			$file_name   = TEMP_PATH . "/assets/qrcode/{$qrcode_name}.png";
			QRcode::png( $qrcode_name, $file_name, QR_ECLEVEL_H, 5 );
		}

		/**
		 * Save qrcode information into participant.
		 */
		public function save_qrcode_to_participant() {
			// Generate qrcode locally.
			$this->generate_qrcode_locally();

			// Save qrcode data.
			$qrcode_name = $this->participant_key . '.png';
			$qrcode_uri  = TEMP_URI . '/assets/qrcode/' . $qrcode_name;

			// Save qrcode into participant.
			$this->save_meta(
				[
					'qrcode_name' => $qrcode_name,
					'qrcode_url'  => $qrcode_uri,
				]
			);
		}

		/**
		 * Find participant by their booking code.
		 *
		 * @param string $booking_code booking code.
		 *
		 * @return Result
		 */
		public static function find_participant_by_booking_code( $booking_code ) {
			return Helper::get_post_id_by_meta_key( 'booking_code', $booking_code, 'participant' );
		}
	}
}
