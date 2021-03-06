<?php
/**
 * Custom template to display location section.
 *
 * @author  WPerfekt
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$side_column_size = isset( $location_image ) && $location_image ? 'col-sm-2-3 col-md-1-2' : 'col-md-2-3';
?>

<div class="wcr-section-content-wrapper">
	<div class="frow">
		<?php
		if ( isset( $location_image ) && $location_image ) {
			?>
			<div class="col-sm-2-3 col-md-1-2">
				<div class="wcr-location-image-wrapper">
					<img src="<?php echo esc_attr( $location_image ); ?>" class="wcr-location-image" alt="">
				</div>
			</div>
			<?php
		}
		?>
		<div class="<?php echo esc_attr( $side_column_size ); ?>">
			<div class="wcr-location-content-wrapper">
				<div class="wcr-location-title-wrapper">
					<h4 class="wcr-location-title"><?php echo esc_html( $location_name ); ?></h4>
				</div>
				<div class="wcr-location-description-wrapper">
					<p class="wcr-location-description"><?php echo esc_html( $location_description ); ?></p>
				</div>
				<div class="wcr-location-address-wrapper">
					<p class="wcr-location-address"><?php echo esc_html( $location_address ); ?></p>
				</div>
				<div class="wcr-location-cta-wrapper">
					<a href="<?php echo esc_url( "https://maps.google.com/maps?q={$location_name}&amp;t=&amp;z=15&amp;ie=UTF8&amp;iwloc=&amp" ); ?>" target="_blank" class="wcr-button wcr-button-main wcr-location-cta"><?php esc_html_e( 'Open Map', 'wacara' ); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>

