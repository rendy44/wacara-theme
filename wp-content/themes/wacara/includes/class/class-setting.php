<?php
/**
 * Use this class to add some configuration to override WordPress default behaviors
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Skeleton\Setting' ) ) {

	/**
	 * Class Setting
	 *
	 * @package Skeleton
	 */
	class Setting {

		/**
		 * Instance variable
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Singleton
		 *
		 * @return null|Setting
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Setting constructor.
		 */
		private function __construct() {
			$this->override_wp_default_settings();
			$this->add_theme_support();
		}

		/**
		 * Override wp default setting.
		 */
		private function override_wp_default_settings() {
			// Hide admin bar in front-end.
			add_filter( 'show_admin_bar', '__return_false' );
		}

		/**
		 * Add theme support
		 */
		private function add_theme_support() {

			// Add theme supports.
			add_theme_support( 'title-tag' );
			add_theme_support( 'menus' );
			add_theme_support( 'post-thumbnails' );

			// Register custom sidebar.
			register_sidebar(
				[
					'name'          => __( 'Sidebar' ),
					'id'            => 'sk_sidebar',
					'before_widget' => '<div class="card widget-item mb-4">',
					'before_title'  => '<h5 class="card-header">',
					'after_title'   => '</h5>',
					'after_widget'  => '</div>',
				]
			);

			// Register custom image size.
			add_image_size( 'wacara_gallery_thumbnail', 350, 350, true );
			add_image_size( 'wacara_location_gallery_thumbnail', 768, 350, true );

			// Load translation files.
			add_action( 'after_setup_theme', [ $this, 'wacara_language_domain_callback' ] );
		}

		/**
		 * Callback for loading languages domain.
		 */
		public function wacara_language_domain_callback() {
			load_theme_textdomain( 'wacara', TEMP_DIR . '/i18n' );
		}
	}
}

Setting::init();
