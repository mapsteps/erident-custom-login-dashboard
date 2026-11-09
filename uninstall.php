<?php
/**
 * Runs when the plugin is deleted.
 *
 * @package Custom_Login_Dashboard
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || die( "Can't access directly" );

$cldashboard_settings = get_option( 'plugin_erident_settings', [] );
$cldashboard_remove   = is_array( $cldashboard_settings ) && isset( $cldashboard_settings['dashboard_delete_db'] ) ? strtolower( (string) $cldashboard_settings['dashboard_delete_db'] ) : '';

// Older versions saved "yes" / "no". The plugin isn't loaded here, so no cldashboard_is_on().
if ( ! in_array( $cldashboard_remove, [ '', '0', 'no', 'false' ], true ) ) {
	delete_option( 'plugin_erident_settings' );
}

// When the migration notice was dismissed. Not a setting, so always removed.
delete_metadata( 'user', 0, 'cldashboard_migration_notice_dismissed', '', true );

// Left over if a migration broke off.
delete_option( 'cldashboard_migration_lock' );
