<?php
/**
 * Simple helper class to render php file into output buffer
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Template' ) ) {

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
		private static function set_default_folder() {
			$folder = WACARA_PATH . '/templates';

			if ( ! self::$folder ) {

				self::do_set_folder( $folder );
			}
		}

		/**
		 * Maybe find template file in theme.
		 *
		 * @param string $file_name name of the template file.
		 *
		 * @return string
		 */
		private static function find_template_in_theme( $file_name ) {
			return locate_template( "templates/wacara/{$file_name}.php" );
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
			$found = false;

			// Maybe check template file in theme if it supports wacara.
			if ( current_theme_supports( 'wacara' ) ) {

				// Check template file in theme.
				$file_in_theme = self::find_template_in_theme( $file_name );
				if ( '' !== $file_in_theme ) {
					$found = $file_in_theme;
				}
			}

			// Find default file in plugin.
			if ( ! $found ) {

				// Set default folder.
				self::set_default_folder();

				// Check file in plugin.
				$file = self::$folder . "/{$file_name}.php";
				if ( file_exists( $file ) ) {
					$found = $file;
				}
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
		private static function render_template( $template, $variables = array() ) {
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
		public static function render( $file_name, $variables = array(), $echo = false ) {
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
		 * @param bool        $from_plugin whether override the template from plugin or theme.
		 */
		public static function override_folder( $file = false, $from_plugin = true ) {
			$folder = WACARA_PATH . '/templates';

			if ( $file ) {
				$folder  = $from_plugin ? plugin_dir_path( $file ) : WACARA_MAYBE_THEME_PATH;
				$folder .= '/templates';
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
