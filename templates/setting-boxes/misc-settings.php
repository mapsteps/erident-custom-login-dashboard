<?php
/**
 * Misc settings box.
 *
 * @package Custom_Login_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<div class="heatbox">
	<h2><?php esc_html_e( 'Misc', 'erident-custom-login-and-dashboard' ); ?></h2>
	<table class="form-table" role="presentation">
		<?php
		cldashboard_toggle_field( $settings, 'dashboard_delete_db', __( 'Remove Data on Uninstall', 'erident-custom-login-and-dashboard' ), [ 'description' => __( 'Delete all plugin settings when the plugin is deleted.', 'erident-custom-login-and-dashboard' ) ] );
		?>
	</table>
</div>
