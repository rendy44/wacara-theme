<?php
/**
 * Class to manage assets.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara_Theme;

use Wacara\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara_Theme\Assets' ) ) {

	/**
	 * Class Assets
	 *
	 * @package Wacara_Theme
	 */
	class Assets {

		/**
		 * Instance variable.
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Front-end css variable.
		 *
		 * @var array
		 */
		private $front_css = [];

		/**
		 * Front-end js variable.
		 *
		 * @var array
		 */
		private $front_js = [];

		/**
		 * Singleton functions.
		 *
		 * @return Assets|null
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Assets constructor.
		 */
		private function __construct() {
			$this->load_front_assets();
		}

		/**
		 * Load assets in front-end.
		 */
		private function load_front_assets() {
			$this->map_front_assets();

			add_action( 'wp_enqueue_scripts', [ $this, 'load_front_assets_callback' ] );
		}

		/**
		 * Map assets that will be loaded in front-end.
		 */
		private function map_front_assets() {
			$this->front_css = [
				'remixicon'               => [
					'url' => WACARA_MAYBE_THEME_URI . '/assets/lib/remixicon/remixicon.css',
				],
				'lity'                    => [
					'url' => WACARA_MAYBE_THEME_URI . '/assets/lib/lity/lity.min.css',
				],
				'wacara_theme_main_style' => [
					'url' => WACARA_MAYBE_THEME_URI . '/assets/css/wacara.css',
				],
			];

			$this->front_js = [
				'lity'                 => [
					'url'    => WACARA_MAYBE_THEME_URI . '/assets/lib/lity/lity.min.js',
					'module' => false,
				],
				'wacara_theme_main_js' => [
					'url'   => WACARA_MAYBE_THEME_URI . '/assets/js/wacara.js',
					'vars'  => [],
					'depth' => [ 'jquery' ],
				],
			];
		}

		/**
		 * Callback for loading front-end assets.
		 */
		public function load_front_assets_callback() {

			// Load css files.
			foreach ( $this->front_css as $name => $css_obj ) {
				Helper::load_css( $name, $css_obj );
			}

			// Load js files.
			foreach ( $this->front_js as $name => $js_obj ) {
				Helper::load_js( $name, $js_obj );
			}
		}
	}

	Assets::init();
}
