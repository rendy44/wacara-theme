<?php
/**
 * This is our main class, only use this class to load other classes and dependency libraries
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Wacara' ) ) {
	/**
	 * Class Wacara
	 *
	 * @package Wacara
	 */
	class Wacara {

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
		private $classes = array();

		/**
		 * Libraries variable
		 *
		 * @var array
		 */
		private $libraries = array();

		/**
		 * Singleton
		 *
		 * @return null|Wacara
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
			$this->load_libraries();
			$this->load_classes();
			$this->load_hooks();
			$this->init_settings();
		}

		/**
		 * Trigger loaded plugin hook.
		 */
		private function load_hooks() {
			add_action( 'plugins_loaded', array( $this, 'trigger_hook_loader_callback' ), - 1 );
			add_action( 'plugins_loaded', array( $this, 'load_language_callback' ) );
		}

		/**
		 * Run the default settings.
		 */
		private function init_settings() {

			// Register registrant default status.
			Registrant_Status::init();
		}

		/**
		 * Callback for triggering hook loader.
		 */
		public function trigger_hook_loader_callback() {
			do_action( 'wacara_loaded' );
		}

		/**
		 * Callback for loading language.
		 */
		public function load_language_callback() {
			load_plugin_textdomain( 'wacara', false, WACARA_PATH . 'i18n/' );
		}

		/**
		 * Map all dependency classes that's required by our theme
		 */
		private function map_classes() {
			$this->classes = array(
				'class-result',
				'class-helper',
				'class-master',
				'class-register-payment',
				'class-template',
				'class-ui',
				'class-setting',
				'class-page-template',
				'class-options',
				'class-metabox',
				'class-cpt',
				'class-cmb2-conditionals',
				'class-cmb2-hooks',
				'class-ajax',
				'class-action',
				'class-post',
				'class-event',
				'class-event-pricing',
				'class-event-location',
				'class-event-header',
				'class-event-speaker',
				'class-registrant',
				'class-registrant-status',
				'class-registrant-logs',
				'mailer/class-mailer',
				'mailer/class-mailer-event',
				'mailer/class-mailer-after-register',
				'mailer/class-mailer-after-done',
				'class-asset',
				'class-post-columns',
				'abstract/class-payment-method',
			);
		}

		/**
		 * Map all dependency libraries that`s required by our theme
		 */
		private function map_libraries() {
			$this->libraries = array(
				'cmb2/init',
				'cmb2-tabs/plugin',
				'cmb2-select2/cmb-field-select2',
				'phpqrcode/qrlib',
			);
		}

		/**
		 * Load dependency classes
		 */
		private function load_classes() {
			$this->map_classes();

			foreach ( $this->classes as $class ) {
				require WACARA_PATH . "/includes/class/{$class}.php";
			}
		}

		/**
		 * Load dependency libraries
		 */
		private function load_libraries() {
			$this->map_libraries();

			foreach ( $this->libraries as $library ) {
				require WACARA_PATH . "/includes/lib/{$library}.php";
			}
		}
	}

	Wacara::init();
}
