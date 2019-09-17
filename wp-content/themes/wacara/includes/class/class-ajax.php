<?php
/**
 * Use this class to add custome ajax handler
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Skeleton\Ajax' ) ) {
	/**
	 * Class Ajax
	 *
	 * @package Skeleton
	 */
	class Ajax {
		/**
		 * Instance variable
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Singleton function
		 *
		 * @return Ajax|null
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Ajax constructor.
		 */
		private function __construct() {
			// Register ajax endpoint for registering participant.
			add_action( 'wp_ajax_nopriv_register', [ $this, 'register_callback' ] );
			add_action( 'wp_ajax_register', [ $this, 'register_callback' ] );
		}

		/**
		 * Callback for registering participant.
		 */
		public function register_callback() {
			$result     = new Result();
			$event_id   = Helper::post( 'event_id' );
			$pricing_id = Helper::post( 'pricing_id' );
			if ( $event_id && $pricing_id ) {
				// Validate the event before using it for registration.
				$validate_event = Helper::is_event_valid( $event_id );
				if ( $validate_event->success ) {
					// create participant.
					$new_participant = new Participant(
						false,
						[
							'event_id'   => $event_id,
							'pricing_id' => $pricing_id,
						]
					);
					if ( $new_participant->success ) {
						$result->success  = true;
						$result->callback = $new_participant->participant_url;
					} else {
						$result->message = $new_participant->message;
					}
				} else {
					$result->message = $validate_event->message;
				}
			} else {
				$result->message = __( 'Please provide a valid event id', 'wacara' );
			}
			wp_send_json( $result );
		}
	}
}

Ajax::init();
