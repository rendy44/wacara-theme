<?php
/**
 * Class to manage the location.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Event_Location' ) ) {

	/**
	 * Class Event_Location
	 *
	 * @package Wacara
	 */
	class Event_Location extends Post {

		/**
		 * Event_Location constructor.
		 *
		 * @param string $location_id location id.
		 */
		public function __construct( $location_id ) {
			parent::__construct( $location_id, 'location' );

			// Self validate the location.
			$this->validate();
		}

		/**
		 * Validate the location.
		 */
		private function validate() {

			// Is name assigned.
			if ( ! $this->get_location_name() ) {
				$this->success = false;
				$this->message = __( 'This event is not completed yet, it uses invalid location which does not have valid name', 'wacara' );

				return;
			}

			// Is country assigned.
			if ( ! $this->get_location_country() ) {
				$this->success = false;
				$this->message = __( 'This event is not completed yet, it uses invalid location which does not have valid country', 'wacara' );

				return;
			}

			// Is province assigned.
			if ( ! $this->get_location_province() ) {
				$this->message = __( 'This event is not completed yet, it uses invalid location which does not have valid province', 'wacara' );
				$this->success = false;

				return;
			}

			// Is city assigned.
			if ( ! $this->get_location_city() ) {
				$this->success = false;
				$this->message = __( 'This event is not completed yet, it uses invalid location which does not have valid city', 'wacara' );

				return;
			}

			// Is address assigned.
			if ( ! $this->get_location_address() ) {
				$this->success = false;
				$this->message = __( 'This event is not completed yet, it uses invalid location which does not have valid address', 'wacara' );

				return;
			}

			// Is postal_code assigned.
			if ( ! $this->get_location_postal() ) {
				$this->success = false;
				$this->message = __( 'This event is not completed yet, it uses invalid location which does not have valid postal code', 'wacara' );

				return;
			}

			// Is photo assigned.
			if ( ! $this->get_location_photo() ) {
				$this->success = false;
				$this->message = __( 'This event is not completed yet, it uses invalid location which does not have valid photo', 'wacara' );

				return;
			}

			// Is description assigned.
			if ( ! $this->get_location_description() ) {
				$this->success = false;
				$this->message = __( 'This event is not completed yet, it uses invalid location which does not have valid description', 'wacara' );

				return;
			}

			// Everything seems ok.
			$this->success = true;
		}

		/**
		 * Get location name.
		 *
		 * @return array|bool|mixed
		 */
		public function get_location_name() {
			return $this->get_meta( 'name' );
		}

		/**
		 * Get location country.
		 *
		 * @return array|bool|mixed
		 */
		public function get_location_country() {
			return $this->get_meta( 'country' );
		}

		/**
		 * Get location province.
		 *
		 * @return array|bool|mixed
		 */
		public function get_location_province() {
			return $this->get_meta( 'province' );
		}

		/**
		 * Get location city.
		 *
		 * @return array|bool|mixed
		 */
		public function get_location_city() {
			return $this->get_meta( 'city' );
		}

		/**
		 * Get location address.
		 *
		 * @return array|bool|mixed
		 */
		public function get_location_address() {
			return $this->get_meta( 'address' );
		}

		/**
		 * Get location postal code.
		 *
		 * @return array|bool|mixed
		 */
		public function get_location_postal() {
			return $this->get_meta( 'postal' );
		}

		/**
		 * Get location photo id.
		 *
		 * @return array|bool|mixed
		 */
		public function get_location_photo() {
			return $this->get_meta( 'photo' );
		}

		/**
		 * Get location image url.
		 *
		 * @param string $size size of the photo.
		 *
		 * @return false|string
		 */
		public function get_location_photo_url( $size = 'medium' ) {
			return wp_get_attachment_image_url( $this->get_location_photo(), $size );
		}

		/**
		 * Get location description.
		 *
		 * @return array|bool|mixed
		 */
		public function get_location_description() {
			return $this->get_meta( 'description' );
		}

		/**
		 * Get full location info
		 *
		 * @param bool $include_name whether include location name or not.
		 *
		 * @return string
		 */
		public function get_location_paragraph( $include_name = true ) {
			return ( $include_name ? $this->get_location_name() . ', ' : '' ) . $this->get_location_address() . ', ' . $this->get_location_city() . ', ' . $this->get_location_province() . ' ' . $this->get_location_postal() . ' ' . Helper::translate_country_code( $this->get_location_country() );
		}
	}
}
