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
	}
}

CPT::init();
