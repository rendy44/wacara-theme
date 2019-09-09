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
			// Register price post type.
			add_action( 'init', [ $this, 'register_price_post_type_callback' ] );
		}

		/**
		 * Callback for registering event post type.
		 */
		public function register_event_post_type_callback() {
			$labels = [
				'name'               => _x( 'Events', 'post type general name', 'wacara' ),
				'singular_name'      => _x( 'Event', 'post type singular name', 'wacara' ),
				'menu_name'          => _x( 'Events', 'admin menu', 'wacara' ),
				'name_admin_bar'     => _x( 'Event', 'add new on admin bar', 'wacara' ),
				'add_new'            => _x( 'Add New', 'event', 'wacara' ),
				'add_new_item'       => __( 'Add New Event', 'wacara' ),
				'new_item'           => __( 'New Event', 'wacara' ),
				'edit_item'          => __( 'Edit Event', 'wacara' ),
				'view_item'          => __( 'View Event', 'wacara' ),
				'all_items'          => __( 'All Events', 'wacara' ),
				'search_items'       => __( 'Search Events', 'wacara' ),
				'parent_item_colon'  => __( 'Parent Events:', 'wacara' ),
				'not_found'          => __( 'No events found.', 'wacara' ),
				'not_found_in_trash' => __( 'No events found in Trash.', 'wacara' ),
			];

			$args = [
				'labels'             => $labels,
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
				'name'               => _x( 'Locations', 'post type general name', 'wacara' ),
				'singular_name'      => _x( 'Location', 'post type singular name', 'wacara' ),
				'menu_name'          => _x( 'Locations', 'admin menu', 'wacara' ),
				'name_admin_bar'     => _x( 'Location', 'add new on admin bar', 'wacara' ),
				'add_new'            => _x( 'Add New', 'location', 'wacara' ),
				'add_new_item'       => __( 'Add New Location', 'wacara' ),
				'new_item'           => __( 'New Location', 'wacara' ),
				'edit_item'          => __( 'Edit Location', 'wacara' ),
				'view_item'          => __( 'View Location', 'wacara' ),
				'all_items'          => __( 'All Locations', 'wacara' ),
				'search_items'       => __( 'Search Locations', 'wacara' ),
				'parent_item_colon'  => __( 'Parent Locations:', 'wacara' ),
				'not_found'          => __( 'No locations found.', 'wacara' ),
				'not_found_in_trash' => __( 'No locations found in Trash.', 'wacara' ),
			];

			$args = [
				'labels'             => $labels,
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
				'name'               => _x( 'Speakers', 'post type general name', 'wacara' ),
				'singular_name'      => _x( 'Speaker', 'post type singular name', 'wacara' ),
				'menu_name'          => _x( 'Speakers', 'admin menu', 'wacara' ),
				'name_admin_bar'     => _x( 'Speaker', 'add new on admin bar', 'wacara' ),
				'add_new'            => _x( 'Add New', 'speaker', 'wacara' ),
				'add_new_item'       => __( 'Add New Speaker', 'wacara' ),
				'new_item'           => __( 'New Speaker', 'wacara' ),
				'edit_item'          => __( 'Edit Speaker', 'wacara' ),
				'view_item'          => __( 'View Speaker', 'wacara' ),
				'all_items'          => __( 'All Speakers', 'wacara' ),
				'search_items'       => __( 'Search Speakers', 'wacara' ),
				'parent_item_colon'  => __( 'Parent Speakers:', 'wacara' ),
				'not_found'          => __( 'No speakers found.', 'wacara' ),
				'not_found_in_trash' => __( 'No speakers found in Trash.', 'wacara' ),
			];

			$args = [
				'labels'             => $labels,
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

		/**
		 * Callback for registering price post type.
		 */
		public function register_price_post_type_callback() {
			$labels = [
				'name'               => _x( 'Prices', 'post type general name', 'wacara' ),
				'singular_name'      => _x( 'Price', 'post type singular name', 'wacara' ),
				'menu_name'          => _x( 'Prices', 'admin menu', 'wacara' ),
				'name_admin_bar'     => _x( 'Price', 'add new on admin bar', 'wacara' ),
				'add_new'            => _x( 'Add New', 'price', 'wacara' ),
				'add_new_item'       => __( 'Add New Price', 'wacara' ),
				'new_item'           => __( 'New Price', 'wacara' ),
				'edit_item'          => __( 'Edit Price', 'wacara' ),
				'view_item'          => __( 'View Price', 'wacara' ),
				'all_items'          => __( 'All Prices', 'wacara' ),
				'search_items'       => __( 'Search Prices', 'wacara' ),
				'parent_item_colon'  => __( 'Parent Prices:', 'wacara' ),
				'not_found'          => __( 'No prices found.', 'wacara' ),
				'not_found_in_trash' => __( 'No prices found in Trash.', 'wacara' ),
			];

			$args = [
				'labels'             => $labels,
				'public'             => true,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => [ 'slug' => 'price' ],
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => 8,
				'supports'           => [ 'title' ],
				'menu_icon'          => 'dashicons-products',
			];

			register_post_type( 'price', $args );
		}
	}
}

CPT::init();
