<?php
/**
 * Use this class to config metaboxes and its custom fields using CMB2 library
 * Please refer to CMB2 official documentation for further details
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 * @see     https://github.com/CMB2/CMB2/wiki
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Metabox' ) ) {

	/**
	 * Class Metabox
	 *
	 * @package Wacara
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
		private $meta_prefix = WACARA_PREFIX;

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

			// Add event design metabox.
			add_action( 'cmb2_admin_init', [ $this, 'design_event_metabox_callback' ] );

			// Add event custom metabox.
			add_action( 'add_meta_boxes', [ $this, 'custom_event_metabox_callback' ] );

			// Add registrant custom metabox.
			add_action( 'add_meta_boxes', [ $this, 'custom_registrant_metabox_callback' ] );

			// Add header detail metabox.
			add_action( 'cmb2_admin_init', [ $this, 'detail_header_metabox_callback' ] );

			// Add location detail metabox.
			add_action( 'cmb2_admin_init', [ $this, 'detail_location_metabox_callback' ] );

			// Add speaker detail metabox.
			add_action( 'cmb2_admin_init', [ $this, 'detail_speaker_metabox_callback' ] );

			// Add price detail metabox.
			add_action( 'cmb2_admin_init', [ $this, 'detail_price_metabox_callback' ] );
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
		 * Callback for adding custom metabox in event.
		 */
		public function custom_event_metabox_callback() {

			// Download registrants.
			add_meta_box(
				'event_registrant_mb',
				__( 'Registrant', 'wacara' ),
				[ $this, 'event_registrant_metabox_callback' ],
				'event',
				'side'
			);
		}

		/**
		 * Callback for displaying event registrant metabox.
		 */
		public function event_registrant_metabox_callback() {
			global $post;

			// Instance event.
			$event = new Event( $post->ID );

			// Validate the event.
			if ( $event->success ) {

				if ( $event->is_event_allows_register() ) {
					$base_url         = admin_url( 'admin-post.php' );
					$download_csv_url = add_query_arg(
						[
							'action'   => 'download_csv',
							'event_id' => $event->post_id,
						],
						$base_url
					);
					?>
					<p><?php esc_html_e( 'Click link below to download all registrants who already completed the registration', 'wacara' ); ?></p>
					<a href="<?php echo esc_attr( $download_csv_url ); ?>" class="button"><?php esc_html_e( 'Download', 'wacara' ); ?></a>
					<?php
				} else {
					?>
					<p><?php esc_html_e( 'This event does not require registration, so you can not collect any registrant data', 'wacara' ); ?></p>
					<?php
				}
			}
		}

		/**
		 * Callback for adding custom metabox in registrant.
		 */
		public function custom_registrant_metabox_callback() {

			// Logs.
			add_meta_box(
				'registrant_logs_mb',
				__( 'Logs', 'wacara' ),
				[ $this, 'registrant_logs_metabox_callback' ],
				'registrant',
				'side'
			);

			// Detail.
			add_meta_box(
				'registrant_detail_mb',
				__( 'Detail', 'wacara' ),
				[ $this, 'registrant_detail_metabox_callback' ],
				'registrant',
				'normal',
				'high'
			);
		}

		/**
		 * Callback for displaying registrant logs metabox.
		 */
		public function registrant_logs_metabox_callback() {
			global $post;

			// Instance registrant.
			$registrant = new Registrant( $post->ID );

			// Validate the registrant.
			if ( $registrant->success ) {

				// Fetch all logs.
				$logs = $registrant->get_logs();

				// Reverse the array.
				$logs = array_reverse( $logs );
				?>
				<div class="wcr-registrant-logs-wrapper">
					<ul class="wcr-registrant-logs">
						<?php foreach ( $logs as $log ) { ?>
							<li class="wcr-registrant-log">
								<p class="wcr-registrant-log-content"><?php echo esc_html( strtolower( $log['content'] ) ); ?></p>
								<div class="wcr-registrant-log-date-wrapper">
									<?php /* translators: %s : readable date time */ ?>
									<span class="wcr-registrant-log-date"><?php echo esc_html( sprintf( __( 'added on %s', 'wacara' ), Helper::convert_date( $log['time'], true ) ) ); ?></span>
								</div>
							</li>
						<?php } ?>
					</ul>
				</div>
				<?php
			}
		}

		/**
		 * Callback for displaying registrant detail metabox.
		 */
		public function registrant_detail_metabox_callback() {
			global $post_id;

			// Instance the registrant.
			$registrant = new Registrant( $post_id );

			// Validate the registrant.
			if ( ! $registrant->success ) {
				return;
			}

			// Instance the event.
			$event = $registrant->get_event_object();

			?>
			<div class="wcr-registrant-highlight-wrapper">
				<?php /* translators: %s : title of the registrant */ ?>
				<h3 class="wcr-registrant-highlight"><?php echo esc_html( sprintf( __( 'Registrant #%s details', 'wacara' ), $registrant->post_title ) ); ?></h3>
				<p class="wcr-registrant-subhighlight"><?php echo esc_html( $registrant->get_admin_highlight() ); ?></p>
			</div>

			<?php
			// Make sure registrant already filled a detail.
			if ( '' !== $registrant->get_registration_status() ) {

				// General details.
				$general_details = [
					[
						'field' => __( 'Registered', 'wacara' ),
						'value' => $registrant->get_created_date(),
					],
					[
						'field' => __( 'Status', 'wacara' ),
						'value' => $registrant->get_readable_registrant_status( true ),
					],
					[
						'field' => __( 'Registrant', 'wacara' ),
						/* translators: %1$s : registrant name, %2$s : registrant email */
						'value' => sprintf( '%1$s (<a href="mailto:%2$s">%2$s</a>)', $registrant->get_registrant_name(), $registrant->get_registrant_email() ),
					],
					[
						'field' => __( 'Booking code', 'wacara' ),
						'value' => $registrant->get_booking_code(),
					],
					[
						'field' => __( 'Event', 'wacara' ),
						/* translators: %1$s : event edit post link, %2$s : event name, %3$s : event landing page url */
						'value' => sprintf( '<a href="%1$s">%2$s</a> <a href="%3$s" target="_blank"><sup>[?]</sup></a>', get_edit_post_link( $event->post_id ), $event->post_title, $event->post_url ),
					],
				];

				// Content for invoice.
				$package_details = [
					[
						/* translators: %1$s : admin edit url of the pricing, %2$s : name of the pricing */
						'field' => sprintf( '<a href="%1$s">%2$s</a>', get_edit_post_link( $registrant->get_pricing_id() ), $registrant->get_pricing_name() ),
						'value' => number_format_i18n( $registrant->get_pricing_price_in_cent() / 100, 2 ),
					],
				];

				// Maybe add unique number.
				if ( $registrant->get_pricing_unique_number() ) {
					$package_details[] = [
						'field' => __( 'Unique number', 'wacara' ),
						'value' => number_format_i18n( $registrant->get_pricing_unique_number() / 100, 2 ),
					];
				}

				// Calculate total.
				$package_details[] = [
					'field' => __( 'Total', 'wacara' ),
					'value' => $registrant->get_total_pricing_in_html(),
				];

				?>
				<div class="frow">
					<div class="col-xs-1-1 col-sm-1-2 column-left">
						<div class="wcr-registrant-detail-title-wrapper">
							<h4 class="wcr-registrant-detail-title"><?php esc_html_e( 'General', 'wacara' ); ?></h4>
						</div>
						<div class="wcr-registrant-info-wrapper">
							<?php foreach ( $general_details as $detail ) { ?>
								<div class="wcr-registrant-info-detail-wrapper">
									<div class="frow">
										<div class="col-xs-1-3">
											<label class="wcr-registrant-detail-label"><?php echo esc_html( $detail['field'] ); ?></label>
										</div>
										<div class="col-xs-2-3">
                                            <p class="wcr-registrant-detail-value"><?php echo $detail['value']; // phpcs:ignore ?></p>
										</div>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
					<div class="col-xs-1-1 col-sm-1-2">
						<div class="wcr-registrant-detail-title-wrapper">
							<h4 class="wcr-registrant-detail-title"><?php esc_html_e( 'Invoice', 'wacara' ); ?></h4>
						</div>
						<div class="wcr-registrant-invoice-wrapper">
							<?php foreach ( $package_details as $detail ) { ?>
								<div class="wcr-registrant-invoice-detail-wrapper">
									<div class="frow">
										<div class="col-xs-2-3">
											<?php echo $detail['field']; // phpcs:ignore ?>
										</div>
										<div class="col-xs-1-3">
											<?php echo $detail['value']; // phpcs:ignore ?>
										</div>
									</div>
								</div>
							<?php } ?>
						</div>
						<div class="wcr-registrant-invoice-alert-wrapper">
							<p class="wcr-registrant-invoice-alert"><?php esc_html_e( 'This is cached detail of the pricing, the live version may vary.', 'wacara' ); ?></p>
						</div>
					</div>
				</div>
				<?php
			} else {
				?>
				<div class="wcr-registrant-empty">
					<p><?php esc_html_e( 'Nothing can be shown right now', 'wacara' ); ?></p>
				</div>
				<?php
			}
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
						'id'     => 'event_general_info',
						'title'  => _x( 'General', 'Tab metabox title', 'wacara' ),
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
								'desc' => __( 'Allow registrant register this event', 'wacara' ),
							],
							[
								'name'       => __( 'No registration message', 'wacara' ),
								'id'         => $this->meta_prefix . 'no_registration_message',
								'type'       => 'textarea_small',
								'attributes' => [
									'data-conditional-id' => $this->meta_prefix . 'allow_register',
									'data-conditional-value' => 'off',
								],
							],
							[
								'name'       => __( 'Limit register', 'wacara' ),
								'id'         => $this->meta_prefix . 'limit_register',
								'type'       => 'checkbox',
								'desc'       => __( 'Limit the registration by number of registrant or by date', 'wacara' ),
								'attributes' => [
									'data-conditional-id' => $this->meta_prefix . 'allow_register',
								],
							],
							[
								'name'       => __( 'By registrant', 'wacara' ),
								'id'         => $this->meta_prefix . 'max_registrant',
								'type'       => 'text',
								'attributes' => [
									'data-conditional-id' => $this->meta_prefix . 'limit_register',
								],
							],
							[
								'name'        => __( 'By date', 'wacara' ),
								'id'          => $this->meta_prefix . 'max_date',
								'type'        => 'text_date_timestamp',
								'time_format' => Helper::get_time_format(),
								'attributes'  => [
									'data-conditional-id' => $this->meta_prefix . 'limit_register',
								],
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
						],
					],
				],
			];

			/**
			 * Wacara event detail metabox tabs args filter hook.
			 *
			 * @param array $tabs current tab args.
			 */
			$tabs = apply_filters( 'wacara_filter_event_detail_metabox_tabs_args', $tabs );

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
								'preview_size' => 'medium',
							],
							[
								'name' => __( 'Darken', 'wacara' ),
								'id'   => $this->meta_prefix . 'darken',
								'type' => 'checkbox',
								'desc' => __( 'Darken background image', 'wacara' ),
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

			/**
			 * Wacara header detail metabox tabs args filter hook.
			 *
			 * @param array $tabs current tab args.
			 */
			$tabs = apply_filters( 'wacara_filter_header_detail_metabox_tabs_args', $tabs );

			$cmb2->add_field(
				[
					'id'   => 'information_header_tabs',
					'type' => 'tabs',
					'tabs' => $tabs,
				]
			);
		}

		/**
		 * Metabox configuration for design event.
		 */
		public function design_event_metabox_callback() {
			$args = [
				'id'           => 'design_event_metabox',
				'title'        => __( 'Design', 'wacara' ),
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
						'id'     => 'event_general_design',
						'title'  => _x( 'General', 'Tab metabox title', 'wacara' ),
						'fields' => [
							[
								'name' => __( 'Headline', 'wacara' ),
								'id'   => $this->meta_prefix . 'headline',
								'type' => 'text',
								'desc' => __( 'Give a short and eye-catching headline', 'wacara' ),
							],
							[
								'name'    => __( 'Section order', 'wacara' ),
								'id'      => $this->meta_prefix . 'section_order',
								'type'    => 'pw_multiselect',
								'options' => [
									'about'    => __( 'About section', 'wacara' ),
									'speakers' => __( 'Speakers section', 'wacara' ),
									'location' => __( 'Location section', 'wacara' ),
									'gallery'  => __( 'Gallery section', 'wacara' ),
									'sponsors' => __( 'Sponsors section', 'wacara' ),
									'schedule' => __( 'Schedule section', 'wacara' ),
									'pricing'  => __( 'Pricing section', 'wacara' ),
								],
								'desc'    => __( 'Drag item to reorder the section', 'wacara' ),
								'default' => [
									'about',
									'speakers',
									'location',
									'gallery',
									'sponsors',
									'schedule',
									'pricing',
								],
							],
							[
								'name'    => __( 'Header', 'wacara' ),
								'id'      => $this->meta_prefix . 'header',
								'type'    => 'select',
								'options' => Master::get_list_of_headers(),
							],
							[
								'name'         => __( 'Background', 'wacara' ),
								'desc'         => __( 'If you leave this empty, default background will be applied from header setting', 'wacara' ),
								'id'           => $this->meta_prefix . 'background_image',
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
								'preview_size' => 'medium',
							],
						],
					],
					[
						'id'     => 'event_about_design',
						'title'  => _x( 'About section', 'Tab metabox title', 'wacara' ),
						'fields' => [
							[
								'name' => __( 'Title', 'wacara' ),
								'id'   => $this->meta_prefix . 'about_title',
								'type' => 'text',
							],
							[
								'name' => __( 'Subtitle', 'wacara' ),
								'id'   => $this->meta_prefix . 'about_subtitle',
								'type' => 'text',
							],
							[
								'name' => __( 'Description', 'wacara' ),
								'id'   => $this->meta_prefix . 'about_description',
								'type' => 'textarea_small',
							],
						],
					],
					[
						'id'     => 'event_speakers_design',
						'title'  => _x( 'Speakers section', 'Tab metabox title', 'wacara' ),
						'fields' => [
							[
								'name' => __( 'Title', 'wacara' ),
								'id'   => $this->meta_prefix . 'speakers_title',
								'type' => 'text',
							],
							[
								'name' => __( 'Subtitle', 'wacara' ),
								'id'   => $this->meta_prefix . 'speakers_subtitle',
								'type' => 'text',
							],
							[
								'name' => __( 'Description', 'wacara' ),
								'id'   => $this->meta_prefix . 'speakers_description',
								'type' => 'textarea_small',
							],
							[
								'name'    => __( 'Speakers', 'wacara' ),
								'id'      => $this->meta_prefix . 'speakers',
								'type'    => 'pw_multiselect',
								'options' => Master::get_list_of_speakers(),
							],
						],
					],
					[
						'id'     => 'event_location_design',
						'title'  => _x( 'Location section', 'Tab metabox title', 'wacara' ),
						'fields' => [
							[
								'name' => __( 'Title', 'wacara' ),
								'id'   => $this->meta_prefix . 'location_title',
								'type' => 'text',
							],
							[
								'name' => __( 'Subtitle', 'wacara' ),
								'id'   => $this->meta_prefix . 'location_subtitle',
								'type' => 'text',
							],
							[
								'name' => __( 'Description', 'wacara' ),
								'id'   => $this->meta_prefix . 'location_description',
								'type' => 'textarea_small',
							],
							[
								'name'    => __( 'Location', 'wacara' ),
								'id'      => $this->meta_prefix . 'location',
								'type'    => 'select',
								'options' => Master::get_list_of_locations(),
							],
						],
					],
					[
						'id'     => 'event_gallery_design',
						'title'  => _x( 'Gallery section', 'Tab metabox title', 'wacara' ),
						'fields' => [
							[
								'name' => __( 'Title', 'wacara' ),
								'id'   => $this->meta_prefix . 'gallery_title',
								'type' => 'text',
							],
							[
								'name' => __( 'Subtitle', 'wacara' ),
								'id'   => $this->meta_prefix . 'gallery_subtitle',
								'type' => 'text',
							],
							[
								'name' => __( 'Description', 'wacara' ),
								'id'   => $this->meta_prefix . 'gallery_description',
								'type' => 'textarea_small',
							],
							[
								'name'         => __( 'Gallery', 'wacara' ),
								'id'           => $this->meta_prefix . 'gallery',
								'type'         => 'file_list',
								'preview_size' => [ 100, 100 ],
								'query_args'   => [ 'type' => 'image' ],
							],
						],
					],
					[
						'id'     => 'event_sponsors_design',
						'title'  => _x( 'Sponsors section', 'Tab metabox title', 'wacara' ),
						'fields' => [
							[
								'name' => __( 'Title', 'wacara' ),
								'id'   => $this->meta_prefix . 'sponsors_title',
								'type' => 'text',
							],
							[
								'name' => __( 'Subtitle', 'wacara' ),
								'id'   => $this->meta_prefix . 'sponsors_subtitle',
								'type' => 'text',
							],
							[
								'name' => __( 'Description', 'wacara' ),
								'id'   => $this->meta_prefix . 'sponsors_description',
								'type' => 'textarea_small',
							],
							[
								'name'         => __( 'Sponsors', 'wacara' ),
								'id'           => $this->meta_prefix . 'sponsors',
								'type'         => 'file_list',
								'preview_size' => [ 100, 100 ],
								'query_args'   => [ 'type' => 'image' ],
							],
						],
					],
					[
						'id'     => 'event_schedule_design',
						'title'  => _x( 'Schedule section', 'Tab metabox title', 'wacara' ),
						'fields' => [
							[
								'name' => __( 'Title', 'wacara' ),
								'id'   => $this->meta_prefix . 'schedule_title',
								'type' => 'text',
							],
							[
								'name' => __( 'Subtitle', 'wacara' ),
								'id'   => $this->meta_prefix . 'schedule_subtitle',
								'type' => 'text',
							],
							[
								'name' => __( 'Description', 'wacara' ),
								'id'   => $this->meta_prefix . 'schedule_description',
								'type' => 'textarea_small',
							],
						],
					],
					[
						'id'     => 'event_pricing_design',
						'title'  => _x( 'Pricing section', 'Tab metabox title', 'wacara' ),
						'fields' => [
							[
								'name' => __( 'Title', 'wacara' ),
								'id'   => $this->meta_prefix . 'pricing_title',
								'type' => 'text',
							],
							[
								'name' => __( 'Subtitle', 'wacara' ),
								'id'   => $this->meta_prefix . 'pricing_subtitle',
								'type' => 'text',
							],
							[
								'name' => __( 'Description', 'wacara' ),
								'id'   => $this->meta_prefix . 'pricing_description',
								'type' => 'textarea_small',
							],
							[
								'name'    => __( 'Pricing', 'wacara' ),
								'id'      => $this->meta_prefix . 'pricing',
								'type'    => 'pw_multiselect',
								'options' => Master::get_list_of_prices(),
							],
						],
					],
				],
			];

			/**
			 * Wacara event design metabox tabs args filter hook.
			 *
			 * @param array $tabs current tab args.
			 */
			$tabs = apply_filters( 'wacara_filter_event_design_metabox_tabs_args', $tabs );

			$cmb2->add_field(
				[
					'id'   => 'design_event_tabs',
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
								'options' => Master::get_list_of_countries(),
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
								'preview_size' => 'medium',
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

			/**
			 * Wacara location detail metabox tabs args filter hook.
			 *
			 * @param array $tabs current tab args.
			 */
			$tabs = apply_filters( 'wacara_filter_location_detail_metabox_tabs_args', $tabs );

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

			/**
			 * Wacara speaker detail metabox tabs args filter hook.
			 *
			 * @param array $tabs current tab args.
			 */
			$tabs = apply_filters( 'wacara_filter_speaker_detail_metabox_tabs_args', $tabs );

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
					[
						'id'     => 'options_price',
						'title'  => __( 'Options', 'wacara' ),
						'fields' => [
							[
								'name' => __( 'Unique number', 'wacara' ),
								'id'   => $this->meta_prefix . 'unique_number',
								'type' => 'checkbox',
								'desc' => __( 'Enable unique number', 'wacara' ),
							],
							[
								'name' => __( 'Recommended', 'wacara' ),
								'id'   => $this->meta_prefix . 'recommended',
								'type' => 'checkbox',
								'desc' => __( 'Set as recommended pricing', 'wacara' ),
							],
						],
					],
				],
			];

			/**
			 * Wacara price detail metabox tabs args filter hook.
			 *
			 * @param array $tabs current tab args.
			 */
			$tabs = apply_filters( 'wacara_filter_price_detail_metabox_tabs_args', $tabs );

			$cmb2->add_field(
				[
					'id'   => 'information_price_tabs',
					'type' => 'tabs',
					'tabs' => $tabs,
				]
			);
		}
	}

	Metabox::init();
}
