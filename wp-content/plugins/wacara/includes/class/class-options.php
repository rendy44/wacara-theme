<?php
/**
 * Class to manage the options page.
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Options' ) ) {

	/**
	 * Class Options
	 *
	 * @package Wacara
	 */
	class Options {

		/**
		 * Instance variable.
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Global option key variable.
		 *
		 * @var string
		 */
		private $global_option_key;

		/**
		 * Variable to map options field.
		 *
		 * @var array
		 */
		private $options_fields = array();

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

			// Set global variable.
			$this->global_option_key = 'options';

			// Add options page.
			add_action( 'cmb2_admin_init', array( $this, 'options_page_callback' ) );

			// Maybe add options from payment methods.
			add_action( 'cmb2_admin_init', array( $this, 'maybe_payment_methods_options_page_callback' ) );
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

			// Validate options.
			if ( ! empty( $payment_methods ) ) {

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
			$this->options_fields = array(
				$this->global_option_key => array(
					'title'  => esc_html__( 'Wacara Options', 'wacara' ),
					'fields' => array(
						array(
							'name'         => __( 'Logo', 'wacara' ),
							'desc'         => __( 'Only file with .png extension is allowed', 'wacara' ),
							'id'           => 'logo',
							'type'         => 'file',
							'options'      => array(
								'url' => false,
							),
							'text'         => array(
								'add_upload_file_text' => __( 'Select Image', 'wacara' ),
							),
							'query_args'   => array(
								'type' => array(
									'image/png',
								),
							),
							'preview_size' => 'medium',
						),
					),
				),
			);
		}

		/**
		 * Create options page using cmb2 fields.
		 *
		 * @param string       $option_id the page id.
		 * @param string       $option_name the page title.
		 * @param array        $option_fields fields of page.
		 * @param string|false $parent_slug parent slug.
		 * @param string       $prefix prefix key for option name.
		 */
		private function register_option_page( $option_id, $option_name, $option_fields, $parent_slug = false, $prefix = WACARA_PREFIX ) {
			// Register custom option page.
			$option_args = array(
				'id'           => $prefix . $option_id,
				'title'        => $option_name,
				'object_types' => array( 'options-page' ),
				'option_key'   => $prefix . $option_id,
			);

			// Maybe set default parent slug.
			if ( false === $parent_slug ) {
				$parent_slug = $this->global_option_key;
			}

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
