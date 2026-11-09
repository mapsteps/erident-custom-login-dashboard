<?php
/**
 * Leave a review box.
 *
 * @package Custom_Login_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<div class="heatbox">
	<h2><?php esc_html_e( 'Leave a Review', 'erident-custom-login-and-dashboard' ); ?></h2>
	<div class="heatbox-content">
		<p class="description">
			<?php esc_html_e( 'Do you enjoy Erident Custom Login and Dashboard? Help us spread the word and leave a review in the official WordPress repository.', 'erident-custom-login-and-dashboard' ); ?>
		</p>
		<a href="https://wordpress.org/support/plugin/erident-custom-login-and-dashboard/reviews/#new-post" target="_blank" class="button">
			<?php esc_html_e( 'Sure, you deserve it!', 'erident-custom-login-and-dashboard' ); ?>
			<span class="screen-reader-text"> <?php esc_html_e( '(opens in a new tab)', 'erident-custom-login-and-dashboard' ); ?></span>
		</a>
	</div>
</div>
