<?php
/**
 * Plugin Name: Erident Custom Login and Dashboard
 * Plugin URI: https://mapsteps.com
 * Description: Fully customize the WordPress Login Screen.
 * Version: 4.3
 * Requires at least: 5.3
 * Requires PHP: 5.6
 * Author: David Vongries
 * Author URI: https://mapsteps.com
 * License: GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: erident-custom-login-and-dashboard
 *
 * @package Custom_Login_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

// Helper constants.
define( 'CUSTOM_LOGIN_DASHBOARD_PLUGIN_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
define( 'CUSTOM_LOGIN_DASHBOARD_PLUGIN_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );
define( 'CUSTOM_LOGIN_DASHBOARD_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'CUSTOM_LOGIN_DASHBOARD_PLUGIN_VERSION', '4.3' );

// Helper classes.
require __DIR__ . '/helpers/helpers.php';
require __DIR__ . '/helpers/fields.php';
require __DIR__ . '/helpers/class-export.php';
require __DIR__ . '/helpers/class-import.php';

// Ajax handler classes.
require __DIR__ . '/ajax/class-save-settings.php';
require __DIR__ . '/ajax/class-reset-settings.php';
require __DIR__ . '/ajax/class-load-default-settings.php';
require __DIR__ . '/ajax/class-migration.php';

// Required classes.
require __DIR__ . '/class-setup.php';
require __DIR__ . '/class-output.php';

/**
 * Function to run on plugin activation.
 */
function cldashboard_activation_script() {

	$settings = get_option( 'plugin_erident_settings', [] );

	if ( empty( $settings ) ) {
		update_option( 'plugin_erident_settings', cldashboard_get_field_default_values() );
	}

}
register_activation_hook( __FILE__, 'cldashboard_activation_script' );

CustomLoginDashboard\Setup::init();
CustomLoginDashboard\Output::init();
