<?php
/**
 * Template for displaying top-nav in registrant.
 *
 * @author WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<nav class="wcr-nav">
	<div class="frow-container">
		<div class="wcr-nav-wrapper">
			<?php if ( $nav_logo_url ) { ?>
				<div class="wcr-nav-logo-wrapper">
					<a href="<?php echo esc_attr__($nav_home_url); ?>" class="wcr-nav-logo-link">
						<img src="<?php echo esc_attr( $nav_logo_url ); ?>" class="wcr-nav-logo" alt="">
					</a>
				</div>
			<?php } ?>
		</div>
	</div>
</nav>
