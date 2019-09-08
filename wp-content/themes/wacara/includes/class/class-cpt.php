<?php
/**
 * Use this class to register custom post types.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Skeleton\CPT' ) ) {
	/**
	 * Class CPT
	 *
	 * @package Skeleton
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
			// Register event post type.
			add_action( 'init', [ $this, 'register_event_post_type_callback' ] );
			// Register location post type.
			add_action( 'init', [ $this, 'register_location_post_type_callback' ] );
			// Register speaker post type.
			add_action( 'init', [ $this, 'register_speaker_post_type_callback' ] );
		}

		/**
		 * Callback for registering event post type.
		 */
		public function register_event_post_type_callback() {
			$labels = [
				'name'               => _x( 'Events', 'post type general name', 'your-plugin-textdomain' ),
				'singular_name'      => _x( 'Event', 'post type singular name', 'your-plugin-textdomain' ),
				'menu_name'          => _x( 'Events', 'admin menu', 'your-plugin-textdomain' ),
				'name_admin_bar'     => _x( 'Event', 'add new on admin bar', 'your-plugin-textdomain' ),
				'add_new'            => _x( 'Add New', 'event', 'your-plugin-textdomain' ),
				'add_new_item'       => __( 'Add New Event', 'your-plugin-textdomain' ),
				'new_item'           => __( 'New Event', 'your-plugin-textdomain' ),
				'edit_item'          => __( 'Edit Event', 'your-plugin-textdomain' ),
				'view_item'          => __( 'View Event', 'your-plugin-textdomain' ),
				'all_items'          => __( 'All Events', 'your-plugin-textdomain' ),
				'search_items'       => __( 'Search Events', 'your-plugin-textdomain' ),
				'parent_item_colon'  => __( 'Parent Events:', 'your-plugin-textdomain' ),
				'not_found'          => __( 'No events found.', 'your-plugin-textdomain' ),
				'not_found_in_trash' => __( 'No events found in Trash.', 'your-plugin-textdomain' ),
			];

			$args = [
				'labels'             => $labels,
				'description'        => __( 'Description.', 'your-plugin-textdomain' ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => [ 'slug' => 'event' ],
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => 5,
				'supports'           => [ 'title' ],
				'menu_icon'          => 'dashicons-calendar-alt',
			];

			register_post_type( 'event', $args );
		}

		/**
		 * Callback for registering location post type.
		 */
		public function register_location_post_type_callback() {
			$labels = [
				'name'               => _x( 'Locations', 'post type general name', 'your-plugin-textdomain' ),
				'singular_name'      => _x( 'Location', 'post type singular name', 'your-plugin-textdomain' ),
				'menu_name'          => _x( 'Locations', 'admin menu', 'your-plugin-textdomain' ),
				'name_admin_bar'     => _x( 'Location', 'add new on admin bar', 'your-plugin-textdomain' ),
				'add_new'            => _x( 'Add New', 'location', 'your-plugin-textdomain' ),
				'add_new_item'       => __( 'Add New Location', 'your-plugin-textdomain' ),
				'new_item'           => __( 'New Location', 'your-plugin-textdomain' ),
				'edit_item'          => __( 'Edit Location', 'your-plugin-textdomain' ),
				'view_item'          => __( 'View Location', 'your-plugin-textdomain' ),
				'all_items'          => __( 'All Locations', 'your-plugin-textdomain' ),
				'search_items'       => __( 'Search Locations', 'your-plugin-textdomain' ),
				'parent_item_colon'  => __( 'Parent Locations:', 'your-plugin-textdomain' ),
				'not_found'          => __( 'No locations found.', 'your-plugin-textdomain' ),
				'not_found_in_trash' => __( 'No locations found in Trash.', 'your-plugin-textdomain' ),
			];

			$args = [
				'labels'             => $labels,
				'description'        => __( 'Description.', 'your-plugin-textdomain' ),
				'public'             => true,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => [ 'slug' => 'location' ],
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => 6,
				'supports'           => [ 'title' ],
				'menu_icon'          => 'dashicons-location',
			];

			register_post_type( 'location', $args );
		}

		/**
		 * Callback for registering speaker post type.
		 */
		public function register_speaker_post_type_callback() {
			$labels = [
				'name'               => _x( 'Speakers', 'post type general name', 'your-plugin-textdomain' ),
				'singular_name'      => _x( 'Speaker', 'post type singular name', 'your-plugin-textdomain' ),
				'menu_name'          => _x( 'Speakers', 'admin menu', 'your-plugin-textdomain' ),
				'name_admin_bar'     => _x( 'Speaker', 'add new on admin bar', 'your-plugin-textdomain' ),
				'add_new'            => _x( 'Add New', 'speaker', 'your-plugin-textdomain' ),
				'add_new_item'       => __( 'Add New Speaker', 'your-plugin-textdomain' ),
				'new_item'           => __( 'New Speaker', 'your-plugin-textdomain' ),
				'edit_item'          => __( 'Edit Speaker', 'your-plugin-textdomain' ),
				'view_item'          => __( 'View Speaker', 'your-plugin-textdomain' ),
				'all_items'          => __( 'All Speakers', 'your-plugin-textdomain' ),
				'search_items'       => __( 'Search Speakers', 'your-plugin-textdomain' ),
				'parent_item_colon'  => __( 'Parent Speakers:', 'your-plugin-textdomain' ),
				'not_found'          => __( 'No speakers found.', 'your-plugin-textdomain' ),
				'not_found_in_trash' => __( 'No speakers found in Trash.', 'your-plugin-textdomain' ),
			];

			$args = [
				'labels'             => $labels,
				'description'        => __( 'Description.', 'your-plugin-textdomain' ),
				'public'             => true,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => [ 'slug' => 'speaker' ],
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => 7,
				'supports'           => [ 'title', 'thumbnail' ],
				'menu_icon'          => 'dashicons-businessperson',
			];

			register_post_type( 'speaker', $args );
		}
	}
}

CPT::init();
