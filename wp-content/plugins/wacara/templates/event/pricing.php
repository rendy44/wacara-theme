<?php
/**
 * Custom template for displaying pricing section in event landing.
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! empty( $price_lists ) ) {
	?>
	<div class="wcr-section-content-wrapper">
		<div class="frow">
			<?php
			foreach ( $price_lists as $list ) {
				$maybe_recommended_class = $list['recommended'] ? 'wcr-pricing-item-best-value' : '';
				?>
				<div class="col-sm-1-2 col-md-1-3">
					<div class="wcr-pricing-item-wrapper <?php echo esc_attr( $maybe_recommended_class ); ?>">
						<?php if ( 'on' === $list['recommended'] ) { ?>
							<div class="wcr-pricing-item-ribbon-wrapper">
								<span class="wcr-pricing-item-ribbon"><?php esc_html_e( 'Best Value', 'wacara' ); ?></span>
							</div>
						<?php } ?>
						<div class="wcr-pricing-title-wrapper">
							<h4 class="wcr-pricing-title"><?php echo esc_html( $list['name'] ); ?></h4>
						</div>
						<div class="wcr-pricing-price-wrapper">
							<span class="wcr-pricing-price-currency"><?php echo esc_html( $list['symbol'] ); ?></span>
							<span class="wcr-pricing-price-value"><?php echo esc_html( number_format_i18n( $list['price'] ) ); ?></span>
						</div>
						<div class="wcr-pricing-features-wrapper">
							<ul class="wcr-pricing-features">
								<?php
								// Render the price's pros.
								if ( ! empty( $list['pros'] ) ) {
									foreach ( $list['pros'] as $pro ) {
										?>
										<li class="wcr-pricing-feature wcr-pricing-feature-pro"><?php echo esc_html( $pro ); ?></li>
										<?php
									}
								}

								// Render the price's cons.
								if ( ! empty( $list['cons'] ) ) {
									foreach ( $list['cons'] as $con ) {
										?>
										<li class="wcr-pricing-feature wcr-pricing-feature-con"><?php echo esc_html( $con ); ?></li>
										<?php
									}
								}
								?>
							</ul>
						</div>
						<div class="wcr-pricing-desc-wrapper"></div>
						<div class="wcr-pricing-cta-wrapper">
							<button class="wcr-button wcr-pricing-cta" data-pricing="<?php echo esc_attr( $list['id'] ); ?>" data-event="<?php echo esc_attr( $event_id ); ?>"><?php esc_html_e( 'Book Now', 'wacara' ); ?></button>
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
}
?>
