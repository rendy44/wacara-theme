<?php
/**
 * Use this class to add some configuration to override WordPress default behaviors
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Wacara\Setting' ) ) {

	/**
	 * Class Setting
	 *
	 * @package Wacara
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
			$this->add_theme_support();

			// Hide admin bar in front-end.
			add_filter( 'show_admin_bar', '__return_false' );

			// Override single post template.
			add_filter( 'single_template', [ $this, 'override_single_post_callback' ], 10, 3 );
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
			load_theme_textdomain( 'wacara', WACARA_PATH . '/i18n' );
		}

		/**
		 * Override custom template
		 *
		 * @param string $template Path to the template. See locate_template().
		 * @param string $type Sanitized filename without extension.
		 * @param array  $templates A list of template candidates, in descending order of priority.
		 *
		 * @return string
		 */
		public function override_single_post_callback( $template, $type, $templates ) {
			global $post;

			$used_post_types   = [ 'event', 'participant' ];
			$current_post_type = $post->post_type;
			if ( in_array( $current_post_type, $used_post_types, true ) ) {
				$template_found = Helper::locate_template( "single-{$current_post_type}" );
				$template       = $template_found ? $template_found : WACARA_PATH . "templates/single-{$current_post_type}.php";
			}

			return $template;
		}
	}

	Setting::init();
}
