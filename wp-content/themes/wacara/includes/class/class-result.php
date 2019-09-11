<?php
/**
 * Use this class to handle functions which has possibility to throw error.
 *
 * @author  Rendy
 * @package Wacara
 */

namespace Skeleton;

if ( ! defined( 'Skeleton\Result' ) ) {
	/**
	 * Class Result
	 *
	 * @package Skeleton
	 */
	class Result {
		/**
		 * Whether to request return success or false.
		 *
		 * @var bool
		 */
		public $success = false;
		/**
		 * Display error message.
		 *
		 * @var string
		 */
		public $message = '';
		/**
		 * Return array on success request.
		 *
		 * @var array
		 */
		public $items = [];
		/**
		 * Callback to display another response.
		 *
		 * @var string
		 */
		public $callback = '';

		/**
		 * Result constructor.
		 */
		public function __construct() {
		}
	}
}
