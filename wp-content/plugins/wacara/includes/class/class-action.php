<?php
/**
 * Class to manage custom admin actions.
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
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
			add_action( 'admin_post_download_csv', array( $this, 'download_csv_callback' ) );
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
				$file_name = sprintf( __( '%s Registrants', 'wacara' ), $event->post_title );
				header( 'Content-type: application/csv' );
				header( "Content-Disposition: attachment; filename={$file_name}.csv" );
				$fp = fopen( 'php://output', 'w' );

				// Validate the event.
				if ( $event->success ) {
					$event->get_all_done_registrants();

					// Validate the registrants.
					if ( $event->success ) {

						// Write the header.
						fputcsv(
							$fp,
							array(
								__( 'Booking Code', 'wacara' ),
								__( 'Name', 'wacara' ),
								__( 'Email', 'wacara' ),
								__( 'Status', 'wacara' ),
								__( 'Registered', 'wacara' ),
							)
						);

						// Start looping.
						foreach ( $event->items as $item ) {
							$item_data = $item->get_data();
							$used_item = array(
								$item_data['booking_code'],
								$item_data['name'],
								$item_data['email'],
								$item->get_readable_registrant_status(),
								$item->get_created_date(),
							);
							fputcsv( $fp, $used_item );
						}
					}
				} else {
					wp_die( esc_html( $event->message ) );
				}
				fclose( $fp ); // phpcs:ignore
			} else {
				wp_die( esc_html__( 'Please try again later', 'wacara' ) );
			}
		}

	}

	Action::init();
}
