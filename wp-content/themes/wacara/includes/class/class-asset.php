<?php
/**
 * Use this class to enqueuing assets both in front-end and back-end
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

use Skeleton\Payment\Stripe_Payment;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Skeleton\Asset' ) ) {

	/**
	 * Class Asset
	 *
	 * @package Skeleton
	 */
	class Asset {

		/**
		 * Instance variable
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Theme version variable.
		 *
		 * @var string
		 */
		private $version = '';

		/**
		 * Variable to mapping all css in front-end
		 *
		 * @var array
		 */
		private $front_css = [];

		/**
		 * Variable to mapping all js in front-end
		 *
		 * @var array
		 */
		private $front_js = [];

		/**
		 * Variable for mapping all css in admin
		 *
		 * @var array
		 */
		private $admin_css = [];

		/**
		 * Variable for mapping all js in admin
		 *
		 * @var array
		 */
		private $admin_js = [];

		/**
		 * Singleton
		 *
		 * @return null|Asset
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Asset constructor.
		 */
		private function __construct() {
			$theme_object  = wp_get_theme();
			$this->version = $theme_object->get( 'Version' );
			$this->load_front_asset();
			$this->load_admin_asset();

			add_filter( 'script_loader_tag', [ $this, 'load_as_module' ], 10, 3 );
		}

		/**
		 * Filters the HTML script tag of an enqueued script.
		 *
		 * @param string $tag The `<script>` tag for the enqueued script.
		 * @param string $handle The script's registered handle.
		 * @param string $src The script's source URL.
		 *
		 * @return  string
		 * @since 4.1.0
		 */
		public function load_as_module( $tag, $handle, $src ) {
			$modules = [ 'main', 'checkin', 'app_be' ];
			if ( in_array( $handle, $modules, true ) ) {
				$tag = '<script type="module" src="' . esc_url( $src ) . '"></script>'; // phpcs:ignore
			}

			return $tag;
		}

		/**
		 * Load js file
		 *
		 * @param string $name js name.
		 * @param array  $obj_js js object.
		 */
		private function load_js( $name, array $obj_js ) {
			$depth = ! empty( $obj_js['depth'] ) ? $obj_js['depth'] : [];
			wp_enqueue_script( $name, $obj_js['url'], $depth, $this->version, true );
			if ( isset( $obj_js['vars'] ) ) {
				wp_localize_script( $name, 'obj', $obj_js['vars'] );
			}
		}

		/**
		 * Load css file
		 *
		 * @param string $name css name.
		 * @param array  $obj_css css object.
		 */
		private function load_css( $name, array $obj_css ) {
			$depth = ! empty( $obj_css['depth'] ) ? $obj_css['depth'] : [];
			wp_enqueue_style( $name, $obj_css['url'], $depth, $this->version );
		}

		/**
		 * Map all assets that will be loaded in front end
		 */
		private function map_front_asset() {
			// CSS files.
			$this->front_css = [
				'google_font' => [
					'url' => 'https://fonts.googleapis.com/css?family=Work+Sans:200,300,400,500,600,700&amp;display=swap',
				],
				'bootstrap'   => [
					'url' => TEMP_URI . '/assets/vendor/bootstrap/css/bootstrap.min.css',
				],
				'fontawesome' => [
					'url' => TEMP_URI . '/assets/vendor/fontawesome-free/css/all.min.css',
				],
				'aos'         => [
					'url' => TEMP_URI . '/assets/vendor/aos/aos.css',
				],
				'baguettebox' => [
					'url' => TEMP_URI . '/assets/vendor/baguettebox/baguetteBox.min.css',
				],
				'sweetalert2' => [
					'url' => TEMP_URI . '/assets/vendor/sweetalert2/dist/sweetalert2.min.css',
				],
				'slick'       => [
					'url' => TEMP_URI . '/assets/vendor/slick/slick.css',
				],
				'slick-theme' => [
					'url' => TEMP_URI . '/assets/vendor/slick/slick-theme.css',
				],
				'app'         => [
					'url' => TEMP_URI . '/assets/css/app.css',
				],
			];

			// JS files.
			$this->front_js = [
				'bootstrap'         => [
					'url' => TEMP_URI . '/assets/vendor/bootstrap/js/bootstrap.bundle.min.js',
				],
				'aos'               => [
					'url' => TEMP_URI . '/assets/vendor/aos/aos.js',
				],
				'baguettebox'       => [
					'url' => TEMP_URI . '/assets/vendor/baguettebox/baguetteBox.min.js',
				],
				'sweetalert2'       => [
					'url' => TEMP_URI . '/assets/vendor/sweetalert2/dist/sweetalert2.min.js',
				],
				'slick'             => [
					'url' => TEMP_URI . '/assets/vendor/slick/slick.min.js',
				],
				'jquery-validation' => [
					'url' => TEMP_URI . '/assets/vendor/jquery-validation/dist/jquery.validate.min.js',
				],
				'stripe'            => [
					'url' => 'https://js.stripe.com/v3/',
				],
				'checkin'           => [
					'url' => TEMP_URI . '/assets/js/checkin.js',
				],
				'main'              => [
					'url' => TEMP_URI . '/assets/js/main.js',
				],
				'app'               => [
					'url'   => TEMP_URI . '/assets/js/app.min.js',
					'vars'  => [
						'ajax_url'        => admin_url( 'admin-ajax.php' ),
						'publishable_key' => Stripe_Payment::get_publishable_key(),
					],
					'depth' => [ 'jquery' ],
				],
			];
		}

		/**
		 * Load all assets in front-end
		 */
		private function load_front_asset() {
			$this->map_front_asset();

			add_action( 'wp_enqueue_scripts', [ $this, 'front_asset_callback' ] );
		}

		/**
		 * Map all assets that will be loaded in admin
		 */
		private function map_admin_asset() {
			$this->admin_js = [
				'cmb2_conditionals' => [
					'url'   => TEMP_URI . '/assets/admin/js/cmb2-conditionals.js',
					'depth' => [ 'jquery', 'cmb2-scripts' ],
				],
				'inputosaurus'      => [
					'url'   => TEMP_URI . '/assets/vendor/inputosaurus/inputosaurus.js',
					'depth' => [ 'jquery', 'cmb2-scripts' ],
				],
				'app_be'            => [
					'url'  => TEMP_URI . '/assets/admin/js/app.js',
					'vars' => [
						'ajax_url' => admin_url( 'admin-ajax.php' ),
					],
				],
			];

			$this->admin_css = [
				'inputosaurus' => [
					'url'   => TEMP_URI . '/assets/vendor/inputosaurus/inputosaurus.css',
					'depth' => [ 'cmb2-styles' ],
				],
				'app'          => [
					'url' => TEMP_URI . '/assets/admin/css/app.css',
				],
			];
		}

		/**
		 * Load all assets in admin.
		 */
		private function load_admin_asset() {
			$this->map_admin_asset();

			add_action( 'admin_enqueue_scripts', [ $this, 'admin_assets_callback' ] );
		}

		/**
		 * Callback for loading front end assets
		 */
		public function front_asset_callback() {
			// Load all css files.
			foreach ( $this->front_css as $css_name => $css_obj ) {
				wp_enqueue_style( $css_name, $css_obj['url'], [], $this->version );
			}

			// Load all js files.
			foreach ( $this->front_js as $js_name => $js_obj ) {
				$this->load_js( $js_name, $js_obj );
			}
		}

		/**
		 * Callback for loading admin assets.
		 */
		public function admin_assets_callback() {
			// Load all js files.
			foreach ( $this->admin_js as $js_name => $js_obj ) {
				$this->load_js( $js_name, $js_obj );
			}

			// Load all css files.
			foreach ( $this->admin_css as $css_name => $css_obj ) {
				$this->load_css( $css_name, $css_obj );
			}
		}
	}
}

Asset::init();
