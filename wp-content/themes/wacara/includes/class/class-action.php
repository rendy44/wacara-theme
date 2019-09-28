<?php
/**
 * Class to manage custom admin actions.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Skeleton\Action' ) ) {
	/**
	 * Class Action
	 *
	 * @package Skeleton
	 */
	class Action {
		/**
		 * Instance variable
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Singleton function
		 *
		 * @return Action|null
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Action constructor.
		 */
		private function __construct() {
			add_action( 'admin_post_download_csv', [ $this, 'download_csv_callback' ] );
		}

		/**
		 * Callback for downloading csv.
		 */
		public function download_csv_callback() {
			$event_id = Helper::get( 'event_id' );
			if ( $event_id ) {
				/* translators: 1: event name */
				$file_name = sprintf( __( '%s Participants', 'wacara' ), get_the_title( $event_id ) );
				header( 'Content-type: application/csv' );
				header( "Content-Disposition: attachment; filename={$file_name}.csv" );
				$fp = fopen( 'php://output', 'w' );

				// Instance the event.
				$event = new Event( $event_id );

				// Validate the event.
				if ( $event->success ) {
					$event->get_all_participants();

					// Validate the participants.
					if ( $event->success ) {

						// Write the header.
						fputcsv(
							$fp,
							[
								__( 'Name', 'wacara' ),
								__( 'Email', 'wacara' ),
								__( 'Company', 'wacara' ),
								__( 'Position', 'wacara' ),
								__( 'Phone', 'wacara' ),
								__( 'Id Number', 'wacara' ),
							]
						);

						// Start looping.
						foreach ( $event->items as $item ) {
							fputcsv( $fp, $item );
						}
					}
				}
				fclose( $fp ); // phpcs:ignore
			} else {
				wp_die( esc_html__( 'Please try again later', 'wacara' ) );
			}
		}

	}
}

Action::init();
