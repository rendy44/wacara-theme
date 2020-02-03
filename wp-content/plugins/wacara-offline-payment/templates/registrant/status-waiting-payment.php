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

<div class="wcr-command-wrapper">
    <p class="wcr-command"><?php esc_html_e( 'In order to save your seat, please make a payment as detailed below:', 'wacara' ); ?></p>
</div>
<div class="wcr-amount-wrapper wcr-text-center">
    <h1 class="wcr-amount"><?php echo sprintf( '<span class="wcr-currency">%s</span><span class="wcr-value">%s</span>', $currency_symbol, $amount ); // phpcs:ignore ?></h1>
</div>
<div class="wcr-alert wcr-alert-warning">
    <strong><?php esc_html_e( 'Important!', 'wacara' ); ?></strong>
	<?php esc_html_e( 'The transfer amount must be exactly same as above, including the coma if any', 'wacara' ); ?>
</div>
<div class="wcr-command-wrapper">
    <p class="wcr-command"><?php esc_html_e( 'Select one of the following bank accounts you want to transfer to', 'wacara' ); ?></p>
</div>
<div class="wcr-bank-accounts-wrapper">
    <div class="frow">
		<?php
		if ( ! empty( $bank_accounts ) ) {
			$row_num = 0;
			foreach ( $bank_accounts as $account ) {
				?>
                <div class="col-sm-1-3">
                    <div class="wcr-bank-account">
                        <input type="radio" name="selected_bank" id="bank_<?php echo esc_attr( $row_num ); ?>" value="<?php echo esc_attr( $row_num ); ?>">
                        <label for="bank_<?php echo esc_attr( $row_num ); ?>">
							<?php /* translators: %1: bank name &2: branch name */ ?>
                            <p class="wcr-bank-account-name"><?php echo esc_html( sprintf( _x( '%1$s, %2$s', 'Displaying bank information', 'wacara' ), $account['name'], $account['branch'] ) ); ?></p>
                            <p class="wcr-bank-account-number"><?php echo esc_html( $account['number'] ); ?></p>
                            <p class="wcr-bank-account-holder"><?php echo esc_html( $account['holder'] ); ?></p>
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
<div class="wcr-command-wrapper">
    <p class="wcr-command"><?php esc_html_e( 'Once you made a transfer, please click button below to confirm', 'wacara' ); ?></p>
</div>
