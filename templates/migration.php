<?php
/**
 * Migration to Ultimate Dashboard.
 *
 * Shown as a box on the settings page ($cldashboard_is_notice = false)
 * or as a dismissible notice on the Dashboard & Plugins screens.
 *
 * @package Custom_Login_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

$cldashboard_learn_more_url = 'https://ultimatedashboard.io/blog/erident-custom-login-dashboard-is-now-ultimate-dashboard/?utm_source=erident&utm_medium=' . ( $cldashboard_is_notice ? 'admin-notice' : 'settings-page' ) . '&utm_campaign=udb';
?>

<?php if ( $cldashboard_is_notice ) : ?>
	<div class="notice notice-info is-dismissible cldashboard-migration cldashboard-migration-notice">
		<img class="cldashboard-migration-icon" src="<?php echo esc_url( CUSTOM_LOGIN_DASHBOARD_PLUGIN_URL ); ?>/assets/images/logo.png" alt="">
		<div class="cldashboard-migration-content">
			<p><strong><?php esc_html_e( 'Erident Custom Login Page & Dashboard is now Ultimate Dashboard', 'erident-custom-login-and-dashboard' ); ?></strong></p>
			<p><?php esc_html_e( 'Erident only gets security & compatibility fixes now. New features go into Ultimate Dashboard, which is free too. Move your settings over with one click.', 'erident-custom-login-and-dashboard' ); ?></p>
<?php else : ?>
	<div class="heatbox cldashboard-migration cldashboard-migration-box">
		<h2><?php esc_html_e( 'Erident is now Ultimate Dashboard', 'erident-custom-login-and-dashboard' ); ?></h2>
		<div class="heatbox-content cldashboard-migration-content">
			<p><?php esc_html_e( 'Erident Custom Login Page & Dashboard is in maintenance mode: it gets security & compatibility fixes, but no new features. New login features go into Ultimate Dashboard, which is free too.', 'erident-custom-login-and-dashboard' ); ?></p>
			<img class="cldashboard-migration-image" src="<?php echo esc_url( CUSTOM_LOGIN_DASHBOARD_PLUGIN_URL ); ?>/assets/images/login-customizer.jpg" width="1440" height="715" alt="<?php esc_attr_e( 'The Ultimate Dashboard login customizer: settings on the left, a live preview of the login screen on the right.', 'erident-custom-login-and-dashboard' ); ?>">
			<ul class="cldashboard-migration-benefits">
				<li><?php esc_html_e( 'Customize your login screen live in the WordPress Customizer, with more options.', 'erident-custom-login-and-dashboard' ); ?></li>
				<li><?php esc_html_e( 'Your Erident settings move over automatically.', 'erident-custom-login-and-dashboard' ); ?></li>
				<li><?php esc_html_e( 'Plus tools to customize the whole WordPress admin.', 'erident-custom-login-and-dashboard' ); ?></li>
			</ul>
			<p><?php esc_html_e( 'The one-click migration installs Ultimate Dashboard, moves your settings over and removes this plugin.', 'erident-custom-login-and-dashboard' ); ?></p>
<?php endif; ?>

			<p class="cldashboard-migration-actions">
				<button type="button" class="button button-primary<?php echo $cldashboard_is_notice ? '' : ' button-larger'; ?> cldashboard-migration-button"><?php esc_html_e( 'Migrate to Ultimate Dashboard', 'erident-custom-login-and-dashboard' ); ?></button>
				<a href="<?php echo esc_url( $cldashboard_learn_more_url ); ?>" target="_blank"><?php esc_html_e( 'Learn more', 'erident-custom-login-and-dashboard' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(opens in a new tab)', 'erident-custom-login-and-dashboard' ); ?></span></a>
			</p>

			<ul class="cldashboard-migration-statuses" aria-live="polite">
				<li class="cldashboard-migration-status ultimate-dashboard-installed">
					<span class="loader" aria-hidden="true"></span>
					<span class="dashicons dashicons-yes" aria-hidden="true"></span>
					<span class="process-message"></span>
				</li>
				<li class="cldashboard-migration-status cldashboard-uninstalled">
					<span class="loader" aria-hidden="true"></span>
					<span class="dashicons dashicons-yes" aria-hidden="true"></span>
					<span class="process-message"></span>
				</li>
				<li class="cldashboard-migration-status ultimate-dashboard-activated">
					<span class="loader" aria-hidden="true"></span>
					<span class="dashicons dashicons-yes" aria-hidden="true"></span>
					<span class="process-message"></span>
				</li>
				<li class="cldashboard-migration-status migration-failed">
					<span class="dashicons dashicons-no" aria-hidden="true"></span>
					<span><?php esc_html_e( 'Migration failed:', 'erident-custom-login-and-dashboard' ); ?> <span class="error-message"></span></span>
				</li>
			</ul>
		</div>
	</div>
