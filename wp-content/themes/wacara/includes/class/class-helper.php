<?php
/**
 * Put all helpful functions that you may use for multiple times
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Skeleton\Helper' ) ) {
	/**
	 * Class Helper
	 *
	 * @package Skeleton
	 */
	class Helper {

		/**
		 * Custom prefix for postmeta and usermeta.
		 *
		 * @var string
		 */
		private static $meta_prefix = TEMP_PREFIX;

		/**
		 * Get main column bootstrap css class width
		 *
		 * @return int
		 */
		public static function get_main_column_width() {
			return is_active_sidebar( 'sk_sidebar' ) ? 'col-md-8' : 'col-md-12';
		}

		/**
		 * Custom pagination
		 *
		 * @param int $number_pages max number page.
		 * @param int $paged        current page.
		 *
		 * @return string
		 */
		public static function custom_pagination( $number_pages, $paged ) {

			/**
			 * This first part of our function is a fallback
			 * for custom pagination inside a regular loop that
			 * uses the global $paged and global $wp_query variables.
			 *
			 * It's good because we can now override default pagination
			 * in our theme, and use this function in default quries
			 * and custom queries.
			 */
			$paged = empty( $paged ) ? 1 : $paged;
			if ( '' === $number_pages ) {
				global $wp_query;
				$number_pages = $wp_query->max_num_pages;
				if ( ! $number_pages ) {
					$number_pages = 1;
				}
			}
			/**
			 * We construct the pagination arguments to enter into our paginate_links
			 * function.
			 */
			$pagination_args = [
				'base'         => add_query_arg( 'paged', '%#%' ),
				'total'        => $number_pages,
				'current'      => $paged,
				'show_all'     => false,
				'end_size'     => 1,
				'mid_size'     => 2,
				'prev_next'    => true,
				'prev_text'    => __( '<i class="remixicon-arrow-left-s-line"></i>' ),
				'next_text'    => __( '<i class="remixicon-arrow-right-s-line"></i>' ),
				'type'         => 'array',
				'add_args'     => true,
				'add_fragment' => '',
			];
			$result          = '';
			$paginate_links  = paginate_links( $pagination_args );
			if ( $paginate_links ) {
				$result .= '<div class="pagination"><ul class="pagination">';
				foreach ( $paginate_links as $page ) {
					$result .= '<li class="page-item ' . ( strpos( $page, 'current' ) !== false ? 'active' : '' ) . '"> ' . str_replace( 'page-numbers', 'page-link', $page ) . '</li>';
				}
				$result .= '</ul></div>';
			}

			return $result;
		}

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
		 * @param string $key     user meta key.
		 */
		public static function delete_user_meta( $user_id, $key ) {
			delete_user_meta( $user_id, self::$meta_prefix . $key );
		}

		/**
		 * Delete user meta
		 *
		 * @param string $post_id post id.
		 * @param string $key     user meta key.
		 */
		public static function delete_post_meta( $post_id, $key ) {
			delete_post_meta( $post_id, self::$meta_prefix . $key );
		}

		/**
		 * Get user meta
		 *
		 * @param string $key          user meta key.
		 * @param string $user_id      user id.
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
		 * @param string|array $key          post meta key.
		 * @param bool|string  $post_id      post id.
		 * @param bool         $single_value whether the meta is single or array.
		 * @param bool         $with_prefix  whether format field with auto prefix or not.
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
		 * @param string $key               key name of data.
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
		 * @param string $key   array key.
		 *
		 * @return bool|mixed will be returned false once aray does not have key.
		 */
		public static function array_val( array $array, $key ) {
			return ! empty( $array[ $key ] ) ? $array[ $key ] : false;
		}

		/**
		 * Get country list.
		 *
		 * @param bool $use_blank Whether include empty lists or not.
		 *
		 * @return array
		 */
		public static function get_list_of_countries( $use_blank = false ) {
			$countries = include TEMP_PATH . '/i18n/country.php';
			if ( $use_blank ) {
				$countries = array_merge( [ '' => __( 'Select country', 'wacara' ) ], $countries );
			}

			return $countries;
		}

		/**
		 * Get location list.
		 *
		 * @return array
		 */
		public static function get_list_of_locations() {
			$cache_key = TEMP_PREFIX . 'locations_cache';
			$locations = wp_cache_get( $cache_key );
			if ( false === $locations ) {
				global $wpdb;
				$locations_query = "SELECT `ID`,`post_title` FROM `{$wpdb->prefix}posts` WHERE `post_status` = 'publish' AND `post_type` = 'location' ORDER BY ID DESC";
				$locations       = $wpdb->get_results( $locations_query, OBJECT_K ); // phpcs:ignore
				wp_cache_set( $cache_key, $locations );
			}

			return self::convert_wpdb_into_array( $locations );
		}

		/**
		 * Get speaker list.
		 *
		 * @return array
		 */
		public static function get_list_of_speakers() {
			$cache_key = TEMP_PREFIX . 'speakers_cache';
			$speakers  = wp_cache_get( $cache_key );
			if ( false === $speakers ) {
				global $wpdb;
				$speakers_query = "SELECT `ID`,`post_title` FROM `{$wpdb->prefix}posts` WHERE `post_status` = 'publish' AND `post_type` = 'speaker' ORDER BY ID DESC";
				$speakers       = $wpdb->get_results( $speakers_query, OBJECT_K ); // phpcs:ignore
				wp_cache_set( $cache_key, $speakers );
			}

			return self::convert_wpdb_into_array( $speakers );
		}

		/**
		 * Get price list.
		 *
		 * @return array
		 */
		public static function get_list_of_prices() {
			$cache_key = TEMP_PREFIX . 'prices_cache';
			$prices    = wp_cache_get( $cache_key );
			if ( false === $prices ) {
				global $wpdb;
				$prices_query = "SELECT `ID`,`post_title` FROM `{$wpdb->prefix}posts` WHERE `post_status` = 'publish' AND `post_type` = 'price' ORDER BY ID DESC";
				$prices       = $wpdb->get_results( $prices_query, OBJECT_K ); // phpcs:ignore
				wp_cache_set( $cache_key, $prices );
			}

			return self::convert_wpdb_into_array( $prices );
		}

		/**
		 * Get header list.
		 *
		 * @return array
		 */
		public static function get_list_of_headers() {
			$cache_key = TEMP_PREFIX . 'headers_cache';
			$headers   = wp_cache_get( $cache_key );
			if ( false === $headers ) {
				global $wpdb;
				$headers_query = "SELECT `ID`,`post_title` FROM `{$wpdb->prefix}posts` WHERE `post_status` = 'publish' AND `post_type` = 'header' ORDER BY ID DESC";
				$headers       = $wpdb->get_results( $headers_query, OBJECT_K ); // phpcs:ignore
				wp_cache_set( $cache_key, $headers );
			}

			return self::convert_wpdb_into_array( $headers );
		}

		/**
		 * Convert data from wpdb into readable array for cmb2.
		 *
		 * @param array $data original array of object data.
		 *
		 * @return array
		 */
		private static function convert_wpdb_into_array( array $data ) {
			$result = [];
			if ( ! empty( $data ) ) {
				foreach ( $data as $id => $obj ) {
					$result[ $id ] = $obj->post_title;
				}
			}

			return $result;
		}

		/**
		 * Split title into theme format
		 *
		 * @param string $original_title original title that will be split.
		 *
		 * @return string
		 */
		public static function split_title( $original_title ) {
			$title_array     = explode( ' ', $original_title );
			$number_array    = count( $title_array );
			$formatted_title = '';
			$split_number    = 0;
			if ( $number_array > 2 ) {
				$split_number = $number_array - 2;
			}
			$num_arr = 0;
			foreach ( $title_array as $item ) {
				if ( $split_number === $num_arr ) {
					$formatted_title .= '<span>';
				}
				$formatted_title .= $item . ' ';
				if ( $number_array === $num_arr ) {
					$formatted_title .= '</span>';
				}
				$num_arr ++;
			}

			return $formatted_title;
		}

		/**
		 * Format timestamp into readable date;
		 *
		 * @param int  $timestamp              unformulated timestamp.
		 * @param bool $include_time_in_result whether include time in result or not.
		 * @param bool $localize               whether convert date into localize result or not.
		 *
		 * @return false|string
		 */
		public static function convert_date( $timestamp, $include_time_in_result = false, $localize = false ) {
			$date_format = self::get_date_format();
			$time_format = self::get_time_format();
			$used_format = $date_format . ( $include_time_in_result ? ' ' . $time_format : '' );

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
			$counties = self::get_list_of_countries();

			return ! empty( $counties[ $country_code ] ) ? $counties[ $country_code ] : '';
		}

		/**
		 * Get full location info
		 *
		 * @param string $location_id  location id.
		 * @param bool   $include_name whether include location name or not.
		 *
		 * @return string
		 */
		public static function get_location_paragraph( $location_id, $include_name = true ) {
			$name         = self::get_post_meta( 'name', $location_id );
			$address      = self::get_post_meta( 'address', $location_id );
			$city         = self::get_post_meta( 'city', $location_id );
			$province     = self::get_post_meta( 'province', $location_id );
			$country_code = self::get_post_meta( 'country', $location_id );
			$country_name = self::translate_country_code( $country_code );
			$postal       = self::get_post_meta( 'postal', $location_id );

			return ( $include_name ? $name . ', ' : '' ) . $address . ', ' . $city . ', ' . $province . ' ' . $postal . ' ' . $country_name;
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
		 * Check whether the location is valid or not.
		 *
		 * @param string $location_id location id.
		 *
		 * @return Result
		 */
		public static function is_location_valid( $location_id ) {
			$result = new Result();
			// Is name assigned.
			$name = self::get_post_meta( 'name', $location_id );
			if ( $name ) {
				// Is country assigned.
				$country = self::get_post_meta( 'country', $location_id );
				if ( $country ) {
					// Is province assigned.
					$province = self::get_post_meta( 'province', $location_id );
					if ( $province ) {
						// Is city assigned.
						$city = self::get_post_meta( 'city', $location_id );
						if ( $city ) {
							// Is address assigned.
							$address = self::get_post_meta( 'address', $location_id );
							if ( $address ) {
								// Is postal_code assigned.
								$postal = self::get_post_meta( 'postal', $location_id );
								if ( $postal ) {
									// Is photo assigned.
									$photo = self::get_post_meta( 'photo', $location_id );
									if ( $photo ) {
										// Is description assigned.
										$description = self::get_post_meta( 'description', $location_id );
										if ( $description ) {
											$result->success = true;
										} else {
											$result->message = __( 'This event is not completed yet, it uses invalid location which does not have valid description', 'wacara' );
										}
									} else {
										$result->message = __( 'This event is not completed yet, it uses invalid location which does not have valid photo', 'wacara' );
									}
								} else {
									$result->message = __( 'This event is not completed yet, it uses invalid location which does not have valid postal code', 'wacara' );
								}
							} else {
								$result->message = __( 'This event is not completed yet, it uses invalid location which does not have valid address', 'wacara' );
							}
						} else {
							$result->message = __( 'This event is not completed yet, it uses invalid location which does not have valid city', 'wacara' );
						}
					} else {
						$result->message = __( 'This event is not completed yet, it uses invalid location which does not have valid province', 'wacara' );
					}
				} else {
					$result->message = __( 'This event is not completed yet, it uses invalid location which does not have valid country', 'wacara' );
				}
			} else {
				$result->message = __( 'This event is not completed yet, it uses invalid location which does not have valid name', 'wacara' );
			}

			return $result;
		}

		/**
		 * Check whether the pricing is valid or not.
		 *
		 * @param string $pricing_id         pricing id.
		 * @param bool   $validate_the_price whether validate the price or not.
		 *
		 * @return Result
		 */
		public static function is_pricing_valid( $pricing_id, $validate_the_price = false ) {
			$result = new Result();
			// Is price assigned.
			$price = self::get_post_meta( 'price', $pricing_id );
			if ( $price || 0 === (int) $price ) {
				// Is currency assigned.
				$currency = self::get_post_meta( 'currency', $pricing_id );
				if ( $currency ) {
					if ( $validate_the_price ) {
						if ( (int) $price > 0 ) {
							$result->success = true;
						} else {
							$result->message = __( 'The pricing amount should be greater than 0', 'wacara' );
						}
					} else {
						$result->success = true;
					}
				} else {
					$result->message = __( 'The pricing is invalid, the currency has not been assigned yet', 'wacara' ) . $currency;
				}
			} else {
				$result->message = __( 'The pricing is invalid, the price amount has not been assigned yet', 'wacara' );
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
			$symbols         = [
				'USD' => '$',
				'AUD' => 'AU$',
				'SGD' => 'SG$',
				'IDR' => 'Rp',
				'MYR' => 'RM',
				'JPY' => '¥',
				'EUR' => '€',
				'GBP' => '£',
			];

			// Filter currency_symbols.
			$symbols = apply_filters( 'wacara_currency_symbols', $symbols );

			if ( ! empty( $symbols[ $currency_code ] ) ) {
				$currency_symbol = $symbols[ $currency_code ];
			}

			return $currency_symbol;
		}

		/**
		 * Get event main logo url.
		 *
		 * @param string $event_id event id.
		 *
		 * @return false|string
		 */
		public static function get_event_logo_url( $event_id ) {
			$event = new Event( $event_id );

			return $event->get_logo_url();
		}

		/**
		 * Get site logo url.
		 *
		 * @return false|string
		 */
		public static function get_site_logo_url() {
			$static_logo = TEMP_URI . '/assets/img/sample-logo.png';
			$site_logo   = Options::get_theme_option( 'logo_id' );
			if ( $site_logo ) {
				$static_logo = wp_get_attachment_image_url( $site_logo, 'medium' );
			}

			return $static_logo;
		}

		/**
		 * Get post id by meta key.
		 *
		 * @param string $meta_key   meta key.
		 * @param string $meta_value meta value.
		 * @param string $post_type  post type.
		 *
		 * @return Result
		 */
		public static function get_post_id_by_meta_key( $meta_key, $meta_value, $post_type = 'post' ) {
			global $wpdb;
			$result     = new Result();
			$table_meta = $wpdb->prefix . 'postmeta';
			$table_post = $wpdb->prefix . 'posts';
			$post_meta  = TEMP_PREFIX . $meta_key;
			$cache_key  = TEMP_PREFIX . $post_type . $meta_key . $meta_value;

			// Find post id from the cache.
			$post_id = wp_cache_get( $cache_key );

			// Validate the post id.
			if ( false === $post_id ) {

				// Perform the direct query.
				$post_id = $wpdb->get_var( "SELECT {$table_meta}.post_id FROM {$table_meta} INNER JOIN {$table_post} ON {$table_meta}.post_id = {$table_post}.ID WHERE {$table_post}.post_type = '{$post_type}' AND {$table_meta}.meta_key = '{$post_meta}' AND {$table_meta}.meta_value = '{$meta_value}' ORDER BY meta_id DESC LIMIT 1" ); // phpcs:ignore

				// Save post id to cache.
				wp_cache_set( $cache_key, $post_id );
			}

			// Re-validate the post id.
			if ( $post_id ) {

				// Update the result.
				$result->success  = true;
				$result->callback = $post_id;
			} else {
				$wpdb->hide_errors();

				// Update the result.
				/* translators: 1: the post type name */
				$result->message = sprintf( __( '%s not found', 'wacara' ), ucfirst( $post_type ) );
			}

			return $result;
		}

		/**
		 * Dirty way to get current post id.
		 *
		 * @return bool|int
		 */
		public static function get_dirty_current_post_id() {
			$result = false;

			$request_uri = $_SERVER['REQUEST_URI']; // phpcs:ignore
			$event_obj   = get_page_by_path( $request_uri, OBJECT, [ 'event', 'participant' ] ); // phpcs:ignore

			// Maybe remove trailing slash.
			if ( ! $event_obj ) {
				$event_obj = get_page_by_path( untrailingslashit( $request_uri ), OBJECT, [ 'event', 'participant' ] );
			}

			// Maybe remove the first slash.
			if ( ! $event_obj ) {
				$event_obj = get_page_by_path(
					substr( untrailingslashit( $request_uri ), 1 ),
					OBJECT,
					[
						'event',
						'participant',
					]
				);
			}

			// Convert into array.
			$path_arr     = explode( '/', substr( untrailingslashit( $request_uri ), 1 ) );
			$path_arr_num = count( $path_arr );
			if ( ! empty( $path_arr ) ) {
				for ( $i = 0; $i < $path_arr_num; $i ++ ) {
					unset( $path_arr[ $i ] );
					$new_path  = implode( '/', $path_arr );
					$event_obj = get_page_by_path( $new_path, OBJECT, [ 'event', 'participant' ] );
					if ( $event_obj ) {
						$result = $event_obj->ID;
						break;
					}
				}
			}

			return $result;
		}
	}
}
