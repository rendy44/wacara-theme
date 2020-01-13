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

<div class="row justify-content-center">
	<?php
	if ( ! empty( $price_lists ) ) {
		$delay = 600;
		foreach ( $price_lists as $list ) {
			?>
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 mb-md-0 shadow" data-aos="fade-up"
                     data-aos-delay="<?php echo esc_attr( $delay ); ?>">
                    <div class="card-body">
                        <h5 class="card-title text-muted text-uppercase text-center"><?php echo esc_html( $list['name'] ); ?></h5>
                        <h6 class="card-price text-center"><?php echo esc_html( $list['symbol'] ) . esc_html( number_format_i18n( (int) $list['price'] ) ); ?>
                            <span class="period"></span>
                        </h6>
                        <hr>
                        <ul class="fa-ul">
							<?php
							// Render the price's pros.
							if ( $list['pros'] ) {
								$pros_arr = explode( ',', $list['pros'] );
								foreach ( $pros_arr as $pro ) {
									echo '<li><span class="fa-li"><i class="fas fa-check"></i></span>' . $pro . '</li>'; // phpcs:ignore
								}
							}

							// Render the price's cons.
							if ( $list['cons'] ) {
								$cons_arr = explode( ',', $list['cons'] );
								foreach ( $cons_arr as $con ) {
									echo '<li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>' . $con . '</li>'; // phpcs:ignore
								}
							}
							?>
                        </ul>
                        <button class="btn btn-primary btn-lg btn-do-register"
                                data-pricing="<?php echo esc_attr( $list['id'] ); ?>"
                                data-event="<?php echo esc_attr( $event_id ); ?>"><?php esc_html_e( 'Book Now', 'wacara' ); ?></button>
                    </div>
                </div>
            </div>
			<?php
			$delay += 200;
		}
	}
	?>
</div>
