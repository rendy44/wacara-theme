<?php
/**
 * Use this class to register custom post types.
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.2
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
			Helper::register_post_type( 'event', array(), array(), 'dashicons-calendar-alt' );
		}

		/**
		 * Register header post type.
		 */
		private function register_header_post_type() {
			Helper::register_post_type(
				'header',
				array(
					'all_items' => __( 'Headers', 'wacara' ),
				),
				array(
					'publicly_queryable' => false,
					'show_in_menu'       => 'edit.php?post_type=event',
				)
			);
		}

		/**
		 * Register location post type.
		 */
		private function register_location_post_type() {
			Helper::register_post_type(
				'location',
				array(
					'all_items' => __( 'Locations', 'wacara' ),
				),
				array(
					'publicly_queryable' => false,
					'show_in_menu'       => 'edit.php?post_type=event',
				)
			);
		}

		/**
		 * Register speaker post type.
		 *
		 * @version 0.0.2
		 */
		private function register_speaker_post_type() {
			Helper::register_post_type(
				'speaker',
				array(
					'all_items' => __( 'Speakers', 'wacara' ),
				),
				array(
					'publicly_queryable' => false,
					'show_in_menu'       => 'edit.php?post_type=event',
					'supports'           => array( 'title', 'thumbnail' ),
				)
			);
		}

		/**
		 * Register price post type.
		 */
		private function register_price_post_type() {
			Helper::register_post_type(
				'price',
				array(
					'all_items' => __( 'Prices', 'wacara' ),
				),
				array(
					'publicly_queryable' => false,
					'show_in_menu'       => 'edit.php?post_type=event',
				)
			);
		}

		/**
		 * Register registrant post type.
		 */
		private function register_registrant_post_type() {
			Helper::register_post_type(
				'registrant',
				array(),
				array(
					'public'       => false,
					'query_var'    => false,
					'rewrite'      => array( 'slug' => 'reg' ),
					'capabilities' => array(
						'create_posts' => 'do_not_allow',
					),
					'map_meta_cap' => true,
					'supports'     => array( 'none' ),
				),
				'dashicons-businessperson'
			);
		}
	}

	CPT::init();
}
