<?php
/**
 * Settings page template.
 *
 * Included from Setup::page_output(), $settings holds the plugin settings.
 *
 * @package Custom_Login_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<div class="wrap heatbox-wrap cldashboard-settings-page">

	<div class="heatbox-header heatbox-has-tab-nav heatbox-margin-bottom">

		<div class="heatbox-container heatbox-container-center">

			<div class="logo-container">

				<div>
					<h1 class="title">
						<?php echo esc_html( get_admin_page_title() ); ?>
						<span class="version"><?php echo esc_html( CUSTOM_LOGIN_DASHBOARD_PLUGIN_VERSION ); ?></span>
					</h1>
					<p class="subtitle"><?php esc_html_e( 'Customize the WordPress login screen.', 'erident-custom-login-and-dashboard' ); ?></p>
				</div>

				<div>
					<img src="<?php echo esc_url( CUSTOM_LOGIN_DASHBOARD_PLUGIN_URL ); ?>/assets/images/logo.png" alt="">
				</div>

			</div>

			<nav aria-label="<?php esc_attr_e( 'Settings sections', 'erident-custom-login-and-dashboard' ); ?>">
				<ul class="heatbox-tab-nav">
					<li class="heatbox-tab-nav-item cldashboard-login-screen-panel">
						<a href="#login-screen"><?php esc_html_e( 'Login Screen', 'erident-custom-login-and-dashboard' ); ?></a>
					</li>
					<li class="heatbox-tab-nav-item cldashboard-dashboard-settings-panel">
						<a href="#dashboard-settings"><?php esc_html_e( 'Dashboard', 'erident-custom-login-and-dashboard' ); ?></a>
					</li>
					<li class="heatbox-tab-nav-item cldashboard-tools-panel">
						<a href="#tools"><?php esc_html_e( 'Tools', 'erident-custom-login-and-dashboard' ); ?></a>
					</li>
				</ul>
			</nav>

		</div>

	</div>

	<div class="heatbox-container heatbox-container-center heatbox-column-container heatbox-form-container">

		<div class="heatbox-main">

			<!-- WordPress places admin notices after this marker -->
			<hr class="wp-header-end">

			<?php
			if ( cldashboard_offer_migration() ) {
				$cldashboard_is_notice = false;

				require __DIR__ . '/migration.php';
			}
			?>

			<form method="post" action="options.php" class="cldashboard-settings-form">

				<div class="heatbox-admin-panel cldashboard-login-screen-panel">
					<?php
					require __DIR__ . '/setting-boxes/login-logo-settings.php';
					require __DIR__ . '/setting-boxes/login-bg-settings.php';
					require __DIR__ . '/setting-boxes/login-form-settings.php';
					require __DIR__ . '/setting-boxes/login-footer-link-settings.php';
					?>
				</div>

				<div class="heatbox-admin-panel cldashboard-dashboard-settings-panel">
					<?php
					require __DIR__ . '/setting-boxes/dashboard-settings.php';
					require __DIR__ . '/setting-boxes/misc-settings.php';
					?>
				</div>

				<div class="cldashboard-form-footer">
					<div class="cldashboard-submit-area">
						<button type="submit" class="button button-primary button-larger cldashboard-submit-button">
							<?php esc_html_e( 'Save Changes', 'erident-custom-login-and-dashboard' ); ?>
						</button>
						<span class="cldashboard-notice cldashboard-submit-notice" role="status"></span>
					</div>
					<div class="cldashboard-reset-area">
						<span class="cldashboard-notice cldashboard-reset-notice" role="status"></span>
						<button type="button" class="button button-larger cldashboard-load-defaults-button">
							<?php esc_html_e( 'Load Default Settings', 'erident-custom-login-and-dashboard' ); ?>
						</button>
						<button type="button" class="button button-larger cldashboard-reset-button">
							<?php esc_html_e( 'Reset Settings', 'erident-custom-login-and-dashboard' ); ?>
						</button>
					</div>
				</div>

			</form>

			<div class="heatbox-admin-panel cldashboard-tools-panel">
				<div class="cldashboard-tools-container">
					<?php
					require __DIR__ . '/setting-boxes/export-settings.php';
					require __DIR__ . '/setting-boxes/import-settings.php';
					?>
				</div>
			</div>

		</div>

		<div class="heatbox-sidebar">
			<?php
			require __DIR__ . '/setting-boxes/review.php';
			?>
		</div>

		<div class="heatbox-divider"></div>

	</div>

	<div class="heatbox-container heatbox-container-wide heatbox-container-center featured-products">

		<h2><?php esc_html_e( 'Check out our other free WordPress products!', 'erident-custom-login-and-dashboard' ); ?></h2>

		<ul class="products">
			<li class="heatbox">
				<a href="https://wordpress.org/plugins/ultimate-dashboard/" target="_blank" aria-hidden="true" tabindex="-1">
					<img src="<?php echo esc_url( CUSTOM_LOGIN_DASHBOARD_PLUGIN_URL ); ?>/assets/images/ultimate-dashboard.jpg" alt="">
				</a>
				<div class="heatbox-content">
					<h3><?php esc_html_e( 'Ultimate Dashboard', 'erident-custom-login-and-dashboard' ); ?></h3>
					<p class="subheadline"><?php esc_html_e( 'Fully customize your WordPress dashboard.', 'erident-custom-login-and-dashboard' ); ?></p>
					<p><?php esc_html_e( 'Ultimate Dashboard is the #1 plugin to create a custom WordPress dashboard for you and your clients. It also comes with multisite support which makes it the perfect plugin for your WaaS network.', 'erident-custom-login-and-dashboard' ); ?></p>
					<a href="https://wordpress.org/plugins/ultimate-dashboard/" target="_blank" class="button"><?php esc_html_e( 'Learn more', 'erident-custom-login-and-dashboard' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(opens in a new tab)', 'erident-custom-login-and-dashboard' ); ?></span></a>
				</div>
			</li>
			<li class="heatbox">
				<a href="https://wordpress.org/themes/page-builder-framework/" target="_blank" aria-hidden="true" tabindex="-1">
					<img src="<?php echo esc_url( CUSTOM_LOGIN_DASHBOARD_PLUGIN_URL ); ?>/assets/images/page-builder-framework.jpg" alt="">
				</a>
				<div class="heatbox-content">
					<h3><?php esc_html_e( 'Page Builder Framework', 'erident-custom-login-and-dashboard' ); ?></h3>
					<p class="subheadline"><?php esc_html_e( 'The only theme you\'ll ever need.', 'erident-custom-login-and-dashboard' ); ?></p>
					<p class="description"><?php esc_html_e( 'With its minimalistic design the Page Builder Framework theme is the perfect foundation for your next project. Build blazing fast websites with a theme that is easy to use, lightweight & highly customizable.', 'erident-custom-login-and-dashboard' ); ?></p>
					<a href="https://wordpress.org/themes/page-builder-framework/" target="_blank" class="button"><?php esc_html_e( 'Learn more', 'erident-custom-login-and-dashboard' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(opens in a new tab)', 'erident-custom-login-and-dashboard' ); ?></span></a>
				</div>
			</li>
			<li class="heatbox">
				<a href="https://wordpress.org/plugins/better-admin-bar/" target="_blank" aria-hidden="true" tabindex="-1">
					<img src="<?php echo esc_url( CUSTOM_LOGIN_DASHBOARD_PLUGIN_URL ); ?>/assets/images/better-admin-bar.jpg" alt="">
				</a>
				<div class="heatbox-content">
					<h3><?php esc_html_e( 'Better Admin Bar', 'erident-custom-login-and-dashboard' ); ?></h3>
					<p class="subheadline"><?php esc_html_e( 'Replace the boring WordPress admin bar!', 'erident-custom-login-and-dashboard' ); ?></p>
					<p><?php esc_html_e( 'Better Admin Bar is the plugin that make your clients love WordPress. It drastically improves the user experience when working with WordPress and allows you to replace the boring WordPress admin bar with your own navigation panel.', 'erident-custom-login-and-dashboard' ); ?></p>
					<a href="https://wordpress.org/plugins/better-admin-bar/" target="_blank" class="button"><?php esc_html_e( 'Learn more', 'erident-custom-login-and-dashboard' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(opens in a new tab)', 'erident-custom-login-and-dashboard' ); ?></span></a>
				</div>
			</li>
		</ul>

		<p class="credit"><?php esc_html_e( 'Made with ❤ in Torsby, Sweden', 'erident-custom-login-and-dashboard' ); ?></p>

	</div>

</div>
