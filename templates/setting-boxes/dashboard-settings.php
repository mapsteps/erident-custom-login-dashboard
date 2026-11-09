<?php
/**
 * Dashboard settings box.
 *
 * @package Custom_Login_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<div class="heatbox">
	<h2><?php esc_html_e( 'Admin Footer', 'erident-custom-login-and-dashboard' ); ?></h2>
	<table class="form-table" role="presentation">
		<?php
		cldashboard_text_field( $settings, 'dashboard_data_left', __( 'Footer Text', 'erident-custom-login-and-dashboard' ), [ 'description' => __( 'Shown on the left. Links are allowed.', 'erident-custom-login-and-dashboard' ) ] );
		cldashboard_text_field( $settings, 'dashboard_data_right', __( 'Version Text', 'erident-custom-login-and-dashboard' ), [ 'description' => __( 'Shown on the right. Links are allowed.', 'erident-custom-login-and-dashboard' ) ] );
		?>
	</table>
</div>
