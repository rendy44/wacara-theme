<?php
/**
 * Class to manage the header.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Event_Header' ) ) {

	/**
	 * Class Event_Header
	 *
	 * @package Wacara
	 */
	class Event_Header extends Post {

		/**
		 * Content alignment variable.
		 *
		 * @var array|bool|mixed
		 */
		private $content_alignment;

		/**
		 * Default image id variable.
		 *
		 * @var array|bool|mixed
		 */
		private $default_image_id;

		/**
		 * Darken status variable.
		 *
		 * @var array|bool|mixed
		 */
		private $is_darken;

		/**
		 * Countdown availability status.
		 *
		 * @var array|bool|mixed
		 */
		private $is_countdown_content;

		/**
		 * Event_Header constructor.
		 *
		 * @param string $header_id header id.
		 */
		public function __construct( $header_id ) {
			parent::__construct( $header_id, 'header' );

			// Fetch details.
			$this->content_alignment    = $this->get_meta( 'content_alignment' );
			$this->default_image_id     = $this->get_meta( 'default_image_id' );
			$this->is_darken            = $this->get_meta( 'darken' );
			$this->is_countdown_content = $this->get_meta( 'countdown_content' );
		}

		/**
		 * Get header content alignment.
		 *
		 * @return array|bool|mixed
		 */
		public function get_content_alignment() {
			return $this->content_alignment;
		}

		/**
		 * Get header default image url.
		 *
		 * @param string $size size of the default image.
		 *
		 * @return bool|false|string
		 */
		public function get_default_image_url( $size = 'large' ) {
			return $this->default_image_id ? wp_get_attachment_image_url( $this->default_image_id, $size ) : false;
		}

		/**
		 * Get header darken status.
		 *
		 * @return bool
		 */
		public function is_darken() {
			return 'on' === $this->is_darken;
		}

		/**
		 * Get header countdown status.
		 *
		 * @return bool
		 */
		public function is_countdown_content() {
			return 'on' === $this->is_countdown_content;
		}
	}
}
