<?php
/**
 * Template for displaying input text.
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo sprintf( '<input class="form-control form-control-lg" id="%s" type="%s" name="%s" value="%s" %s>', $field_id, $field_type, $field_id, $field_value, $field_required ); // phpcs:ignore
