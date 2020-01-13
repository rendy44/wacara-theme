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

<div class="row justify-content-center">
    <div class="col-12">
        <ul class="timeline">
			<?php
			$delay = 600;
			foreach ( $schedules as $schedule ) {
				?>
                <li data-aos="fade-left" data-aos-delay="<?php echo esc_attr( $delay ); ?>">
                    <div class="timeline-image shadow">
                        <!-- img.rounded-circle.img-fluid(src='img/about/1.jpg', alt='')-->
                    </div>
                    <div class="timeline-panel">
                        <div class="timeline-heading">
                            <h3><?php echo esc_html( $schedule['period'] ); ?></h3>
                            <h4 class="subheading"><?php echo esc_html( $schedule['title'] ); ?></h4>
                        </div>
                        <div class="timeline-body">
                            <p class="text-muted"><?php echo esc_html( $schedule['content'] ); ?></p>
                        </div>
                    </div>
                </li>
				<?php
				$delay += 200;
			}
			?>
            <li data-aos="fade-left" data-aos-delay="<?php echo esc_attr( $delay + 200 ); ?>>">
                <div class="timeline-image shadow"></div>
            </li>
        </ul>
    </div>
</div>
