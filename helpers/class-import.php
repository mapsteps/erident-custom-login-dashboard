<?php
/**
 * Setup import functions.
 *
 * @package Custom_Login_Dashboard
 */

namespace CustomLoginDashboard\Helpers;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Setup settings import functions.
 */
class Import {

	/**
	 * Run importer.
	 *
	 * The nonce and capability are checked in Setup::process_import().
	 */
	public function import() {

		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce is checked by the caller, the file is validated below.
		$file = isset( $_FILES['import_file'] ) && is_array( $_FILES['import_file'] ) ? $_FILES['import_file'] : [];

		if ( empty( $file['tmp_name'] ) || ! empty( $file['error'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
			wp_die( esc_html__( 'Please upload a file to import.', 'erident-custom-login-and-dashboard' ), '', [ 'back_link' => true ] );
		}

		$extension = strtolower( pathinfo( sanitize_file_name( $file['name'] ), PATHINFO_EXTENSION ) );

		if ( 'json' !== $extension ) {
			wp_die( esc_html__( 'Please upload a valid .json file.', 'erident-custom-login-and-dashboard' ), '', [ 'back_link' => true ] );
		}

		$settings = json_decode( file_get_contents( $file['tmp_name'] ), true );

		if ( ! is_array( $settings ) ) {
			wp_die( esc_html__( 'The file does not contain valid settings.', 'erident-custom-login-and-dashboard' ), '', [ 'back_link' => true ] );
		}

		update_option( 'plugin_erident_settings', cldashboard_sanitize_settings( $settings ) );

		wp_safe_redirect( admin_url( 'options-general.php?page=erident-custom-login-and-dashboard&imported=1#tools' ) );
		exit;

	}

}
