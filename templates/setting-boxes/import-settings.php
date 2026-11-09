<?php
/**
 * Import settings box.
 *
 * @package Custom_Login_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<div class="heatbox">
	<h2><?php esc_html_e( 'Import', 'erident-custom-login-and-dashboard' ); ?></h2>
	<div class="heatbox-content">
		<?php if ( isset( $_GET['imported'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only shows a notice. ?>
			<div class="notice notice-success inline"><p><?php esc_html_e( 'Settings imported successfully.', 'erident-custom-login-and-dashboard' ); ?></p></div>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'options-general.php?page=erident-custom-login-and-dashboard' ) ); ?>#tools" enctype="multipart/form-data">
			<input type="hidden" name="er_action" value="import_settings">
			<?php wp_nonce_field( 'er_import_nonce', 'er_import_nonce' ); ?>
			<p>
				<label for="cldashboard-import-file"><?php esc_html_e( 'Select the .json file you would like to import.', 'erident-custom-login-and-dashboard' ); ?></label>
			</p>
			<p>
				<input type="file" name="import_file" id="cldashboard-import-file" accept=".json,application/json" required>
			</p>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Import Settings', 'erident-custom-login-and-dashboard' ); ?></button>
		</form>
	</div>
</div>
