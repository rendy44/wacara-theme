<?php
/**
 * Class to manage post type.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Skeleton\Post' ) ) {
	/**
	 * Class Post
	 *
	 * @package Skeleton
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
		 * @param string $post_id   post id.
		 * @param string $post_type post type.
		 */
		public function __construct( $post_id, $post_type = 'post' ) {

			// Validate the post.
			if ( get_post_type( $post_id ) === $post_type ) {
				$this->post_id    = $post_id;
				$this->post_url   = get_permalink( $post_id );
				$this->post_title = get_the_title( $post_id );

				// Update the result.
				$this->success = true;
			} else {
				/* translators: 1: post tye */
				$this->message = sprintf( __( 'The given id is not a valid %s', 'wacara' ), $post_type );
			}
		}

		/**
		 * Get event meta data.
		 *
		 * @param string|array $key participant meta key.
		 *
		 * @return array|bool|mixed
		 */
		protected function get_meta( $key ) {
			return Helper::get_post_meta( $key, $this->post_id );
		}

		/**
		 * Update post meta data.
		 *
		 * @param array $meta_data participant meta data.
		 */
		protected function save_meta( array $meta_data ) {
			Helper::save_post_meta( $this->post_id, $meta_data );
		}
	}
}
