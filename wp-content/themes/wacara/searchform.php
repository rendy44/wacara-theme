<?php
/**
 * Template for customizing search form
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="input-group">
		<input type="search" id="search_form" name="s" class="form-control" placeholder="<?php echo esc_attr__( 'Search for...', 'wacara' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>"> <span class="input-group-btn">
			<button class="btn btn-primary" type="submit"><?php echo esc_html__( 'Search', 'wacara' ); ?></button>
		</span>
	</div>
</form>
