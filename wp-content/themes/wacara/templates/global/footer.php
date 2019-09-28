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

<footer class="py-4 bg-white" id="sticky-footer">
    <div class="container text-center"><small><?php echo $content; // phpcs:ignore ?></small></div>
</footer>
<?php wp_footer(); ?>
</main>
</body>
</html>
