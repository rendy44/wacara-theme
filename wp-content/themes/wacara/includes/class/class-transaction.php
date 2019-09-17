<?php
/**
 * Class to handle all transaction related to transaction.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Skeleton\Transaction' ) ) {
	/**
	 * Class Transaction
	 *
	 * @package Skeleton
	 */
	class Transaction {
		/**
		 * Create customer.
		 *
		 * @param string $customer_email customer email address.
		 *
		 * @return Result
		 */
		public static function save_customer( $customer_email ) {
			$result       = new Result();
			$new_customer = wp_insert_post(
				[
					'post_type'   => 'customer',
					'post_status' => 'publish',
					'post_title'  => $customer_email,
					'post_name'   => sanitize_title( $customer_email ),
				]
			);

			return $result;
		}

		public static function find_customer( $customer_email ) {

		}
	}
}
