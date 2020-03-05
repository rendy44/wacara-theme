<?php
/**
 * Email template after making registration.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<p>Hi <?php echo esc_html( $recipient_name ); ?>,</p>
<p>Mantab laahhh pokokna mah, ini <i><?php echo esc_html( $recipient_email ); ?></i> emailmu kan?</p>
