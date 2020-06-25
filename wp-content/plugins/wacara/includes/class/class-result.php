<?php
/**
 * Use this class to handle functions which has possibility to throw error.
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

namespace Wacara;

if ( ! defined( 'Wacara\Result' ) ) {

	/**
	 * Class Result
	 *
	 * @package Wacara
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
		public $items = array();

		/**
		 * Callback to display another response.
		 *
		 * @var string
		 */
		public $callback = '';

		/**
		 * Max num pages of loop.
		 *
		 * @var int
		 */
		public $max_num_pages = 0;
	}
}
