<?php
/**
 * Custom template to render schedule section
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wcr-section-content-wrapper">
    <div class="wcr-schedule-items-wrapper">
		<?php
		foreach ( $schedules as $schedule ) {
			?>
            <div class="wcr-schedule-item-wrapper">
                <div class="wcr-schedule-item-inner-wrapper">
                    <div class="wcr-schedule-item-title-wrapper">
                        <h4 class="wcr-schedule-item-title"><?php echo esc_html( $schedule['period'] ); ?></h4>
                    </div>
                    <div class="wcr-schedule-item-content-wrapper">
                        <p><?php echo esc_html( $schedule['content'] ); ?></p>
                    </div>
                </div>
            </div>
			<?php
		}
		?>
    </div>
</div>
