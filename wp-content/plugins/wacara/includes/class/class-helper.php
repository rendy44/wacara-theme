<?php
/**
 * Put all helpful functions that you may use for multiple times
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Helper' ) ) {

	/**
	 * Class Helper
	 *
	 * @package Wacara
	 */
	class Helper {

		/**
		 * Custom prefix for postmeta and usermeta.
		 *
		 * @var string
		 */
		private static $meta_prefix = WACARA_PREFIX;

		/**
		 * Save multiple post meta
		 *
		 * @param int   $post_id post id.
		 * @param array $options meta_key => meta_value formatted array.
		 */
		public static function save_post_meta( $post_id, $options = [] ) {
			if ( ! empty( $options ) ) {
				foreach ( $options as $option_key => $option_value ) {
					update_post_meta( $post_id, self::$meta_prefix . $option_key, $option_value );
				}
			}
		}

		/**
		 * Add a new single post meta.
		 *
		 * @param int          $post_id post id.
		 * @param string       $meta_key meta key.
		 * @param string|array $meta_value meta value.
		 */
		public static function add_post_meta( $post_id, $meta_key, $meta_value ) {
			add_post_meta( $post_id, self::$meta_prefix . $meta_key, $meta_value );
		}

		/**
		 * Save multiple user meta
		 *
		 * @param string $user_id user id.
		 * @param array  $options meta_key => meta_value formatterd array.
		 */
		public static function save_user_meta( $user_id, $options = [] ) {
			if ( ! empty( $options ) ) {
				foreach ( $options as $option_key => $option_value ) {
					update_user_meta( $user_id, self::$meta_prefix . $option_key, $option_value );
				}
			}
		}

		/**
		 * Delete user meta
		 *
		 * @param string $user_id user id.
		 * @param string $key user meta key.
		 */
		public static function delete_user_meta( $user_id, $key ) {
			delete_user_meta( $user_id, self::$meta_prefix . $key );
		}

		/**
		 * Delete user meta
		 *
		 * @param string $post_id post id.
		 * @param string $key user meta key.
		 */
		public static function delete_post_meta( $post_id, $key ) {
			delete_post_meta( $post_id, self::$meta_prefix . $key );
		}

		/**
		 * Get user meta
		 *
		 * @param string $key user meta key.
		 * @param string $user_id user id.
		 * @param bool   $single_value whether the meta is single or array.
		 *
		 * @return mixed
		 */
		public static function get_user_meta( $key, $user_id, $single_value = true ) {
			return get_user_meta( $user_id, self::$meta_prefix . $key, $single_value );
		}

		/**
		 * Get post meta
		 *
		 * @param string|array $key post meta key.
		 * @param bool|string  $post_id post id.
		 * @param bool         $single_value whether the meta is single or array.
		 * @param bool         $with_prefix whether format field with auto prefix or not.
		 *
		 * @return array|bool|mixed
		 */
		public static function get_post_meta( $key, $post_id = false, $single_value = true, $with_prefix = true ) {
			$result  = false;
			$post_id = ! $post_id ? get_the_ID() : $post_id;
			$prefix  = $with_prefix ? self::$meta_prefix : '';
			if ( is_array( $key ) ) {
				foreach ( $key as $single_key ) {
					$result[ $single_key ] = get_post_meta( $post_id, $prefix . $single_key, $single_value );
				}
			} else {
				$result = get_post_meta( $post_id, $prefix . $key, $single_value );
			}

			return $result;
		}

		/**
		 * Get serialized value
		 *
		 * @param array  $unserialized_data unserialized data to be parsed.
		 * @param string $key key name of data.
		 *
		 * @return array|bool|mixed
		 */
		public static function get_serialized_val( $unserialized_data, $key ) {
			$result           = '';
			$temporary_result = [];
			foreach ( $unserialized_data as $obj ) {
				if ( $obj['name'] === $key ) {
					$temporary_result[] = $obj['value'];
				}
			}
			$count_result = count( $temporary_result );
			if ( $count_result > 0 ) {
				$result = count( $temporary_result ) > 1 ? $temporary_result : $temporary_result[0];
			}

			return $result;
		}

		/**
		 * Simple $_POST request handler
		 *
		 * @param string $key post key.
		 *
		 * @return bool|mixed
		 */
		public static function post( $key ) {
			return ! empty( $_POST[ $key ] ) ? $_POST[ $key ] : false; // phpcs:ignore
		}

		/**
		 * Simple $_GET request handler
		 *
		 * @param string $key get key.
		 *
		 * @return bool|mixed
		 */
		public static function get( $key ) {
			return ! empty( $_GET[ $key ] ) ? $_GET[ $key ] : false; // phpcs:ignore
		}

		/**
		 * Get array value by its key.
		 *
		 * @param array  $array array object.
		 * @param string $key array key.
		 * @param mixed  $return_false return on false.
		 *
		 * @return bool|mixed will be returned false once aray does not have key.
		 */
		public static function array_val( array $array, $key, $return_false = [] ) {
			return ! empty( $array[ $key ] ) ? $array[ $key ] : $return_false;
		}

		/**
		 * Convert data from wpdb into readable array for cmb2.
		 *
		 * @param array $data original array of object data.
		 *
		 * @return array
		 */
		public static function convert_wpdb_into_array( array $data ) {
			$result = [];
			if ( ! empty( $data ) ) {
				foreach ( $data as $id => $obj ) {
					$result[ $id ] = $obj->post_title;
				}
			}

			return $result;
		}

		/**
		 * Format timestamp into readable date;
		 *
		 * @param int    $timestamp unformulated timestamp.
		 * @param bool   $include_time_in_result whether include time in result or not.
		 * @param bool   $localize whether convert date into localize result or not.
		 * @param string $override_format completely override date format.
		 *
		 * @return false|string
		 */
		public static function convert_date( $timestamp, $include_time_in_result = false, $localize = false, $override_format = '' ) {
			$used_format = $include_time_in_result ? self::get_date_time_format() : self::get_date_format();

			// Just in case override all the date format.
			if ( $override_format ) {
				$used_format = $override_format;
			}

			return $localize ? date_i18n( $used_format, $timestamp ) : date( $used_format, $timestamp );
		}

		/**
		 * Get today timestamp.
		 *
		 * @return false|int
		 */
		public static function get_today_timestamp() {
			$today_date = date( self::get_date_format() );

			return strtotime( $today_date );
		}

		/**
		 * Translate country code into readable country name.
		 *
		 * @param string $country_code two digits of country code.
		 *
		 * @return mixed|string
		 */
		public static function translate_country_code( $country_code ) {
			$counties = Master::get_list_of_countries();

			return self::array_val( $counties, $country_code, '' );
		}

		/**
		 * Get date format from options
		 *
		 * @return mixed|void
		 */
		public static function get_date_format() {
			return get_option( 'date_format' );
		}

		/**
		 * Get time format from option
		 *
		 * @return mixed|void
		 */
		public static function get_time_format() {
			return get_option( 'time_format' );
		}

		/**
		 * Get date and time format from option.
		 *
		 * @return string
		 */
		public static function get_date_time_format() {
			return self::get_date_format() . ' ' . self::get_time_format();
		}

		/**
		 * Get readable utc.
		 *
		 * @return string
		 */
		public static function get_readable_utc() {
			$utc    = (int) get_option( 'gmt_offset' );
			$result = 'UTC';
			if ( 0 !== $utc ) {
				if ( $utc > 0 ) {
					$result .= '+';
				}
				$result .= $utc;
			}

			return $result;
		}

		/**
		 * Convert currency code into currency symbol.
		 *
		 * @param string $currency_code currency code that will be converted into currency symbol.
		 *
		 * @return mixed|void
		 */
		public static function get_currency_symbol_by_code( $currency_code ) {
			// Use USD as default symbol.
			$currency_symbol = '$';
			$currencies      = Master::get_list_of_currency_codes();
			$filter          = self::array_val( $currencies, $currency_code, false );

			// If filter has result then use it.
			if ( $filter ) {
				$currency_symbol = $filter;
			}

			return $currency_symbol;
		}

		/**
		 * Get site logo url.
		 *
		 * @return bool|false|string
		 */
		public static function get_site_logo_url() {
			$result    = false;
			$site_logo = Master::get_wacara_options( 'logo_id' );
			if ( $site_logo ) {
				$result = wp_get_attachment_image_url( $site_logo, 'medium' );
			}

			return $result;
		}

		/**
		 * Get post id by meta key.
		 *
		 * @param string $meta_key meta key.
		 * @param string $meta_value meta value.
		 * @param string $post_type post type.
		 *
		 * @return bool|mixed|string|null
		 */
		public static function get_post_id_by_meta_key( $meta_key, $meta_value, $post_type = 'post' ) {
			global $wpdb;
			$table_meta = $wpdb->prefix . 'postmeta';
			$table_post = $wpdb->prefix . 'posts';
			$post_meta  = WACARA_PREFIX . $meta_key;
			$cache_key  = WACARA_PREFIX . $post_type . $meta_key . $meta_value;

			// Find post id from the cache.
			$post_id = wp_cache_get( $cache_key );

			// Validate the post id.
			if ( false === $post_id ) {

				// Perform the direct query.
				$post_id = $wpdb->get_var( "SELECT {$table_meta}.post_id FROM {$table_meta} INNER JOIN {$table_post} ON {$table_meta}.post_id = {$table_post}.ID WHERE {$table_post}.post_type = '{$post_type}' AND {$table_meta}.meta_key = '{$post_meta}' AND {$table_meta}.meta_value = '{$meta_value}' ORDER BY meta_id DESC LIMIT 1" ); // phpcs:ignore

				// Save post id to cache.
				wp_cache_set( $cache_key, $post_id, '', 3600 );
			}

			return $post_id;
		}

		/**
		 * Load template
		 *
		 * @param string $template_name template name without extension.
		 * @param bool   $unique whether include theme once or not.
		 */
		public static function load_template( $template_name, $unique = false ) {
			$find_in_theme = self::locate_template( $template_name );
			$template      = $find_in_theme ? $find_in_theme : WACARA_PATH . "templates/{$template_name}.php";
			if ( $unique ) {
				include_once $template;
			} else {
				include $template;
			}
		}

		/**
		 * Locate the template in theme.
		 *
		 * @param string $template_name template name that's going to be located in theme.
		 *
		 * @return string
		 */
		public static function locate_template( $template_name ) {
			return locate_template( "wacara/{$template_name}.php" );
		}

		/**
		 * Load css file
		 *
		 * @param string $name css name.
		 * @param array  $obj_css css object.
		 */
		public static function load_css( $name, array $obj_css ) {
			$depth = ! empty( $obj_css['depth'] ) ? $obj_css['depth'] : [];
			wp_enqueue_style( $name, $obj_css['url'], $depth, WACARA_VERSION );
		}

		/**
		 * Load js file
		 *
		 * @param string $name js name.
		 * @param array  $obj_js js object.
		 */
		public static function load_js( $name, array $obj_js ) {
			$js_prefix = WACARA_PREFIX . 'module_';
			$is_module = false === $obj_js['module'] ? false : true;
			$depth     = ! empty( $obj_js['depth'] ) ? $obj_js['depth'] : [];
			$name      = $is_module ? $js_prefix . $name : $name;
			wp_enqueue_script( $name, $obj_js['url'], $depth, WACARA_VERSION, true );
		}

		/**
		 * Register ajax endpoint.
		 *
		 * @param string   $endpoint the endpoint name.
		 * @param callable $callback obect of the endpoint.
		 * @param bool     $is_public whether set endpoint as public or not.
		 * @param bool     $is_logged_in whether set endpoint as accessible in logged in user or not.
		 */
		public static function add_ajax_endpoint( $endpoint, $callback, $is_public = true, $is_logged_in = true ) {
			$endpoint_prefix = WACARA_PREFIX;

			// Only register endpoint that has callback.
			if ( is_callable( $callback ) ) {

				// Register endpoint for public access.
				if ( $is_public ) {
					add_action( 'wp_ajax_nopriv_' . $endpoint_prefix . $endpoint, $callback );
				}

				// Register endpoint for admin access.
				if ( $is_logged_in ) {
					add_action( 'wp_ajax_' . $endpoint_prefix . $endpoint, $callback );
				}
			}
		}

		/**
		 * Maybe convert ajax endpoint object to fill the default args.
		 *
		 * @param array $endpoint_object default endpoint.
		 *
		 * @return array
		 */
		public static function maybe_convert_ajax_endpoint_obj( $endpoint_object ) {
			$default_args = [
				'callback'  => false,
				'public'    => true,
				'logged_in' => true,
			];

			return wp_parse_args( $endpoint_object, $default_args );
		}

		/**
		 * Convert string into readable string
		 *
		 * @param string $string original string.
		 * @param bool   $uppercase whether convert as uppercase or not.
		 *
		 * @return string|string[]
		 */
		private static function convert_to_readable_string( $string, $uppercase = true ) {
			$string = str_replace( '-', ' ', $string );
			$string = str_replace( '_', ' ', $string );

			if ( $uppercase ) {
				$string = ucfirst( $string );
			}

			return $string;
		}

		/**
		 * Register custom post type.
		 *
		 * @param string $name name of the custom post type.
		 * @param array  $label_args label configuration.
		 * @param array  $setting_args setting configuration.
		 * @param string $dashicon dashicon name.
		 */
		public static function register_post_type( $name, $label_args = [], $setting_args = [], $dashicon = '' ) {
			$readable_string        = self::convert_to_readable_string( $name );
			$readable_string_plural = $readable_string . 's';

			// Prepare default args for label configuration.
			$default_label_args = [
				'name'               => $readable_string_plural,
				'singular_name'      => $readable_string,
				'menu_name'          => $readable_string_plural,
				'name_admin_bar'     => $readable_string,
				'add_new'            => __( 'Add New', 'wacara' ),
				/* translators: %s: singular post type */
				'add_new_item'       => sprintf( _x( 'Add New %s', 'Add New Post Type', 'wacara' ), $readable_string ),
				/* translators: %s: singular post type */
				'new_item'           => sprintf( _x( 'New %s', 'New Post Type', 'wacara' ), $readable_string ),
				/* translators: %s: singular post type */
				'edit_item'          => sprintf( _x( 'Edit %s', 'Edit Post Type', 'wacara' ), $readable_string ),
				/* translators: %s: singular post type */
				'view_item'          => sprintf( _x( 'View %s', 'View Post Type', 'wacara' ), $readable_string ),
				/* translators: %s: plural post type */
				'all_items'          => sprintf( _x( 'All %s', 'All Post Types', 'wacara' ), $readable_string_plural ),
				/* translators: %s: plural post type */
				'search_items'       => sprintf( _x( 'Search %s', 'Search Post Types', 'wacara' ), $readable_string_plural ),
				/* translators: %s: plural post type */
				'parent_item_colon'  => sprintf( _x( 'Parent %s:', 'Parent Post Types:', 'wacara' ), $readable_string_plural ),
				/* translators: %s: plural post type */
				'not_found'          => sprintf( _x( 'No %s found.', 'No post types found', 'wacara' ), $readable_string_plural ),
				/* translators: %s: plural post type */
				'not_found_in_trash' => sprintf( _x( 'No %s found in Trash.', 'No post types found in trash.', 'wacara' ), $readable_string_plural ),
			];

			$label_args = wp_parse_args( $label_args, $default_label_args );

			// Prepare default args for setting configuration.
			$default_args = [
				'labels'             => $label_args,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => [ 'slug' => $name ],
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'supports'           => [ 'title' ],
			];
			if ( $dashicon ) {
				$default_args['menu_icon'] = $dashicon;
			}

			// Clean non editable args.
			if ( isset( $setting_args['label'] ) ) {
				unset( $setting_args['label'] );
			}
			if ( isset( $setting_args['menu_icon'] ) ) {
				unset( $setting_args['menu_icon'] );
			}

			$setting_args = wp_parse_args( $setting_args, $default_args );

			add_action(
				'init',
				function () use ( $name, $setting_args ) {
					// Register the post type.
					register_post_type( $name, $setting_args );
				}
			);
		}

		/**
		 * Convert properties into inline styles.
		 *
		 * @param array $properties list of properties.
		 * @param bool  $style_attribute include style attribute or not.
		 *
		 * @return string
		 */
		public static function convert_properties_to_inline_styles( $properties, $style_attribute = true ) {
			$result = '';
			if ( ! empty( $properties ) ) {

				foreach ( $properties as $property_name => $property_value ) {
					$result .= "{$property_name}: {$property_value};";
				}

				// Maybe include style attribute.
				if ( $style_attribute ) {
					$result = "style='{$result}'";
				}
			}

			return $result;
		}

		/**
		 * Add and render modal.
		 *
		 * @param string $id id of the modal.
		 * @param string $body html content of the body.
		 * @param string $title title of the modal.
		 * @param string $footer footer content of the modal.
		 */
		public static function add_modal( $id, $body, $title = '', $footer = '' ) {
			$modal_args = [
				'modal_id'     => $id,
				'modal_title'  => $title,
				'modal_body'   => $body,
				'modal_footer' => $footer,
			];

			/**
			 * Wacara modal args filter hook.
			 *
			 * @param array $modal_args default args.
			 */
			$modal_args = apply_filters( 'wacara_filter_modal_args', $modal_args );

			Template::render( 'modal/wrapper', $modal_args, true );
		}

		/**
		 * Simple encrypt and decrypt function.
		 *
		 * @param string $string string to be encrypted/decrypted.
		 * @param string $action what to do with this? e for encrypt, d for decrypt.
		 *
		 * @return bool|false|string
		 * @link http://nazmulahsan.me/simple-two-way-function-encrypt-decrypt-string/
		 *
		 * @author Nazmul Ahsan <n.mukto@gmail.com>
		 */
		public static function encryption( $string, $action = 'e' ) {

			// Define key.
			$secret_key = WACARA_PREFIX;
			$secret_iv  = WACARA_PREFIX . WACARA_PREFIX; // TODO: Add a secure key.

			$output         = false;
			$encrypt_method = 'AES-256-CBC';
			$key            = hash( 'sha256', $secret_key );
			$iv             = substr( hash( 'sha256', $secret_iv ), 0, 16 );

			if ( 'e' === $action ) {
				$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
			} elseif ( 'd' === $action ) {
				$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
			}

			return $output;
		}

		/**
		 * Censor email.
		 *
		 * @param string $email plain email address.
		 *
		 * @return string
		 */
		public static function censor_email( $email ) {
			$em   = explode( '@', $email );
			$name = implode( array_slice( $em, 0, count( $em ) - 1 ), '@' );
			$len  = floor( strlen( $name ) / 2 );

			return substr( $name, 0, $len ) . str_repeat( '*', $len ) . '@' . end( $em );
		}
	}
}
