<?php
/**
 * This is our main class, only use this class to load other classes and dependency libraries
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Skeleton\Skeleton' ) ) {
	/**
	 * Class Skeleton
	 *
	 * @package Skeleton
	 */
	class Skeleton {

		/**
		 * Instance variable
		 *
		 * @var null
		 */
		private static $instance = null;
		/**
		 * Classes variable
		 *
		 * @var array
		 */
		private $classes = [];
		/**
		 * Libraries variable
		 *
		 * @var array
		 */
		private $libraries = [];

		/**
		 * Singleton
		 *
		 * @return null|Skeleton
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Skeleton constructor.
		 */
		private function __construct() {
			$this->load_libraries();
			$this->load_classes();
		}

		/**
		 * Map all dependency classes that's required by our theme
		 */
		private function map_classes() {
			$this->classes = [
				'result',
				'template',
				'ui',
				'setting',
				'navwalker',
				'helper',
				'options',
				'metabox',
				'cpt',
				'cmb2-conditionals',
				'cmb2-hooks',
				'ajax',
				'post',
				'event',
				'transaction',
				'participant',
				'payment',
				'mailer',
				'asset',
			];
		}

		/**
		 * Map all dependency libraries that`s required by our theme
		 */
		private function map_libraries() {
			$this->libraries = [
				'cmb2/init',
				'cmb2-tabs/plugin',
				'cmb2-select2/cmb-field-select2',
				'stripe-php/init',
				'phpqrcode/qrlib',
			];
		}

		/**
		 * Load dependency classes
		 */
		private function load_classes() {
			$this->map_classes();

			foreach ( $this->classes as $class ) {
				require TEMP_PATH . "/includes/class/class-{$class}.php";
			}
		}

		/**
		 * Load dependency libraries
		 */
		private function load_libraries() {
			$this->map_libraries();

			foreach ( $this->libraries as $library ) {
				require TEMP_PATH . "/includes/lib/{$library}.php";
			}
		}
	}
}

Skeleton::init();
