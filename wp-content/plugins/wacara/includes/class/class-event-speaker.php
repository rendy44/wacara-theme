<?php
/**
 * Class to manage the speaker.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Event_Speaker' ) ) {

	/**
	 * Class Event_Speaker
	 *
	 * @package Wacara
	 */
	class Event_Speaker extends Post {

		/**
		 * Speaker position variable.
		 *
		 * @var array|bool|mixed
		 */
		private $position;

		/**
		 * Speaker website url variable.
		 *
		 * @var array|bool|mixed
		 */
		private $web_url;

		/**
		 * Speaker facebook url variable.
		 *
		 * @var array|bool|mixed
		 */
		private $facebook_url;

		/**
		 * Speaker instagram url variable.
		 *
		 * @var array|bool|mixed
		 */
		private $instagram_url;

		/**
		 * Speaker youtube url variable.
		 *
		 * @var array|bool|mixed
		 */
		private $youtube_url;

		/**
		 * Speaker twitter url variable.
		 *
		 * @var array|bool|mixed
		 */
		private $twitter_url;

		/**
		 * Speaker linkedin url variable.
		 *
		 * @var array|bool|mixed
		 */
		private $linkedin_url;

		/**
		 * Event_Speaker constructor.
		 *
		 * @param string $speaker_id speaker id.
		 */
		public function __construct( $speaker_id ) {
			parent::__construct( $speaker_id, 'speaker' );

			// Fetch details.
			$this->position      = $this->get_meta( 'position' );
			$this->web_url       = $this->get_meta( 'website' );
			$this->facebook_url  = $this->get_meta( 'facebook' );
			$this->instagram_url = $this->get_meta( 'instagram' );
			$this->youtube_url   = $this->get_meta( 'youtube' );
			$this->twitter_url   = $this->get_meta( 'twitter' );
			$this->linkedin_url  = $this->get_meta( 'linkedin' );
		}

		/**
		 * Get position.
		 *
		 * @return array|bool|mixed
		 */
		public function get_position() {
			return $this->position;
		}

		/**
		 * Get website url.
		 *
		 * @return array|bool|mixed
		 */
		public function get_website_url() {
			return $this->web_url;
		}

		/**
		 * Get facebook url.
		 *
		 * @return array|bool|mixed
		 */
		public function get_facebook_url() {
			return $this->facebook_url;
		}

		/**
		 * Get instagram url.
		 *
		 * @return array|bool|mixed
		 */
		public function get_instagram_url() {
			return $this->instagram_url;
		}

		/**
		 * Get youtube url.
		 *
		 * @return array|bool|mixed
		 */
		public function get_youtube_url() {
			return $this->youtube_url;
		}

		/**
		 * Get twitter url.
		 *
		 * @return array|bool|mixed
		 */
		public function get_twitter_url() {
			return $this->twitter_url;
		}

		/**
		 * Get linkedin url.
		 *
		 * @return array|bool|mixed
		 */
		public function get_linkedin_url() {
			return $this->linkedin_url;
		}
	}
}
