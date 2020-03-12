<?php
/**
 * Class to manage the master data.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wacara\Master' ) ) {

	/**
	 * Class Master
	 *
	 * @package Wacara
	 */
	class Master {

		/**
		 * Get plugin options.
		 *
		 * @param string $key filter options by key.
		 *
		 * @return mixed|void
		 */
		public static function get_wacara_options( $key = '' ) {
			$result = self::get_option( 'options' );

			// Maybe filter by key.
			if ( $key ) {
				$result = Helper::array_val( $result, $key );
			}

			return $result;
		}

		/**
		 * Get option value.
		 *
		 * @param string $key option key.
		 *
		 * @return mixed|void
		 */
		public static function get_option( $key ) {
			return get_option( WACARA_PREFIX . $key );
		}

		/**
		 * Get country list.
		 *
		 * @param bool $use_blank Whether include empty lists or not.
		 *
		 * @return array
		 */
		public static function get_list_of_countries( $use_blank = false ) {
			$countries = include WACARA_PATH . '/i18n/country.php';
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
			$cache_key = WACARA_PREFIX . 'locations_cache';
			$locations = wp_cache_get( $cache_key );
			if ( false === $locations ) {
				global $wpdb;
				$locations_query = "SELECT `ID`,`post_title` FROM `{$wpdb->prefix}posts` WHERE `post_status` = 'publish' AND `post_type` = 'location' ORDER BY ID DESC";
				$locations       = $wpdb->get_results( $locations_query, OBJECT_K ); // phpcs:ignore
				wp_cache_set( $cache_key, $locations );
			}

			return Helper::convert_wpdb_into_array( $locations );
		}

		/**
		 * Get price list.
		 *
		 * @return array
		 */
		public static function get_list_of_prices() {
			$cache_key = WACARA_PREFIX . 'prices_cache';
			$prices    = wp_cache_get( $cache_key );
			if ( false === $prices ) {
				global $wpdb;
				$prices_query = "SELECT `ID`,`post_title` FROM `{$wpdb->prefix}posts` WHERE `post_status` = 'publish' AND `post_type` = 'price' ORDER BY ID DESC";
				$prices       = $wpdb->get_results( $prices_query, OBJECT_K ); // phpcs:ignore
				wp_cache_set( $cache_key, $prices );
			}

			return Helper::convert_wpdb_into_array( $prices );
		}

		/**
		 * Get header list.
		 *
		 * @return array
		 */
		public static function get_list_of_headers() {
			$cache_key = WACARA_PREFIX . 'headers_cache';
			$headers   = wp_cache_get( $cache_key );
			if ( false === $headers ) {
				global $wpdb;
				$headers_query = "SELECT `ID`,`post_title` FROM `{$wpdb->prefix}posts` WHERE `post_status` = 'publish' AND `post_type` = 'header' ORDER BY ID DESC";
				$headers       = $wpdb->get_results( $headers_query, OBJECT_K ); // phpcs:ignore
				wp_cache_set( $cache_key, $headers );
			}

			return Helper::convert_wpdb_into_array( $headers );
		}

		/**
		 * Get speaker list.
		 *
		 * @return array
		 */
		public static function get_list_of_speakers() {
			$cache_key = WACARA_PREFIX . 'speakers_cache';
			$speakers  = wp_cache_get( $cache_key );
			if ( false === $speakers ) {
				global $wpdb;
				$speakers_query = "SELECT `ID`,`post_title` FROM `{$wpdb->prefix}posts` WHERE `post_status` = 'publish' AND `post_type` = 'speaker' ORDER BY ID DESC";
				$speakers       = $wpdb->get_results( $speakers_query, OBJECT_K ); // phpcs:ignore
				wp_cache_set( $cache_key, $speakers );
			}

			return Helper::convert_wpdb_into_array( $speakers );
		}

		/**
		 * Get list of currency codes.
		 *
		 * @return mixed|void
		 */
		public static function get_list_of_currency_codes() {
			$currencies = [
				'USD' => '$',
				'AUD' => 'AU$',
				'SGD' => 'SG$',
				'IDR' => 'Rp',
				'MYR' => 'RM',
				'JPY' => '¥',
				'EUR' => '€',
				'GBP' => '£',
			];

			/**
			 * Wacara currency symbol filter hook.
			 *
			 * @param array $symbols default available symbols.
			 */
			$currencies = apply_filters( 'wacara_filter_currencies', $currencies );

			return $currencies;
		}

		/**
		 * Find registrant by their booking code.
		 *
		 * @param string $booking_code booking code.
		 *
		 * @return bool|mixed|string|null
		 */
		public static function find_registrant_by_booking_code( $booking_code ) {
			return Helper::get_post_id_by_meta_key( 'booking_code', $booking_code, 'registrant' );
		}
	}
}
