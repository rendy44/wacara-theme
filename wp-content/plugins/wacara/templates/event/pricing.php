<?php
/**
 * Custom template for displaying pricing section in event landing.
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<?php
if ( ! empty( $price_lists ) ) {
	?>
    <div class="wcr-section-content-wrapper">
        <div class="frow">
			<?php
			foreach ( $price_lists as $list ) {
				?>
                <div class="col-sm-1-2 col-md-1-3">
                    <div class="wcr-pricing-item-wrapper">
                        <div class="wcr-pricing-title-wrapper">
                            <h4 class="wcr-pricing-title"><?php echo esc_html( $list['name'] ); ?></h4>
                        </div>
                        <div class="wcr-pricing-price-wrapper">
                            <span class="wcr-pricing-price-currency"><?php echo esc_html( $list['symbol'] ); ?></span>
                            <span class="wcr-pricing-price-value"><?php echo esc_html( number_format_i18n( (int) $list['price'] ) ); ?></span>
                            <!--                            <span class="wcr-pricing-price-value-comma">00</span>-->
                        </div>
                        <div class="wcr-pricing-features-wrapper">
                            <ul class="wcr-pricing-features">
								<?php
								// Render the price's pros.
								if ( $list['pros'] ) {
									$pros_arr = explode( ',', $list['pros'] );
									foreach ( $pros_arr as $pro ) {
										?>
                                        <li class="wcr-pricing-feature wcr-pricing-feature-pro"><?php echo esc_html( $pro ); ?></li>
										<?php
									}
								}

								// Render the price's cons.
								if ( $list['cons'] ) {
									$cons_arr = explode( ',', $list['cons'] );
									foreach ( $cons_arr as $con ) {
										?>
                                        <li class="wcr-pricing-feature wcr-pricing-feature-con"><?php echo esc_html( $con ); ?></li>
										<?php
									}
								}
								?>
                            </ul>
                        </div>
                        <div class="wcr-pricing-desc-wrapper">
                            <!--                            <p class="wcr-pricing-desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>-->
                        </div>
                        <div class="wcr-pricing-cta-wrapper">
                            <button class="wcr-pricing-cta" data-pricing="<?php echo esc_attr( $list['id'] ); ?>"
                                    data-event="<?php echo esc_attr( $event_id ); ?>"><?php esc_html_e( 'Book Now', 'wacara' ); ?></button>
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
