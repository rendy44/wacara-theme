<?php
/**
 * Main class that will do anything.
 * Everything is started from here.
 *
 * @author Rendy
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
		}

		/**
		 * Add specific theme supports.
		 */
		private function add_supports() {
			add_theme_support( 'wacara' );
		}

		/**
		 * Load dependency classes.
		 */
		private function load_classes() {
			include WCR_THM_PATH . 'includes/class-assets.php';
		}
	}

	Wacara::init();
}
