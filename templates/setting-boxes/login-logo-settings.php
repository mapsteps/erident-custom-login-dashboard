<?php
/**
 * Login logo settings box.
 *
 * @package Custom_Login_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<div class="heatbox">
	<h2><?php esc_html_e( 'Logo', 'erident-custom-login-and-dashboard' ); ?></h2>
	<table class="form-table" role="presentation">
		<?php
		cldashboard_image_field( $settings, 'dashboard_image_logo', __( 'Logo Image', 'erident-custom-login-and-dashboard' ) );
		cldashboard_number_field( $settings, 'dashboard_image_logo_width', __( 'Logo Width', 'erident-custom-login-and-dashboard' ) );
		cldashboard_number_field( $settings, 'dashboard_image_logo_height', __( 'Logo Height', 'erident-custom-login-and-dashboard' ) );
		cldashboard_text_field( $settings, 'dashboard_power_text', __( 'Logo Title', 'erident-custom-login-and-dashboard' ), [ 'description' => __( 'The link text of the logo, read by screen readers.', 'erident-custom-login-and-dashboard' ) ] );
		?>
	</table>
</div>
