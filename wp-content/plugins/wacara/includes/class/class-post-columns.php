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
			$this->modify_registrant_columns();
		}

		/**
		 * Modify registrant columns.
		 */
		private function modify_registrant_columns() {
			Helper::modify_columns(
				'registrant',
				[
					'event'   => __( 'Event', 'wacara' ),
					'pricing' => __( 'Pricing', 'wacara' ),
					'status'  => __( 'Status', 'wacara' ),
					'action'  => __( 'Action', 'wacara' ),
				]
			);

			Helper::modify_columns_content( 'registrant', [ $this, 'modify_registrant_column_content' ] );
		}

		/**
		 * Get list of registrant status.
		 *
		 * @return array
		 */
		private function get_admin_registrant_status_list() {
			$status = [
				'done' => __( 'Success', 'wacara' ),
				'hold' => __( 'Waiting details', 'wacara' ),
			];

			/**
			 * Wacara registrant status list filter hook.
			 *
			 * @param array $status status of the registrant.
			 */
			$status = apply_filters( 'wacara_filter_registrant_status_list', $status );

			return $status;
		}

		/**
		 * Get readable registrant status.
		 *
		 * @param string $status raw status from db.
		 *
		 * @return mixed|string
		 */
		private function maybe_get_readable_status( $status ) {
			$result      = __( 'Uncompleted registrant', 'wacara' );
			$status_list = $this->get_admin_registrant_status_list();

			if ( ! empty( $status_list[ $status ] ) ) {
				$result = $status_list[ $status ];
			}

			return $result;
		}

		/**
		 * Callback for modifying registrant column content.
		 *
		 * @param string $column name of the column.
		 * @param int    $post_id id of the registrant.
		 */
		public function modify_registrant_column_content( $column, $post_id ) {
			$result = '';

			switch ( $column ) {
				case 'event':
					$event_id = Helper::get_post_meta( 'event_id', $post_id );
					if ( $event_id ) {
						$result = "<a href='" . esc_url( get_edit_post_link( $event_id ) ) . "'>" . get_the_title( $event_id ) . '</a>';
					}
					break;
				case 'pricing':
					$pricing_id = Helper::get_post_meta( 'pricing_id', $post_id );
					if ( $pricing_id ) {

						// Fetch pricing object.
						$pricing = new Pricing( $pricing_id );
						$result  = "<a href='" . esc_url( get_edit_post_link( $pricing_id ) ) . "'>" . $pricing->post_title . '</a>';
						$result .= " ({$pricing->get_html_price()})";
					}
					break;
				case 'status':
					// Fetch registrant object.
					$registrant = new Registrant( $post_id );
					$status     = $registrant->get_registration_status();
					$result     = $this->maybe_get_readable_status( $status );
					break;
			}

			echo $result; // phpcs:ignore
		}
	}

	Post_Columns::init();
}
