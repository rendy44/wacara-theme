<?php
/**
 * Use this class to register custom post types.
 *
 * @author  Rendy
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\CPT' ) ) {

	/**
	 * Class CPT
	 *
	 * @package Wacara
	 */
	class CPT {

		/**
		 * Instance variable
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Singleton function
		 *
		 * @return CPT|null
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * CPT constructor.
		 */
		private function __construct() {
			$this->register_event_post_type();
			$this->register_header_post_type();
			$this->register_location_post_type();
			$this->register_speaker_post_type();
			$this->register_price_post_type();
			$this->register_registrant_post_type();
		}

		/**
		 * Register event post type.
		 */
		private function register_event_post_type() {
			Helper::register_post_type( 'event', [], [], 'dashicons-calendar-alt' );
		}

		/**
		 * Register header post type.
		 */
		private function register_header_post_type() {
			Helper::register_post_type(
				'header',
				[
					'all_items' => __( 'Headers', 'wacara' ),
				],
				[
					'publicly_queryable' => false,
					'show_in_menu'       => 'edit.php?post_type=event',
				]
			);
		}

		/**
		 * Register location post type.
		 */
		private function register_location_post_type() {
			Helper::register_post_type(
				'location',
				[
					'all_items' => __( 'Locations', 'wacara' ),
				],
				[
					'publicly_queryable' => false,
					'show_in_menu'       => 'edit.php?post_type=event',
				]
			);
		}

		/**
		 * Register speaker post type.
		 */
		private function register_speaker_post_type() {
			Helper::register_post_type(
				'speaker',
				[
					'all_items' => __( 'Speakers', 'wacara' ),
				],
				[
					'publicly_queryable' => false,
					'show_in_menu'       => 'edit.php?post_type=event',
				]
			);
		}

		/**
		 * Register price post type.
		 */
		private function register_price_post_type() {
			Helper::register_post_type(
				'price',
				[
					'all_items' => __( 'Prices', 'wacara' ),
				],
				[
					'publicly_queryable' => false,
					'show_in_menu'       => 'edit.php?post_type=event',
				]
			);
		}

		/**
		 * Register registrant post type.
		 */
		private function register_registrant_post_type() {
			Helper::register_post_type(
				'registrant',
				[],
				[
					'public'       => false,
					'query_var'    => false,
					'rewrite'      => [ 'slug' => 'reg' ],
					'capabilities' => [
						'create_posts' => 'do_not_allow',
					],
				],
				'dashicons-businessperson'
			);
		}
	}

	CPT::init();
}
