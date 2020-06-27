<?php
/**
 * Custom template to display speakers section in landing event.
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wcr-section-content-wrapper">
	<div class="frow">
		<?php
		$maybe_socnet_accounts = array( 'facebook', 'twitter', 'linkedin', 'instagram', 'youtube', 'website' );
		foreach ( $speakers as $speaker ) {
			?>
			<div class="col-sm-1-2 col-md-1-3">
				<div class="wcr-speaker-item-wrapper">
					<div class="wcr-speaker-image-wrapper" style="background-image: url(<?php echo esc_attr( $speaker['image'] ); ?>)"></div>
					<div class="wcr-speaker-detail-wrapper">
						<div class="wcr-speaker-link-wrapper">
							<a href="#" class="wcr-speaker-link"><?php echo esc_html( $speaker['name'] ); ?></a>
						</div>
						<div class="wcr-speaker-desc-wrapper">
							<p class="wcr-speaker-desc"><?php echo esc_html( $speaker['position'] ); ?></p>
						</div>
						<div class="wcr-speaker-socnet-wrapper">
							<ul class="wcr-speaker-socnet">
								<?php
								foreach ( $maybe_socnet_accounts as $account ) {
									if ( ! empty( $speaker[ $account ] ) ) {
										?>
										<li class="wcr-speaker-<?php echo esc_attr( $account ); ?>-wrapper">
											<a target="_blank" href="<?php echo esc_url( $speaker[ $account ] ); ?>" class="wcr-speaker-<?php echo esc_attr( $account ); ?>">
												<span class="wcr-speaker-socnet-label"><?php echo esc_html( ucfirst( $account ) ); ?></span>
											</a>
										</li>
										<?php
									}
								}
								?>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		?>
	</div>
</div>
