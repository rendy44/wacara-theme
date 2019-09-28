<?php
/**
 * Class to manage the options page.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Skeleton\Options' ) ) {
	/**
	 * Class Options
	 *
	 * @package Skeleton
	 */
	class Options {
		/**
		 * Instance variable.
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Prefix for metabox's fields
		 *
		 * @var string
		 */
		private $meta_prefix = TEMP_PREFIX;

		/**
		 * Theme options variable.
		 *
		 * @var array
		 */
		private static $theme_options = [];

		/**
		 * Stripe options variable.
		 *
		 * @var array
		 */
		private static $stripe_options = [];

		/**
		 * Singleton function.
		 *
		 * @return Options|null
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Options constructor.
		 */
		private function __construct() {
			$this->get_options();

			// Add options page.
			add_action( 'cmb2_admin_init', [ $this, 'options_page_callback' ] );
		}

		/**
		 * Load the options from database.
		 */
		private function get_options() {
			self::$stripe_options = get_option( $this->meta_prefix . 'stripe_options' );
			self::$theme_options  = get_option( $this->meta_prefix . 'theme_options' );
		}

		/**
		 * Get theme options.
		 *
		 * @return array
		 */
		public static function get_theme_options() {
			return self::$theme_options;
		}

		/**
		 * Get theme option setting.
		 *
		 * @param string $key theme key.
		 *
		 * @return bool|mixed
		 */
		public static function get_theme_option( $key ) {
			return ! empty( self::$theme_options[ $key ] ) ? self::$theme_options[ $key ] : false;
		}

		/**
		 * Get stripe options.
		 *
		 * @return array
		 */
		public static function get_stripe_options() {
			return self::$stripe_options;
		}

		/**
		 * Callback for registering options page.
		 */
		public function options_page_callback() {
			/**
			 * Registers main options page menu item and form.
			 */
			$theme_options = new_cmb2_box(
				[
					'id'           => $this->meta_prefix . 'theme_options',
					'title'        => esc_html__( 'Wacara Options', 'wacara' ),
					'object_types' => [ 'options-page' ],
					'option_key'   => $this->meta_prefix . 'theme_options',
				]
			);
			$theme_options->add_field(
				[
					'name'         => __( 'Logo', 'wacara' ),
					'desc'         => __( 'Only file with .png extension is allowed', 'wacara' ),
					'id'           => 'logo',
					'type'         => 'file',
					'options'      => [
						'url' => false,
					],
					'text'         => [
						'add_upload_file_text' => __( 'Select Image', 'wacara' ),
					],
					'query_args'   => [
						'type' => [
							'image/png',
						],
					],
					'preview_size' => 'medium',
				]
			);
			$stripe_options = new_cmb2_box(
				[
					'id'           => $this->meta_prefix . 'stripe_options',
					'title'        => esc_html__( 'Stripe Options', 'wacara' ),
					'object_types' => [ 'options-page' ],
					'option_key'   => $this->meta_prefix . 'stripe_options',
					'parent_slug'  => $this->meta_prefix . 'theme_options',
				]
			);
			$stripe_options->add_field(
				[
					'name' => __( 'Sandbox', 'wacara' ),
					'desc' => __( 'Enable sandbox for testing', 'wacara' ),
					'id'   => 'sandbox',
					'type' => 'checkbox',
				]
			);
			$stripe_options->add_field(
				[
					'name' => __( 'Sandbox secret key', 'wacara' ),
					'id'   => 'sandbox_secret_key',
					'type' => 'text',
					'desc' => __( 'Normally it something like this sk_test_xxx', 'wacara' ),
				]
			);
			$stripe_options->add_field(
				[
					'name' => __( 'Sandbox publishable key', 'wacara' ),
					'id'   => 'sandbox_publishable_key',
					'type' => 'text',
					'desc' => __( 'Normally it something like this pk_test_xxx', 'wacara' ),
				]
			);
			$stripe_options->add_field(
				[
					'name' => __( 'Live secret key', 'wacara' ),
					'id'   => 'live_secret_key',
					'type' => 'text',
					'desc' => __( 'Normally it something like this sk_live_xxx', 'wacara' ),
				]
			);
			$stripe_options->add_field(
				[
					'name' => __( 'Live publishable key', 'wacara' ),
					'id'   => 'live_publishable_key',
					'type' => 'text',
					'desc' => __( 'Normally it something like this pk_live_xxx', 'wacara' ),
				]
			);
		}
	}
}

Options::init();
