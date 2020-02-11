<?php
/**
 * Simple helper class to render php file into output buffer
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Wacara\Template' ) ) {

	/**
	 * Class Template
	 *
	 * @package Wacara
	 */
	class Template {

		/**
		 * Folder name variable
		 *
		 * @var string
		 */
		private static $folder = '';

		/**
		 * Set template folder
		 */
		private static function maybe_set_folder() {
			$folder = WACARA_PATH . '/templates';

			if ( ! self::$folder ) {

				self::do_set_folder( $folder );
			}
		}

		/**
		 * Save folder path.
		 *
		 * @param string $path the folder path.
		 */
		private static function do_set_folder( $path ) {
			self::$folder = rtrim( $path, '/' );
		}

		/**
		 * Check template file existence
		 *
		 * @param string $file_name template file name.
		 *
		 * @return bool|string
		 */
		private static function find_template( $file_name ) {
			self::maybe_set_folder();

			$found = false;
			$file  = self::$folder . "/{$file_name}.php";
			if ( file_exists( $file ) ) {
				$found = $file;
			}

			return $found;
		}

		/**
		 * Render the template
		 *
		 * @param string $template template file path.
		 * @param array  $variables variables that will be injected into template file.
		 *
		 * @return string
		 */
		private static function render_template( $template, $variables = [] ) {
			ob_start();
			foreach ( $variables as $key => $value ) {
				${$key} = $value;
			}
			include $template;

			return ob_get_clean();
		}

		/**
		 * Render the template
		 *
		 * @param string $file_name template file name.
		 * @param array  $variables variables that will be injected into template file.
		 * @param bool   $echo whether display as variable or display in browser.
		 *
		 * @return void|string
		 */
		public static function render( $file_name, $variables = [], $echo = false ) {
			$template = self::find_template( $file_name );
			$output   = '';
			if ( $template ) {
				$output = self::render_template( $template, $variables );
			}

			if ( $echo ) {
				echo $output; // phpcs:ignore
			} else {
				return $output;
			}
		}

		/**
		 * Set template folder
		 *
		 * @param bool|string $file file path.
		 */
		public static function override_folder( $file = false ) {
			$folder = WACARA_PATH . '/templates';

			if ( $file ) {
				$folder = plugin_dir_path( $file ) . '/templates';
			}

			if ( $folder ) {
				self::do_set_folder( $folder );
			}
		}

		/**
		 * Reset the template folder.
		 */
		public static function reset_folder() {
			self::do_set_folder( '' );
		}
	}
}
