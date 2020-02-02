<?php
/**
 * Template for displaying waiting payment page in single registration.
 *
 * @author  Rendy
 * @package Wacara
 * @version 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<p><?php esc_html_e( 'In order to save your seat, please make a payment as detailed below:', 'wacara' ); ?></p>
<h1 class="amount"><?php echo esc_html( sprintf( '%s%s', $currency_code, $amount ) ); ?></h1>
<div class="wcr-alert wcr-alert-warning">
	<strong><?php esc_html_e( 'Important!', 'wacara' ); ?></strong>
	<?php esc_html_e( 'The transfer amount must be exactly same as above, including the coma if any', 'wacara' ); ?>
</div>
<p><?php esc_html_e( 'Select one of the following bank accounts you want to transfer to', 'wacara' ); ?></p>
<div class="wcr-bank-accounts-wrapper">
	<div class="frow">
		<?php
		if ( ! empty( $bank_accounts ) ) {
			$row_num = 0;
			foreach ( $bank_accounts as $account ) {
				?>
				<div class="col-sm-1-2">
					<div class="wcr-bank-account">
						<input type="radio" name="selected_bank" id="bank_<?php echo esc_attr( $row_num ); ?>" value="<?php echo esc_attr( $row_num ); ?>">
						<label for="bank_<?php echo esc_attr( $row_num ); ?>">
							<i class="text-primary fa fa-check-circle fa-2x"></i>
							<?php /* translators: %1: bank name &2: branch name */ ?>
							<p class="name"><?php echo esc_html( sprintf( _x( '%1$s, %2$s', 'Dislaying bank information', 'wacara' ), $account['name'], $account['branch'] ) ); ?></p>
							<p class="number"><?php echo esc_html( $account['number'] ); ?></p>
							<p class="holder"><?php echo esc_html( $account['holder'] ); ?></p>
						</label>
					</div>
				</div>
				<?php
				$row_num ++;
			}
		}
		?>
	</div>
</div>
<p><?php esc_html_e( 'Once you made a transfer, please click button below to confirm', 'wacara' ); ?></p>
