<?php
/**
 * Class for managing the page templates.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Page_Template' ) ) {

	/**
	 * Class Page_Template
	 *
	 * @package Wacara
	 */
	class Page_Template {

		/**
		 * Instance variable.
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Templates variable.
		 *
		 * @var array
		 */
		private $templates = [];

		/**
		 * Singleton.
		 *
		 * @return Page_Template|null
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Page_Template constructor.
		 */
		private function __construct() {

			// Define new templates.
			$this->templates = [
				'self-checkin-page.php' => __( 'Self-Checkin Page', 'wcara' ),
			];

			// Add template list in page metabox.
			add_filter( 'theme_page_templates', [ $this, 'page_templates_callback' ] );

			// Add a filter to the save post to inject out template into the page cache.
			add_filter( 'wp_insert_post_data', [ $this, 'register_page_templates_callback' ] );

			// Add a filter to the template include to determine if the page has our template assigned and return it's path.
			add_filter( 'template_include', [ $this, 'view_page_template_callback' ] );
		}

		/**
		 * Callback for modifying template list in page metabox.
		 *
		 * @param array $templates current templates.
		 *
		 * @return array
		 */
		public function page_templates_callback( $templates ) {

			// Merge the templates.
			return array_merge( $templates, $this->templates );
		}

		/**
		 * Callback for registering template into page's cache.
		 *
		 * @param array $atts current templates.
		 *
		 * @return mixed
		 */
		public function register_page_templates_callback( $atts ) {

			// Create the key used for the themes cache.
			$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

			// Retrieve the cache list.
			// If it doesn't exist, or it's empty prepare an array.
			$templates = wp_get_theme()->get_page_templates();
			if ( empty( $templates ) ) {
				$templates = [];
			}

			// New cache, therefore remove the old one.
			wp_cache_delete( $cache_key, 'themes' );

			// Now add our template to the list of templates by merging our templates
			// with the existing templates array from the cache.
			$templates = array_merge( $templates, $this->templates );

			// Add the modified cache to allow WordPress to pick it up for listing available templates.
			wp_cache_add( $cache_key, $templates, 'themes', 1800 );

			return $atts;
		}

		/**
		 * Callback for returning page templates.
		 *
		 * @param string $template path of the current page template.
		 *
		 * @return string
		 */
		public function view_page_template_callback( $template ) {
			global $post;

			// Return template if post is empty.
			if ( ! $post ) {
				return $template;
			}

			// Return default template if we don't have a custom one defined.
			$current_page_template = Helper::get_post_meta( '_wp_page_template', $post->ID, true, false );
			if ( ! isset( $this->templates[ $current_page_template ] ) ) {
				return $template;
			}
			$file = WACARA_PATH . 'pages/' . $current_page_template;

			// Just to be safe, we check if the file exist first.
			if ( file_exists( $file ) ) {
				return $file;
			} else {
				echo $file; // phpcs:ignore
			}

			// Return template.
			return $template;
		}
	}

	Page_Template::init();
}
