<?php
/**
 * List of helpful functions.
 *
 * @author Rendy
 * @package Wacara_Theme
 * @version 0.0.1
 */

namespace Wacara_Theme;

use Wacara\Template;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara_Theme\Helper' ) ) {

	/**
	 * Class Helper
	 *
	 * @package Wacara_Theme
	 */
	class Helper {

		/**
		 * Extend function to render template from local theme.
		 *
		 * @param string $template name of the template.
		 * @param array  $args list of variables.
		 * @param bool   $echo whether render or save as variable.
		 *
		 * @return string|void
		 */
		public static function render_local_template( $template, $args = [], $echo = false ) {

			// Override the template folder.
			Template::override_folder( __FILE__, false );

			// Render the template.
			$result = Template::render( $template, $args );

			// Reset the template folder.
			Template::reset_folder();

			if ( $echo ) {
				echo $result; //phpcs:ignore
			} else {
				return $result;
			}
		}
	}
}
