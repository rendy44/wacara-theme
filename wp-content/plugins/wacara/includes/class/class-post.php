<?php
/**
 * Class to manage post type.
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Post' ) ) {
	/**
	 * Class Post
	 *
	 * @package Wacara
	 */
	class Post extends Result {
		/**
		 * Post id.
		 *
		 * @var int
		 */
		public $post_id = 0;

		/**
		 * Post permalink.
		 *
		 * @var string
		 */
		public $post_url = '';

		/**
		 * Post title.
		 *
		 * @var string
		 */
		public $post_title = '';

		/**
		 * Post constructor.
		 *
		 * @param string $post_id post id.
		 * @param string $post_type post type.
		 */
		public function __construct( $post_id, $post_type = 'post' ) {

			// Get post type from db.
			$db_post_type = get_post_type( $post_id );

			// Validate the post.
			if ( $db_post_type === $post_type ) {
				$this->post_id    = $post_id;
				$this->post_url   = get_permalink( $post_id );
				$this->post_title = get_the_title( $post_id );

				// Update the result.
				$this->success = true;
			} else {
				/* translators: 1: post tye */
				$this->message = sprintf( __( 'The given id is not a valid %1$s', 'wacara' ), $post_type );
			}
		}

		/**
		 * Get registrant created datetime.
		 *
		 * @param string $format datetime format.
		 *
		 * @return false|string
		 */
		public function get_created_date( $format = '' ) {
			$date_format = $format ? $format : Helper::get_date_time_format();

			return get_the_date( $date_format, $this->post_id );
		}

		/**
		 * Get event meta data.
		 *
		 * @param string|array $key registrant meta key.
		 * @param bool         $single whether get value as single or array.
		 *
		 * @return array|bool|mixed
		 */
		protected function get_meta( $key, $single = true ) {
			return Helper::get_post_meta( $key, $this->post_id, $single );
		}

		/**
		 * Update post meta data.
		 *
		 * @param array $meta_data registrant meta data.
		 */
		protected function save_meta( array $meta_data ) {
			Helper::save_post_meta( $this->post_id, $meta_data );
		}

		/**
		 * Add a new single post meta.
		 *
		 * @param string       $meta_key meta key.
		 * @param string|array $meta_value meta value.
		 */
		protected function add_meta( $meta_key, $meta_value ) {
			Helper::add_post_meta( $this->post_id, $meta_key, $meta_value );
		}
	}
}
