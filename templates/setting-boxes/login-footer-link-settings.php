<?php
/**
 * Login footer link settings box.
 *
 * @package Custom_Login_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<div class="heatbox">
	<h2><?php esc_html_e( 'Footer Links', 'erident-custom-login-and-dashboard' ); ?></h2>
	<table class="form-table" role="presentation">
		<?php
		cldashboard_toggle_field( $settings, 'dashboard_check_lost_pass', __( 'Remove "Register | Lost your password?" Links', 'erident-custom-login-and-dashboard' ) );
		cldashboard_toggle_field( $settings, 'dashboard_check_backtoblog', __( 'Remove "Go to Site" Link', 'erident-custom-login-and-dashboard' ) );
		?>
	</table>
</div>
