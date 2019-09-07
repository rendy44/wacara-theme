<?php
/**
 * Use this class to config metaboxes and its custom fields using CMB2 library
 * Please refer to CMB2 official documentation for further details
 *
 * @author  Rendy
 * @package Wacara
 * @see     https://github.com/CMB2/CMB2/wiki
 */

namespace Skeleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Skeleton\Metabox' ) ) {
	/**
	 * Class Metabox
	 *
	 * @package Skeleton
	 */
	class Metabox {

		/**
		 * Instance variable
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
		 * Singleton
		 *
		 * @return null|Metabox
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Metabox constructor.
		 */
		private function __construct() {
			// Add event detail metabox.
			add_action( 'cmb2_admin_init', [ $this, 'detail_event_metabox_callback' ] );
		}

		/**
		 * Metabox configuration for detail of event.
		 */
		public function detail_event_metabox_callback() {
			$args = [
				'id'           => 'detail_event_metabox',
				'title'        => __( 'Detail', 'wacara' ),
				'object_types' => [ 'event' ],
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			];
			$cmb2 = new_cmb2_box( $args );
			$tabs = [
				'config' => $args,
				'layout' => 'vertical',
				'tabs'   => [
					[
						'id'     => 'basic_info',
						'title'  => __( 'Basic Information', 'wacara' ),
						'fields' => [
							[
								'name' => __( 'Date start', 'wacara' ),
								'id'   => $this->meta_prefix . 'date_start',
								'type' => 'text_datetime_timestamp',
							],
							[
								'name'    => __( 'Single day', 'wacara' ),
								'id'      => $this->meta_prefix . 'single_day',
								'type'    => 'checkbox',
								'desc'    => __( 'Uncheck this if the event will take a place in multiple days', 'wacara' ),
								'default' => 1,
							],
							[
								'name'       => __( 'Date end', 'wacara' ),
								'id'         => $this->meta_prefix . 'date_end',
								'type'       => 'text_datetime_timestamp',
								'attributes' => [
									'data-conditional-id'    => $this->meta_prefix . 'single_day',
									'data-conditional-value' => 'off',
								],
							],
							[
								'name' => __( 'Location', 'wacara' ),
								'id'   => $this->meta_prefix . 'location',
								'type' => 'text',
								'desc' => __( 'Write the place name or its address', 'wacara' ),
							],
						],
					],
				],
			];
			$cmb2->add_field(
				[
					'id'   => 'detail_event__tabs',
					'type' => 'tabs',
					'tabs' => $tabs,
				]
			);
		}
	}
}

Metabox::init();
