<?php
/**
 * Custom template for displaying html closing tag
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<footer class="bg-dark">
	<div class="container">
        <p class="text-white font-weight-light text-center"><?php echo $content; // phpcs:ignore ?></p>
	</div>
</footer>
<?php wp_footer(); ?>
</main>
</body>
</html>
