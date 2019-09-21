<?php
/**
 * Use this class to manage participants.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

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

						// Update class object.
						$this->success          = true;
						$this->participant_id   = $new_participant;
						$this->participant_url  = get_permalink( $new_participant );
						$this->participant_data = $args;

						// Update participant meta after successfully being created.
						Helper::save_post_meta( $new_participant, $args );
					}
				} else {

					// Update result.
					$this->message = __( 'Please use valid input', 'wacara' );
				}
			} else {
				// TODO: Fetching participant.
			}
		}
	}
}
