<?php
/**
 * Main class that will do anything.
 * Everything is started from here.
 *
 * @author WPerfekt
 * @package Wacara_Theme
 * @version 0.0.1
 */

namespace Wacara_Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara_Theme\Wacara' ) ) {

	/**
	 * Class Wacara
	 *
	 * @package Wacara_Theme
	 */
	class Wacara {

		/**
		 * Instance variable.
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Singleton.
		 *
		 * @return Wacara|null
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Wacara constructor.
		 */
		private function __construct() {
			$this->add_supports();
			$this->load_classes();
			$this->add_mobile_optimization();
		}

		/**
		 * Add specific theme supports.
		 */
		private function add_supports() {

			// Make sure the theme is supporting the Wacara.
			add_theme_support( 'wacara' );

			// Add other necessary supports.
			add_theme_support( 'title-tag' );
			add_theme_support( 'menus' );
			add_theme_support( 'post-thumbnails' );
		}

		/**
		 * Load dependency classes.
		 */
		private function load_classes() {
			include WCR_THM_PATH . '/includes/class-helper.php';
			include WCR_THM_PATH . '/includes/class-assets.php';
			include WCR_THM_PATH . '/includes/class-customizer.php';
		}

		/**
		 * Add mobile optimization.
		 */
		private function add_mobile_optimization() {
			add_action( 'wp_head', [ $this, 'mobile_optimization_callback' ] );
		}

		/**
		 * Callback for adding mobile optimization.
		 */
		public function mobile_optimization_callback() {
			?>
			<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
			<?php
		}
	}

	Wacara::init();
}
