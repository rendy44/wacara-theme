<?php
/**
 * Use this class to add some configuration to override WordPress default behaviors
 *
 * @author  Rendy
 * @package Wacara
 * @version 0.0.1
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
			// Override single post template.
			add_filter( 'single_template', [ $this, 'override_single_post_callback' ], 10, 3 );
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

			$used_post_types   = [ 'event', 'registrant' ];
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
