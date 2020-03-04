<?php
/**
 * Class to modify post's columns
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Post_Columns' ) ) {

	/**
	 * Class Post_Columns
	 *
	 * @package Wacara
	 */
	class Post_Columns {

		/**
		 * Instance variable.
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Singleton.
		 *
		 * @return Post_Columns|null
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Post_Columns constructor.
		 */
		private function __construct() {

			// Modify registrant callback.
			add_filter( 'manage_registrant_posts_columns', [ $this, 'modify_registrant_columns_callback' ], 10, 1 );
			add_action( 'manage_registrant_posts_custom_column', [ $this, 'modify_registrant_column_content' ], 10, 2 );
		}

		/**
		 * Callback for modifying registrant columns.
		 *
		 * @param array $default_columns default columns.
		 *
		 * @return array
		 */
		public function modify_registrant_columns_callback( $default_columns ) {

			// Remove date column.
			unset( $default_columns['date'] );

			// Prepare default registrant columns.
			$new_columns = [
				'event'   => __( 'Event', 'wacara' ),
				'pricing' => __( 'Pricing', 'wacara' ),
				'status'  => __( 'Status', 'wacara' ),
			];

			/**
			 * Wacara registrant admin column filter hook.
			 *
			 * @param array $new_columns new registrant columns.
			 */
			$new_columns = apply_filters( 'wacara_filter_registrant_admin_columns', $new_columns );

			// Merge the columns.
			foreach ( $new_columns as $column_key => $column_label ) {
				$default_columns[ $column_key ] = $column_label;
			}

			// Restore date column.
			$default_columns['date'] = __( 'Date' );

			return $default_columns;
		}

		/**
		 * Callback for modifying registrant column content.
		 *
		 * @param string $column name of the column.
		 * @param int    $post_id id of the registrant.
		 */
		public function modify_registrant_column_content( $column, $post_id ) {
			$result     = '';
			$registrant = new Registrant( $post_id );
			$event_id   = Helper::get_post_meta( 'event_id', $registrant->post_id );
			$event      = new Event( $event_id );

			switch ( $column ) {
				case 'event':
					$result = $event->success ? "<a href='" . esc_url( get_edit_post_link( $event_id ) ) . "'>" . get_the_title( $event_id ) . '</a>' : __( 'Invalid event', 'wacara' );
					break;
				case 'pricing':
					$result = $registrant->get_pricing_price_in_html();
					break;
				case 'status':
					$result = $registrant->get_readable_registrant_status();
					break;
				default:
					/**
					 * Wacara registrant admin custom column content.
					 *
					 * @param Registrant $registrant object of the current registrant.
					 * @param Event $event object of the current registrant's event
					 */
					do_action( "wacara_registrant_admin_column_{$column}_content", $registrant, $event );

					break;
			}

			echo $result; // phpcs:ignore
		}
	}

	Post_Columns::init();
}
