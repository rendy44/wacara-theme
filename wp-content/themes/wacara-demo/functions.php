<?php
/**
 * Main file functions.
 *
 * @author Rendy
 * @package Wacara_Theme
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define the constanta.
defined( 'WCR_THM_DIR' ) || define( 'WCR_THM_DIR', get_template_directory() );
defined( 'WCR_THM_URI' ) || define( 'WCR_THM_URI', get_template_directory_uri() );
defined( 'WCR_THM_PATH' ) || define( 'WCR_THM_PATH', get_theme_file_path() );

// Require our main class if the main class from plugin does exist.
if ( class_exists( 'Wacara\Wacara' ) ) {
	require_once WCR_THM_PATH . '/includes/class-wacara.php';
}
