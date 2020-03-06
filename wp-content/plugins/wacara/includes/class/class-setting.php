<?php
/**
 * Use this class to add some configuration to override WordPress default behaviors
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

use WP_Post;
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Setting' ) ) {

	/**
	 * Class Setting
	 *
	 * @package Wacara
	 */
	class Setting {

		/**
		 * Instance variable
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Singleton
		 *
		 * @return null|Setting
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Setting constructor.
		 */
		private function __construct() {
			// Override single post template.
			add_filter( 'single_template', [ $this, 'override_single_post_callback' ], 10, 3 );

			// Add custom image size.
			add_image_size( 'wacara-location-image', 570, 300, true );

			// Manage post row actions.
			add_filter( 'post_row_actions', [ $this, 'manage_post_row_actions_callback' ], 10, 2 );

			// Manage post filters.
			add_action( 'restrict_manage_posts', [ $this, 'manage_post_filters_callback' ], 10, 2 );

			// Remove bulk actions in registrant.
			add_filter( 'bulk_actions-edit-registrant', [ $this, 'bulk_actions_callback' ], 10, 1 );

			// Manage post filter before displayed.
			add_action( 'pre_get_posts', [ $this, 'post_format_filter_to_posts_callback' ], 10, 1 );

			// Remove metabox in registrant post types.
			add_action( 'admin_menu', [ $this, 'remove_registrant_publish_metabox_callback' ], 10, 1 );
		}

		/**
		 * Override custom template
		 *
		 * @param string $template Path to the template. See locate_template().
		 * @param string $type Sanitized filename without extension.
		 * @param array  $templates A list of template candidates, in descending order of priority.
		 *
		 * @return string
		 */
		public function override_single_post_callback( $template, $type, $templates ) {
			global $post;

			$used_post_types   = [ 'event', 'registrant' ];
			$current_post_type = $post->post_type;
			if ( in_array( $current_post_type, $used_post_types, true ) ) {
				$template_found = Helper::locate_template( "single-{$current_post_type}" );
				$template       = $template_found ? $template_found : WACARA_PATH . "templates/single-{$current_post_type}.php";
			}

			return $template;
		}

		/**
		 * Callback for modifying post row actions.
		 *
		 * @param string[] $actions An array of row action links.
		 * @param WP_Post  $post The post object.
		 *
		 * @return string[]
		 */
		public function manage_post_row_actions_callback( $actions, $post ) {

			// Only hide if registrant post type is being viewed.
			if ( 'registrant' === $post->post_type ) {

				// Hide all action rows.
				unset( $actions['trash'] );
				unset( $actions['view'] );
				unset( $actions['inline hide-if-no-js'] );
			}

			return $actions;
		}

		/**
		 * Callback for adding post filters.
		 *
		 * @param string $post_type current post type.
		 * @param string $which The location of the extra table nav markup.
		 */
		public function manage_post_filters_callback( $post_type, $which ) {

			// Display filter for registrant post type.
			if ( 'registrant' === $post_type ) {

				// Fetch all available registrant status.
				$reg_status = Registrant_Status::get_all_status();
				?>
				<select name="reg_status">
					<option value="0"><?php esc_html_e( 'All status', 'wacara' ); ?></option>
					<?php

					// Get current selected.
					$current_selected = Helper::get( 'reg_status' );

					// Fetch all status.
					foreach ( $reg_status as $status_key => $status_label ) {

						// Maybe current value is selected.
						$maybe_selected = $current_selected && $status_key === $current_selected ? ' selected' : '';
						/* translators: %1$s : status key, %2$s : selected attribute, %3$s : status label */
						echo sprintf( '<option value="%1$s" %2$s>%3$s</option>', $status_key, $maybe_selected, $status_label ); // phpcs:ignore
					}
					?>
				</select>
				<?php
			}

			/**
			 * Wacara after post's custom admin filters.
			 */
			do_action( "wacara_after_{$post_type}_custom_admin_filters" );
		}

		/**
		 * Callback for managing bulk actions in registrant.
		 *
		 * @param array $actions default actions.
		 *
		 * @return array
		 */
		public function bulk_actions_callback( $actions ) {

			// Remove edit and delete action.
			unset( $actions['edit'] );
			unset( $actions['trash'] );

			return $actions;
		}

		/**
		 * Callback for formatting filter to post.
		 *
		 * @param WP_Query $query object of the current query.
		 */
		public function post_format_filter_to_posts_callback( $query ) {
			global $post_type, $pagenow;

			// If we are currently on the edit screen of the post type listings.
			if ( 'edit.php' === $pagenow && 'registrant' === $post_type ) {

				// Get current filter.
				$current_filter = Helper::get( 'reg_status' );

				// Make sure filter is not empty.
				if ( $current_filter ) {

					// Get the desired post format.
					$post_format = sanitize_text_field( $current_filter ); // phpcs:ignore

					// If the post format is not 0 (which means all).
					if ( '0' !== $post_format ) {

						// Override the query.
						$query->query_vars['meta_query'] = [
							[
								'key'   => WACARA_PREFIX . 'reg_status',
								'value' => $current_filter,
							],
						];

					}
				}
			}
		}

		/**
		 * Callback for removing registrant publish metabox.
		 */
		public function remove_registrant_publish_metabox_callback() {
			remove_meta_box( 'submitdiv', 'registrant', 'side' );
		}
	}

	Setting::init();
}
