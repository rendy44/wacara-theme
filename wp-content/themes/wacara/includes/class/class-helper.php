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
		 * @param int $numpages max numpage.
		 * @param int $paged    current page.
		 *
		 * @return string
		 */
		public static function custom_pagination( $numpages, $paged ) {

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
			if ( '' === $numpages ) {
				global $wp_query;
				$numpages = $wp_query->max_num_pages;
				if ( ! $numpages ) {
					$numpages = 1;
				}
			}
			/**
			 * We construct the pagination arguments to enter into our paginate_links
			 * function.
			 */
			$pagination_args = [
				'base'         => add_query_arg( 'paged', '%#%' ),
				'total'        => $numpages,
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
		 * @param string      $key          post meta key.
		 * @param bool|string $post_id      post id.
		 * @param bool        $single_value whether the meta is single or array.
		 *
		 * @return mixed
		 */
		public static function get_post_meta( $key, $post_id = false, $single_value = true ) {
			$post_id = ! $post_id ? get_the_ID() : $post_id;

			return get_post_meta( $post_id, self::$meta_prefix . $key, $single_value );
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
		 * @param int  $timestamp    unformatted timestamp.
		 * @param bool $include_time whether include time in result or not.
		 *
		 * @return false|string
		 */
		public static function convert_date( $timestamp, $include_time = false ) {
			$date_format = self::get_date_format();
			$time_format = self::get_time_format();

			return date( $date_format . ( $include_time ? ' ' . $time_format : '' ), $timestamp );
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
		 * Get full event time info
		 *
		 * @param bool|string $event_id event id.
		 *
		 * @return string
		 */
		public static function get_time_paragraph( $event_id = false ) {
			$event_id             = ! $event_id ? get_the_ID() : $event_id;
			$single_day           = self::get_post_meta( 'single_day', $event_id );
			$date_start_timestamp = self::get_post_meta( 'date_start', $event_id );
			$date_start           = self::convert_date( $date_start_timestamp, true );
			$end                  = $single_day ? self::get_post_meta( 'time_end', $event_id ) : self::convert_date( self::get_post_meta( 'date_end', $event_id ), true );
			$utc                  = self::get_readable_utc();

			return $date_start . ' - ' . $end . ' ' . $utc;

		}
	}
}
