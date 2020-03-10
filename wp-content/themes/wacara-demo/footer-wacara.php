<?php
/**
 * Footer template.
 *
 * @author WPerfekt
 * @package Wacara_Theme
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

</body>
<footer class="wcr-footer wcr-footer-dark">
	<div class="frow-container">
		<div class="frow">
			<div class="col-sm-2-3 wcr-text-center">
				<?php
				/* translators: %1$s : site name, %2$s : site description, %3$s : current year */
				echo sprintf( _x( '<p class="wcr-footer-content">&copy; %1$s - %2$s %3$s</p>', 'footer content', 'wacara' ), get_bloginfo( 'name' ), get_bloginfo( 'description' ), date( 'Y' ) ); // phpcs:ignore ?>
			</div>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</html>
