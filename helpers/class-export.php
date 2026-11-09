<?php
/**
 * Setup export functions.
 *
 * @package Custom_Login_Dashboard
 */

namespace CustomLoginDashboard\Helpers;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Setup settings export functions.
 */
class Export {

	/**
	 * Run exporter.
	 *
	 * The nonce and capability are checked in Setup::process_export().
	 */
	public function export() {

		$settings = get_option( 'plugin_erident_settings', [] );

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=erident-settings-export-' . gmdate( 'm-d-Y' ) . '.json' );
		header( 'Expires: 0' );

		echo wp_json_encode( $settings );
		exit;

	}

}
