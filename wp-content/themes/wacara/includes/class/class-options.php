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
		private static $meta_prefix = TEMP_PREFIX;

		/**
		 * Theme options variable.
		 *
		 * @var array
		 */
		private static $theme_options = [];

		/**
		 * Variable to map options field.
		 *
		 * @var array
		 */
		private $options_fields = [];

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

			// Add options page.
			add_action( 'cmb2_admin_init', [ $this, 'options_page_callback' ] );

			// Maybe add options from payment methods.
			add_action( 'cmb2_admin_init', [ $this, 'maybe_payment_methods_options_page_callback' ] );
		}

		/**
		 * Get options from db.
		 *
		 * @param string $key the options key.
		 *
		 * @return mixed|void
		 */
		public static function get_the_options( $key ) {
			return get_option( self::$meta_prefix . $key );
		}

		/**
		 * Get theme options.
		 *
		 * @return array
		 */
		private static function get_theme_options() {
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
			return ! empty( self::get_theme_options()[ $key ] ) ? self::get_theme_options()[ $key ] : false;
		}

		/**
		 * Callback for registering options page.
		 */
		public function options_page_callback() {

			// Map the fields.
			$this->map_options_page();

			foreach ( $this->options_fields as $options_field_id => $options_field_obj ) {
				// Prepare the options slug.
				$parent_slug = empty( $options_field_obj['parent'] ) ? '' : $options_field_obj['parent'];

				$this->register_option_page( $options_field_id, $options_field_obj['title'], $options_field_obj['fields'], $parent_slug );
			}
		}

		/**
		 * Maybe generate options page for payment methods.
		 */
		public function maybe_payment_methods_options_page_callback() {

			// Fetch all payment methods.
			$payment_methods = Register_Payment::get_registered( false );

			if ( $payment_methods ) {
				foreach ( $payment_methods as $payment_method ) {
					$payment_method_fields = $payment_method->admin_setting();

					// Check whether the payment method has option page or not.
					if ( $payment_methods ) {

						// Do register the option page.
						// translators: %s : name of payment method.
						$option_name = sprintf( __( '%s Setting', 'wacara' ), $payment_method->name );
						$this->register_option_page( $payment_method->id, $option_name, $payment_method_fields );
					}
				}
			}
		}

		/**
		 * Map options page's fields.
		 */
		private function map_options_page() {
			$this->options_fields = [
				'theme_options' => [
					'title'  => esc_html__( 'Wacara Options', 'wacara' ),
					'fields' => [
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
						],
					],
				],
			];
		}

		/**
		 * Create options page using cmb2 fields.
		 *
		 * @param string $option_id the page id.
		 * @param string $option_name the page title.
		 * @param array  $option_fields fields of page.
		 * @param string $parent_slug parent slug.
		 * @param string $prefix prefix key for option name.
		 */
		private function register_option_page( $option_id, $option_name, $option_fields, $parent_slug = 'theme_options', $prefix = TEMP_PREFIX ) {
			// Register custom option page.
			$option_args = [
				'id'           => $prefix . $option_id,
				'title'        => $option_name,
				'object_types' => [ 'options-page' ],
				'option_key'   => $prefix . $option_id,
			];

			// Check whether uses parent slug or not.
			if ( $parent_slug ) {
				$option_args['parent_slug'] = $prefix . $parent_slug;
			}

			// Instance the options.
			$option_obj = new_cmb2_box( $option_args );

			// Fetch the fields.
			if ( $option_fields ) {
				foreach ( $option_fields as $option_field ) {

					// Special treatment for grouped field.
					if ( 'group' === $option_field['type'] ) {

						// Store the field information.
						$group_fields = $option_field['fields'];

						// Drop the fields from grouping.
						unset( $option_field['fields'] );

						// Create the group.
						$group_field_id = $option_obj->add_field( $option_field );

						// Fetch the group fields.
						foreach ( $group_fields as $group_field ) {
							$option_obj->add_group_field( $group_field_id, $group_field );
						}
					} else {

						// Normal treatment :).
						$option_obj->add_field( $option_field );
					}
				}
			}
		}
	}

	Options::init();
}
