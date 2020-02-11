<?php
/**
 * Template for displaying registrant form closing tag
 *
 * @author Rendy
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Add nonce.
wp_nonce_field( 'wacara_nonce' );

// Render current registrant id.
echo apply_filters( 'wacara_input_field', 'registrant_id', 'hidden', '', $registrant_id ); // phpcs:ignore
?>
</form>
