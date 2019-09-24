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
		protected $post_id = 0;

		/**
		 * Post permalink.
		 *
		 * @var string
		 */
		protected $post_url = '';

		/**
		 * Post constructor.
		 *
		 * @param string $post_id   post id.
		 * @param string $post_type post type.
		 */
		public function __construct( $post_id, $post_type = 'post' ) {
			parent::__construct();

			// Validate the post.
			if ( get_post_type( $post_id ) === $post_type ) {
				$this->post_id  = $post_id;
				$this->post_url = get_permalink( $post_id );

				// Update the result.
				$this->success = true;
			} else {
				$this->message = __( 'The given id is not a valid id', 'wacara' );
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
