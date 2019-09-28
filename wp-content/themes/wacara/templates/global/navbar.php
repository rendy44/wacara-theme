<?php
/**
 * Custom template for displaying top navbar
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<nav class="navbar navbar-expand-lg navbar-light fixed-top <?php echo esc_attr( $nav_class ); ?>">
    <div class="container">
        <a class="navbar-brand scroll" href="<?php echo esc_attr( $home_link ); ?>">
            <img src="<?php echo esc_attr( $logo_url ); ?>" alt="">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>
		<?php
		if ( $use_full_nav && isset( $use_full_nav ) ) {
			?>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
					<?php
					if ( ! empty( $nav_items ) ) {
						foreach ( $nav_items as $nav_item_slug => $nav_item_text ) {
							?>
                            <li class="nav-item <?php echo esc_attr( $nav_item_slug ); ?>">
                                <a class="nav-link scroll" href="#<?php echo esc_attr( $nav_item_slug ); ?>"><?php echo esc_html( $nav_item_text ); ?></a>
                            </li>
							<?php
						}
					}
					?>
                </ul>
            </div>
			<?php
		}
		?>
    </div>
</nav>
