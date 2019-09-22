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
			// Add event schedule metabox.
			add_action( 'cmb2_admin_init', [ $this, 'schedule_event_metabox_callback' ] );
			// Add header detail metabox.
			add_action( 'cmb2_admin_init', [ $this, 'detail_header_metabox_callback' ] );
			// Add location detail metabox.
			add_action( 'cmb2_admin_init', [ $this, 'detail_location_metabox_callback' ] );
			// Add speaker detail metabox.
			add_action( 'cmb2_admin_init', [ $this, 'detail_speaker_metabox_callback' ] );
			// Add price detail metabox.
			add_action( 'cmb2_admin_init', [ $this, 'detail_price_metabox_callback' ] );
			// Add options page.
			add_action( 'cmb2_admin_init', [ $this, 'options_page_callback' ] );
		}

		/**
		 * Metabox configuration for event schedule
		 */
		public function schedule_event_metabox_callback() {
			$args           = [
				'id'           => 'schedule_event_metabox',
				'title'        => __( 'Schedule', 'wacara' ),
				'object_types' => [ 'event' ],
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			];
			$cmb2           = new_cmb2_box( $args );
			$group_field_id = $cmb2->add_field(
				[
					'id'         => $this->meta_prefix . 'schedules',
					'type'       => 'group',
					'repeatable' => true,
					'options'    => [
						'group_title'   => __( 'Schedule {#}', 'wacara' ),
						'add_button'    => __( 'Add schedule', 'wacara' ),
						'remove_button' => __( 'Remove schedule', 'wacara' ),
						'sortable'      => true,
					],
				]
			);
			$cmb2->add_group_field(
				$group_field_id,
				[
					'name' => __( 'Period', 'wacara' ),
					'id'   => 'period',
					'type' => 'text',
					'desc' => __( 'You can write year, date, clock, nor anything else', 'wacara' ),
				]
			);
			$cmb2->add_group_field(
				$group_field_id,
				[
					'name' => _x( 'Title', 'Title of schedule event', 'wacara' ),
					'id'   => 'title',
					'type' => 'text',
				]
			);
			$cmb2->add_group_field(
				$group_field_id,
				[
					'name' => _x( 'Content', 'Content of schedule event', 'wacara' ),
					'id'   => 'content',
					'type' => 'textarea_small',
				]
			);
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
						'id'     => 'event_basic_info',
						'title'  => _x( 'Basic', 'Tab metabox title', 'wacara' ),
						'fields' => [
							[
								'name'        => __( 'Date start', 'wacara' ),
								'id'          => $this->meta_prefix . 'date_start',
								'type'        => 'text_datetime_timestamp',
								'time_format' => Helper::get_time_format(),
							],
							[
								'name' => __( 'Single day', 'wacara' ),
								'id'   => $this->meta_prefix . 'single_day',
								'type' => 'checkbox',
								'desc' => __( 'Uncheck this if the event will take a place in multiple days', 'wacara' ),
							],
							[
								'name'        => __( 'Time end', 'wacara' ),
								'id'          => $this->meta_prefix . 'time_end',
								'type'        => 'text_time',
								'time_format' => Helper::get_time_format(),
								'attributes'  => [
									'data-conditional-id' => $this->meta_prefix . 'single_day',
								],
							],
							[
								'name'        => __( 'Date end', 'wacara' ),
								'id'          => $this->meta_prefix . 'date_end',
								'type'        => 'text_datetime_timestamp',
								'time_format' => Helper::get_time_format(),
								'attributes'  => [
									'data-conditional-id' => $this->meta_prefix . 'single_day',
									'data-conditional-value' => 'off',
								],
							],
							[
								'name'    => __( 'Location', 'wacara' ),
								'id'      => $this->meta_prefix . 'location',
								'type'    => 'select',
								'options' => Helper::get_list_of_locations(),
							],
							[
								'name' => __( 'Description', 'wacara' ),
								'id'   => $this->meta_prefix . 'description',
								'type' => 'textarea_small',
								'desc' => __( 'Describe the event in short sentence.', 'wacara' ),
							],
						],
					],
					[
						'id'     => 'event_detail_info',
						'title'  => __( 'Detail', 'wacara' ),
						'fields' => [
							[
								'name'    => __( 'Speakers', 'wacara' ),
								'id'      => $this->meta_prefix . 'speakers',
								'type'    => 'pw_multiselect',
								'options' => Helper::get_list_of_speakers(),
							],
							[
								'name'    => __( 'Pricing', 'wacara' ),
								'id'      => $this->meta_prefix . 'pricing',
								'type'    => 'pw_multiselect',
								'options' => Helper::get_list_of_prices(),
							],
						],
					],
					[
						'id'     => 'event_rule',
						'title'  => _x( 'Rules', 'Tab metabox title', 'wacara' ),
						'fields' => [
							[
								'name' => __( 'Allow register', 'wacara' ),
								'id'   => $this->meta_prefix . 'allow_register',
								'type' => 'checkbox',
								'desc' => __( 'Allow participant register this event', 'wacara' ),
							],
						],
					],
					[
						'id'     => 'event_fields_info',
						'title'  => _x( 'Fields', 'Tab metabox title', 'wacara' ),
						'fields' => [
							// Email field.
							[
								'name'       => __( 'Email', 'wacara' ),
								'id'         => $this->meta_prefix . 'email_dummy',
								'type'       => 'checkbox',
								'desc'       => __( 'Use email field', 'wacara' ),
								'default'    => 1,
								'classes'    => 'master-field',
								'attributes' => [
									'disabled' => 'disabled',
								],
							],
							[
								'name'       => __( 'Required', 'wacara' ),
								'id'         => $this->meta_prefix . 'email_required_dummy',
								'type'       => 'checkbox',
								'desc'       => __( 'Set as required field', 'wacara' ),
								'default'    => 1,
								'classes'    => 'mini-label',
								'attributes' => [
									'disabled' => 'disabled',
								],
							],
							[
								'name'    => __( 'Field name', 'wacara' ),
								'id'      => $this->meta_prefix . 'email_field_name',
								'type'    => 'text',
								'default' => __( 'Email address', 'wacara' ),
								'classes' => 'mini-label',
							],
							// Name field.
							[
								'name'       => __( 'Name', 'wacara' ),
								'id'         => $this->meta_prefix . 'name_dummy',
								'type'       => 'checkbox',
								'desc'       => __( 'Use name field', 'wacara' ),
								'default'    => 1,
								'classes'    => 'master-field',
								'attributes' => [
									'disabled' => 'disabled',
								],
							],
							[
								'name'       => __( 'Required', 'wacara' ),
								'id'         => $this->meta_prefix . 'name_required_dummy',
								'type'       => 'checkbox',
								'desc'       => __( 'Set as required field', 'wacara' ),
								'default'    => 1,
								'classes'    => 'mini-label',
								'attributes' => [
									'disabled' => 'disabled',
								],
							],
							[
								'name'    => __( 'Field name', 'wacara' ),
								'id'      => $this->meta_prefix . 'name_field_name',
								'type'    => 'text',
								'default' => __( 'Full name', 'wacara' ),
								'classes' => 'mini-label',
							],
							// Company field.
							[
								'name'    => __( 'Company', 'wacara' ),
								'id'      => $this->meta_prefix . 'company',
								'type'    => 'checkbox',
								'desc'    => __( 'Use company field', 'wacara' ),
								'classes' => 'master-field',
							],
							[
								'name'       => __( 'Required', 'wacara' ),
								'id'         => $this->meta_prefix . 'company_required',
								'type'       => 'checkbox',
								'desc'       => __( 'Set as required field', 'wacara' ),
								'classes'    => 'mini-label',
								'attributes' => [
									'data-conditional-id' => $this->meta_prefix . 'company',
								],
							],
							[
								'name'       => __( 'Field name', 'wacara' ),
								'id'         => $this->meta_prefix . 'company_field_name',
								'type'       => 'text',
								'default'    => __( 'Company', 'wacara' ),
								'classes'    => 'mini-label',
								'attributes' => [
									'data-conditional-id' => $this->meta_prefix . 'company',
								],
							],
							// Position field.
							[
								'name'    => __( 'Position', 'wacara' ),
								'id'      => $this->meta_prefix . 'position',
								'type'    => 'checkbox',
								'desc'    => __( 'Use position field', 'wacara' ),
								'classes' => 'master-field',
							],
							[
								'name'       => __( 'Required', 'wacara' ),
								'id'         => $this->meta_prefix . 'position_required',
								'type'       => 'checkbox',
								'desc'       => __( 'Set as required field', 'wacara' ),
								'classes'    => 'mini-label',
								'attributes' => [
									'data-conditional-id' => $this->meta_prefix . 'position',
								],
							],
							[
								'name'       => __( 'Field name', 'wacara' ),
								'id'         => $this->meta_prefix . 'position_field_name',
								'type'       => 'text',
								'default'    => __( 'Position', 'wacara' ),
								'classes'    => 'mini-label',
								'attributes' => [
									'data-conditional-id' => $this->meta_prefix . 'position',
								],
							],
							// ID number field.
							[
								'name'    => __( 'ID number', 'wacara' ),
								'id'      => $this->meta_prefix . 'id_number',
								'type'    => 'checkbox',
								'desc'    => __( 'Use id_number field', 'wacara' ),
								'classes' => 'master-field',
							],
							[
								'name'       => __( 'Required', 'wacara' ),
								'id'         => $this->meta_prefix . 'id_number_required',
								'type'       => 'checkbox',
								'desc'       => __( 'Set as required field', 'wacara' ),
								'classes'    => 'mini-label',
								'attributes' => [
									'data-conditional-id' => $this->meta_prefix . 'id_number',
								],
							],
							[
								'name'       => __( 'Field name', 'wacara' ),
								'id'         => $this->meta_prefix . 'id_number_field_name',
								'type'       => 'text',
								'default'    => __( 'ID number', 'wacara' ),
								'classes'    => 'mini-label',
								'attributes' => [
									'data-conditional-id' => $this->meta_prefix . 'id_number',
								],
							],
							// Phone field.
							[
								'name'    => __( 'Phone', 'wacara' ),
								'id'      => $this->meta_prefix . 'phone',
								'type'    => 'checkbox',
								'desc'    => __( 'Use phone field', 'wacara' ),
								'classes' => 'master-field',
							],
							[
								'name'       => __( 'Required', 'wacara' ),
								'id'         => $this->meta_prefix . 'phone_required',
								'type'       => 'checkbox',
								'desc'       => __( 'Set as required field', 'wacara' ),
								'classes'    => 'mini-label',
								'attributes' => [
									'data-conditional-id' => $this->meta_prefix . 'phone',
								],
							],
							[
								'name'       => __( 'Field name', 'wacara' ),
								'id'         => $this->meta_prefix . 'phone_field_name',
								'type'       => 'text',
								'default'    => __( 'Phone', 'wacara' ),
								'classes'    => 'mini-label',
								'attributes' => [
									'data-conditional-id' => $this->meta_prefix . 'phone',
								],
							],
						],
					],
					[
						'id'     => 'event_design',
						'title'  => _x( 'Design', 'Tab metabox title', 'wacara' ),
						'fields' => [
							[
								'name'         => __( 'Main logo', 'wacara' ),
								'id'           => $this->meta_prefix . 'main_logo',
								'type'         => 'file',
								'desc'         => __( 'Only image with .png extension is allowed', 'wacara' ),
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
							[
								'name'    => __( 'Header', 'wacara' ),
								'id'      => $this->meta_prefix . 'header',
								'type'    => 'select',
								'options' => Helper::get_list_of_headers(),
							],
						],
					],
				],
			];
			$cmb2->add_field(
				[
					'id'   => 'detail_event_tabs',
					'type' => 'tabs',
					'tabs' => $tabs,
				]
			);
			// Email and name will be set as statically to `on` by using hidden fields.
			// And on the ui it will be use different id only for ui.
			$cmb2->add_field(
				[
					'name'    => 'xyz',
					'id'      => $this->meta_prefix . 'email',
					'type'    => 'hidden',
					'default' => 'on',
				]
			);
			$cmb2->add_field(
				[
					'name'    => 'xyz',
					'id'      => $this->meta_prefix . 'email_required',
					'type'    => 'hidden',
					'default' => 'on',
				]
			);
			$cmb2->add_field(
				[
					'name'    => 'xyz',
					'id'      => $this->meta_prefix . 'name',
					'type'    => 'hidden',
					'default' => 'on',
				]
			);
			$cmb2->add_field(
				[
					'name'    => 'xyz',
					'id'      => $this->meta_prefix . 'name_required',
					'type'    => 'hidden',
					'default' => 'on',
				]
			);
		}

		/**
		 * Metabox configuration for detail of header.
		 */
		public function detail_header_metabox_callback() {
			$args = [
				'id'           => 'detail_header_metabox',
				'title'        => __( 'Detail', 'wacara' ),
				'object_types' => [ 'header' ],
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
						'id'     => 'header_layout',
						'title'  => __( 'Layout', 'wacara' ),
						'fields' => [
							[
								'name'    => __( 'Width', 'wacara' ),
								'id'      => $this->meta_prefix . 'content_width',
								'type'    => 'select',
								'desc'    => __( 'If you select half-width, please use transparent background image for the best result', 'wacara' ),
								'options' => [
									'center' => __( 'Full-width content', 'wacara' ),
									'half'   => __( 'Half-width content', 'wacara' ),
								],
							],
							[
								'name'       => __( 'Scheme', 'wacara' ),
								'id'         => $this->meta_prefix . 'color_scheme',
								'type'       => 'select',
								'options'    => [
									'light' => __( 'Light color', 'wacara' ),
									'dark'  => __( 'Dark color', 'wacara' ),
								],
								'attributes' => [
									'data-conditional-id' => $this->meta_prefix . 'content_width',
									'data-conditional-value' => 'center',
								],
							],
							[
								'name'    => __( 'Alignment', 'wacara' ),
								'id'      => $this->meta_prefix . 'content_alignment',
								'type'    => 'select',
								'options' => [
									'left'   => __( 'Left', 'wacara' ),
									'center' => __( 'Center', 'wacara' ),
									'right'  => __( 'Right', 'wacara' ),
								],
							],
						],
					],
					[
						'id'     => 'content_header',
						'title'  => __( 'Content', 'wacara' ),
						'fields' => [
							[
								'name'         => __( 'Default image', 'wacara' ),
								'desc'         => __( 'This image will be used as default background', 'wacara' ),
								'id'           => $this->meta_prefix . 'default_image',
								'type'         => 'file',
								'options'      => [
									'url' => false,
								],
								'text'         => [
									'add_upload_file_text' => __( 'Select image', 'wacara' ),
								],
								'query_args'   => [
									'type' => [
										'image/gif',
										'image/jpeg',
										'image/png',
									],
								],
								'preview_size' => 'large',
							],
							[
								'name' => __( 'Countdown', 'wacara' ),
								'id'   => $this->meta_prefix . 'countdown_content',
								'type' => 'checkbox',
								'desc' => __( 'Display countdown content', 'wacara' ),
							],
						],
					],
				],
			];
			$cmb2->add_field(
				[
					'id'   => 'information_header_tabs',
					'type' => 'tabs',
					'tabs' => $tabs,
				]
			);
		}

		/**
		 * Metabox configuration for detail of location.
		 */
		public function detail_location_metabox_callback() {
			$args = [
				'id'           => 'detail_location_metabox',
				'title'        => __( 'Detail', 'wacara' ),
				'object_types' => [ 'location' ],
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
						'id'     => 'information_location',
						'title'  => __( 'Detail', 'wacara' ),
						'fields' => [
							[
								'name' => __( 'Name', 'wacara' ),
								'id'   => $this->meta_prefix . 'name',
								'type' => 'text',
								'desc' => __( 'Use the real name of the location', 'wacara' ),
							],
							[
								'name'    => __( 'Country', 'wacara' ),
								'id'      => $this->meta_prefix . 'country',
								'type'    => 'select',
								'options' => Helper::get_list_of_countries(),
							],
							[
								'name' => __( 'Province/State', 'wacara' ),
								'id'   => $this->meta_prefix . 'province',
								'type' => 'text',
							],
							[
								'name' => __( 'City', 'wacara' ),
								'id'   => $this->meta_prefix . 'city',
								'type' => 'text',
							],
							[
								'name' => __( 'Address', 'wacara' ),
								'id'   => $this->meta_prefix . 'address',
								'type' => 'textarea_small',
								'desc' => __( 'Only short address, without city nor province/state name', 'wacara' ),
							],
							[
								'name' => __( 'Postal code', 'wacara' ),
								'id'   => $this->meta_prefix . 'postal',
								'type' => 'text_small',
							],
						],
					],
					[
						'id'     => 'photo_location',
						'title'  => __( 'Photo & Description', 'wacara' ),
						'fields' => [
							[
								'name'         => __( 'Photo', 'wacara' ),
								'desc'         => __( 'Add any pictures related to the location', 'wacara' ),
								'id'           => $this->meta_prefix . 'photo',
								'type'         => 'file_list',
								'query_args'   => [ 'type' => 'image' ],
								'preview_size' => [ 100, 100 ],
								'text'         => [
									'add_upload_files_text' => __( 'Add Images', 'wacara' ),
									'remove_image_text'  => __( 'Remove Images', 'wacara' ),
									'file_text'          => __( 'Image:', 'wacara' ),
									'file_download_text' => __( 'Download', 'wacara' ),
									'remove_text'        => __( 'Remove', 'wacara' ),
								],
							],
							[
								'name' => __( 'Description', 'wacara' ),
								'id'   => $this->meta_prefix . 'description',
								'type' => 'textarea_small',
								'desc' => __( 'Write it short and representative', 'wacara' ),
							],
						],
					],
				],
			];
			$cmb2->add_field(
				[
					'id'   => 'information_location_tabs',
					'type' => 'tabs',
					'tabs' => $tabs,
				]
			);
		}

		/**
		 * Metabox configuration for detail of speaker.
		 */
		public function detail_speaker_metabox_callback() {
			$args = [
				'id'           => 'detail_speaker_metabox',
				'title'        => __( 'Detail', 'wacara' ),
				'object_types' => [ 'speaker' ],
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
						'id'     => 'information_speaker',
						'title'  => __( 'Detail', 'wacara' ),
						'fields' => [
							[
								'name' => __( 'Position', 'wacara' ),
								'id'   => $this->meta_prefix . 'position',
								'type' => 'text',
								'desc' => __( 'Ex: CEO at random company', 'wacara' ),
							],
						],
					],
					[
						'id'     => 'links_speaker',
						'title'  => __( 'Social Networks', 'wacara' ),
						'fields' => [
							[
								'name'      => __( 'Website URL', 'wacara' ),
								'id'        => $this->meta_prefix . 'website',
								'type'      => 'text_url',
								'protocols' => [
									'http',
									'https',
								],
							],
							[
								'name'      => __( 'Facebook URL', 'wacara' ),
								'id'        => $this->meta_prefix . 'facebook',
								'type'      => 'text_url',
								'protocols' => [
									'http',
									'https',
								],
							],
							[
								'name'      => __( 'Instagram URL', 'wacara' ),
								'id'        => $this->meta_prefix . 'instagram',
								'type'      => 'text_url',
								'protocols' => [
									'http',
									'https',
								],
							],
							[
								'name'      => __( 'Youtube URL', 'wacara' ),
								'id'        => $this->meta_prefix . 'youtube',
								'type'      => 'text_url',
								'protocols' => [
									'http',
									'https',
								],
							],
							[
								'name'      => __( 'Twitter URL', 'wacara' ),
								'id'        => $this->meta_prefix . 'twitter',
								'type'      => 'text_url',
								'protocols' => [
									'http',
									'https',
								],
							],
							[
								'name'      => __( 'Linkedin URL', 'wacara' ),
								'id'        => $this->meta_prefix . 'linkedin',
								'type'      => 'text_url',
								'protocols' => [
									'http',
									'https',
								],
							],
						],
					],
				],
			];
			$cmb2->add_field(
				[
					'id'   => 'information_speaker_tabs',
					'type' => 'tabs',
					'tabs' => $tabs,
				]
			);
		}

		/**
		 * Metabox configuration for detail of price.
		 */
		public function detail_price_metabox_callback() {
			$args = [
				'id'           => 'detail_price_metabox',
				'title'        => __( 'Detail', 'wacara' ),
				'object_types' => [ 'price' ],
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
						'id'     => 'information_price',
						'title'  => __( 'Detail', 'wacara' ),
						'fields' => [
							[
								'name'    => __( 'Currency', 'wacara' ),
								'id'      => $this->meta_prefix . 'currency',
								'type'    => 'select',
								'options' => [
									'USD' => 'USD',
									'AUD' => 'AUD',
									'SGD' => 'SGD',
									'IDR' => 'IDR',
									'MYR' => 'MYR',
									'JPY' => 'JPY',
									'EUR' => 'EUR',
									'GBP' => 'GBP',
								],
							],
							[
								'name'    => __( 'Price' ),
								'id'      => $this->meta_prefix . 'price',
								'type'    => 'text',
								'classes' => 'number-only-field',
								'desc'    => __( 'Only absolute number is allowed', 'wacara' ),
								'default' => 00,
							],
						],
					],
					[
						'id'     => 'features_price',
						'title'  => __( 'Features', 'wacara' ),
						'fields' => [
							[
								'name'    => __( 'Pros', 'wacara' ),
								'id'      => $this->meta_prefix . 'pros',
								'type'    => 'text',
								'classes' => 'inputosaurus-field',
								'desc'    => __( 'What user will get, use coma to separate the values', 'wacara' ),
							],
							[
								'name'    => __( 'Cons', 'wacara' ),
								'id'      => $this->meta_prefix . 'cons',
								'type'    => 'text',
								'classes' => 'inputosaurus-field',
								'desc'    => __( 'What user will not get, use coma to separate the values', 'wacara' ),
							],
						],
					],
				],
			];
			$cmb2->add_field(
				[
					'id'   => 'information_price_tabs',
					'type' => 'tabs',
					'tabs' => $tabs,
				]
			);
		}

		/**
		 * Callback for registering options page.
		 */
		public function options_page_callback() {
			/**
			 * Registers main options page menu item and form.
			 */
			$main_options = new_cmb2_box(
				[
					'id'           => $this->meta_prefix . 'tripe_options',
					'title'        => esc_html__( 'Stripe Options', 'wacara' ),
					'object_types' => [ 'options-page' ],
					'option_key'   => $this->meta_prefix . 'stripe_options',
				]
			);
			$main_options->add_field(
				[
					'name' => __( 'Sandbox', 'wacara' ),
					'desc' => __( 'Enable sandbox for testing', 'wacara' ),
					'id'   => 'sandbox',
					'type' => 'checkbox',
				]
			);
			$main_options->add_field(
				[
					'name' => __( 'Sandbox secret key', 'wacara' ),
					'id'   => 'sandbox_secret_key',
					'type' => 'text',
					'desc' => __( 'Normally it something like this sk_test_xxx', 'wacara' ),
				]
			);
			$main_options->add_field(
				[
					'name' => __( 'Sandbox publishable key', 'wacara' ),
					'id'   => 'sandbox_publishable_key',
					'type' => 'text',
					'desc' => __( 'Normally it something like this pk_test_xxx', 'wacara' ),
				]
			);
			$main_options->add_field(
				[
					'name' => __( 'Live secret key', 'wacara' ),
					'id'   => 'live_secret_key',
					'type' => 'text',
					'desc' => __( 'Normally it something like this sk_live_xxx', 'wacara' ),
				]
			);
			$main_options->add_field(
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

Metabox::init();
