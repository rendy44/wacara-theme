<?php
/**
 * Template for displaying section title and subtitle.
 *
 * @author Rendy
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wcr-section-heading-wrapper">
	<div class="frow">
		<div class="col-md-2-3 wcr-text-center">
			<div class="wcr-section-title-wrapper">
				<h2 class="wcr-section-title"><?php echo esc_html( $section_title ); ?></h2>
			</div>
			<div class="wcr-section-subtitle-wrapper">
				<h3 class="wcr-section-subtitle"><?php echo esc_html( $section_subtitle ); ?></h3>
			</div>
			<?php
			if ( isset( $section_description ) && $section_description ) {
				?>
				<div class="wcr-section-desc-wrapper">
					<p class="wcr-section-desc"><?php echo esc_html( $section_description ); ?></p>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>
