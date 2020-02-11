<?php
/**
 * Custom template for displaying modal.
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="modal fade" id="modalTemplate" data-id="">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header">
				<?php
				if ( isset( $content_header ) && $content_header ) {
					echo '<h4>' . $content_header . '</h4>'; // phpcs:ignore
				}
				?>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<?php echo esc_html( $content_body ); ?>
			</div>
			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-light" data-dismiss="modal"><?php esc_html_e( 'Close', 'wacara' ); ?></button>
			</div>
		</div>
	</div>
</div>
