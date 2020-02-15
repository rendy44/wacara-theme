<?php
/**
 * Template for displaying top-nav in event.
 *
 * @author Rendy
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<nav class="wcr-event-nav">
	<div class="frow-container">
		<div class="wcr-nav-wrapper">
			<div class="wcr-nav-toggle-wrapper">
				<span class="wcr-nav-toggle"></span>
				<span class="wcr-nav-toggle"></span>
				<span class="wcr-nav-toggle"></span>
			</div>
			<?php
			if ( $nav_logo_url ) {
				?>
				<div class="wcr-nav-logo-wrapper">
					<a href="#" class="wcr-nav-logo-link">
						<img src="<?php echo esc_attr( $nav_logo_url ); ?>" class="wcr-nav-logo" alt="">
					</a>
				</div>
			<?php } ?>
			<div class="wcr-nav-menu-wrapper">
				<?php if ( ! empty( $nav_items ) ) { ?>
					<ul class="wcr-nav-menu">
						<?php foreach ( $nav_items as $nav_id => $nav_title ) { ?>
							<li class="wcr-nav-menu-item-wrapper <?php echo esc_attr( $nav_id ); ?>">
								<a href="<?php echo esc_attr( '#wcr-section-' . $nav_id ); ?>" class="wcr-nav-menu-item"><?php echo esc_html( $nav_title ); ?></a>
							</li>
						<?php } ?>
					</ul>
				<?php } ?>
			</div>
		</div>
	</div>
</nav>
