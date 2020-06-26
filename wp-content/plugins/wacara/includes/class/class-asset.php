<?php
/**
 * Use this class to enqueuing assets both in front-end and back-end
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Asset' ) ) {

	/**
	 * Class Asset
	 *
	 * @package Wacara
	 */
	class Asset {

		/**
		 * Instance variable
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Variable to mapping all css in front-end
		 *
		 * @var array
		 */
		private $front_css = array();

		/**
		 * Variable to mapping all js in front-end
		 *
		 * @var array
		 */
		private $front_js = array();

		/**
		 * Variable for mapping all css in admin
		 *
		 * @var array
		 */
		private $admin_css = array();

		/**
		 * Variable for mapping all js in admin
		 *
		 * @var array
		 */
		private $admin_js = array();

		/**
		 * Variable for mapping modules.
		 *
		 * @var array
		 */
		private $module_js = array();

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
			$this->load_front_asset();
			$this->load_admin_asset();
			$this->global_vars();

			add_filter( 'script_loader_tag', array( $this, 'load_as_module' ), 10, 3 );
		}

		/**
		 * Filters the HTML script tag of an enqueued script.
		 *
		 * @param string $tag The <script> tag for the enqueued script.
		 * @param string $handle The script's registered handle.
		 * @param string $src The script's source URL.
		 *
		 * @return  string
		 */
		public function load_as_module( $tag, $handle, $src ) {
			$js_prefix = WACARA_PREFIX . 'module_';
			if ( in_array( $handle, $this->module_js, true ) || false !== strpos( $handle, $js_prefix ) ) {
				$tag = '<script type="module" src="' . esc_url( $src ) . '"></script>'; // phpcs:ignore
			}

			return $tag;
		}

		/**
		 * Save js as module.
		 *
		 * @param string $name the handler name of the js.
		 * @param array  $obj_js js object.
		 */
		private function maybe_add_to_module( $name, $obj_js ) {
			if ( ! empty( $obj_js['module'] ) ) {
				$this->module_js[] = $name;
			}
		}

		/**
		 * Map all assets that will be loaded in front end
		 */
		private function map_front_asset() {
			// CSS files.
			$this->front_css = array(
				'google_font'       => array(
					'url' => 'https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600&display=swap',
				),
				'sweetalert2'       => array(
					'url' => WACARA_URI . 'assets/vendor/css/sweetalert2.min.css',
				),
				'wacara_main_style' => array(
					'url' => WACARA_URI . 'assets/css/wacara.css',
				),
			);

			// JS files.
			$this->front_js = array(
				'sweetalert2'       => array(
					'url'    => WACARA_URI . 'assets/vendor/js/sweetalert2.min.js',
					'module' => false,
				),
				'jquery-validation' => array(
					'url'    => WACARA_URI . 'assets/vendor/js/jquery.validate.min.js',
					'module' => false,
				),
				'wacara_checkin'    => array(
					'url'   => WACARA_URI . 'assets/js/checkin.js',
					'depth' => array( 'jquery' ),
				),
				'wacara_main_js'    => array(
					'url'   => WACARA_URI . 'assets/js/wacara.js',
					'depth' => array( 'jquery' ),
				),
			);
		}

		/**
		 * Load all assets in front-end
		 */
		private function load_front_asset() {
			$this->map_front_asset();

			add_action( 'wp_enqueue_scripts', array( $this, 'front_asset_callback' ) );
		}

		/**
		 * Map all assets that will be loaded in admin
		 *
		 * @version 0.0.2
		 */
		private function map_admin_asset() {
			$this->admin_js = array(
				'cmb2_conditionals' => array(
					'url'    => WACARA_URI . 'assets/js/admin/cmb2-conditionals.js',
					'depth'  => array( 'jquery', 'cmb2-scripts' ),
					'module' => false,
				),
				'inputosaurus'      => array(
					'url'    => WACARA_URI . 'assets/vendor/js/inputosaurus.js',
					'depth'  => array( 'jquery', 'cmb2-scripts' ),
					'module' => false,
				),
				'app_be'            => array(
					'url' => WACARA_URI . 'assets/js/admin/app.js',
				),
			);

			$this->admin_css = array(
				'inputosaurus' => array(
					'url'   => WACARA_URI . 'assets/vendor/css/inputosaurus.css',
					'depth' => array( 'cmb2-styles' ),
				),
				'app'          => array(
					'url' => WACARA_URI . 'assets/css/admin/app.css',
				),
			);
		}

		/**
		 * Load all assets in admin.
		 */
		private function load_admin_asset() {
			$this->map_admin_asset();

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets_callback' ) );
		}

		/**
		 * Add global variable in head.
		 */
		private function global_vars() {

			// Embed variable in head.
			add_action( 'wp_head', array( $this, 'global_vars_callback' ), 10, 1 );
			add_action( 'admin_head', array( $this, 'global_vars_callback' ), 10, 1 );
		}

		/**
		 * Callback for loading front end assets
		 */
		public function front_asset_callback() {
			// Load all css files.
			foreach ( $this->front_css as $css_name => $css_obj ) {
				Helper::load_css( $css_name, $css_obj );
			}

			// Load all js files.
			foreach ( $this->front_js as $js_name => $js_obj ) {
				Helper::load_js( $js_name, $js_obj );

				$this->maybe_add_to_module( $js_name, $js_obj );
			}
		}

		/**
		 * Callback for loading admin assets.
		 */
		public function admin_assets_callback() {
			// Load all js files.
			foreach ( $this->admin_js as $js_name => $js_obj ) {
				Helper::load_js( $js_name, $js_obj );

				$this->maybe_add_to_module( $js_name, $js_obj );
			}

			// Load all css files.
			foreach ( $this->admin_css as $css_name => $css_obj ) {
				Helper::load_css( $css_name, $css_obj );
			}
		}

		/**
		 * Callback for embedding global variables.
		 */
		public function global_vars_callback() {

			// Prepare default variable.
			$variables = array(
				'prefix'   => WACARA_PREFIX,
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			);

			/**
			 * Wacara global variable filter hook.
			 *
			 * @param array $variables current variables.
			 */
			$variables = apply_filters( 'wacara_filter_global_variables', $variables );

			?>
			<script type="text/javascript">
				/* <![CDATA[ */
				let obj = <?php echo wp_json_encode( $variables ); ?>;
				/* ]]> */
			</script>
			<?php
		}
	}

	Asset::init();
}
