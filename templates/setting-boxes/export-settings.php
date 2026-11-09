<?php
/**
 * Export settings box.
 *
 * @package Custom_Login_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<div class="heatbox">
	<h2><?php esc_html_e( 'Export', 'erident-custom-login-and-dashboard' ); ?></h2>
	<div class="heatbox-content">
		<form method="post" action="<?php echo esc_url( admin_url( 'options-general.php?page=erident-custom-login-and-dashboard' ) ); ?>#tools">
			<input type="hidden" name="er_action" value="export_settings">
			<?php wp_nonce_field( 'er_export_nonce', 'er_export_nonce' ); ?>
			<p><?php esc_html_e( 'Export a .json file to back up your settings or move them to another WordPress site.', 'erident-custom-login-and-dashboard' ); ?></p>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Export Settings', 'erident-custom-login-and-dashboard' ); ?></button>
		</form>
	</div>
</div>
