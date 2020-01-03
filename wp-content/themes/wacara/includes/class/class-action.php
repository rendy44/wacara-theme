<?php
/**
 * Class to manage custom admin actions.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Action' ) ) {

	/**
	 * Class Action
	 *
	 * @package Wacara
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

				// Instance the event.
				$event = new Event( $event_id );

				/* translators: 1: event name */
				$file_name = sprintf( __( '%s Participants', 'wacara' ), $event->post_title );
				header( 'Content-type: application/csv' );
				header( "Content-Disposition: attachment; filename={$file_name}.csv" );
				$fp = fopen( 'php://output', 'w' );

				// Validate the event.
				if ( $event->success ) {
					$event->get_all_done_participants();

					// Validate the participants.
					if ( $event->success ) {

						// Write the header.
						fputcsv(
							$fp,
							[
								__( 'Booking Code', 'wacara' ),
								__( 'Name', 'wacara' ),
								__( 'Email', 'wacara' ),
								__( 'Company', 'wacara' ),
								__( 'Position', 'wacara' ),
								__( 'Phone', 'wacara' ),
								__( 'Id Number', 'wacara' ),
								__( 'Status', 'wacara' ),
							]
						);

						// Start looping.
						foreach ( $event->items as $item ) {
							$used_item = [
								$item->participant_data['booking_code'],
								$item->participant_data['name'],
								$item->participant_data['email'],
								$item->participant_data['company'],
								$item->participant_data['position'],
								$item->participant_data['phone'],
								$item->participant_data['id_number'],
								$item->participant_data['readable_reg_status'],
							];
							fputcsv( $fp, $used_item );
						}
					}
				}
				fclose( $fp ); // phpcs:ignore
			} else {
				wp_die( esc_html__( 'Please try again later', 'wacara' ) );
			}
		}

	}

	Action::init();
}
