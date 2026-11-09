<?php
/**
 * Save, reset & load the default settings.
 *
 * @package Custom_Login_Dashboard
 */

namespace CustomLoginDashboard\Ajax;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Ajax handlers of the settings form.
 */
class Settings {

	/**
	 * Register the ajax actions.
	 */
	public function __construct() {

		add_action( 'wp_ajax_cldashboard_save_settings', [ $this, 'save' ] );
		add_action( 'wp_ajax_cldashboard_reset_settings', [ $this, 'reset' ] );
		add_action( 'wp_ajax_cldashboard_load_default_settings', [ $this, 'load_defaults' ] );

	}

	/**
	 * Check the nonce & the user's capability, or stop.
	 *
	 * @param string $nonce_action The nonce action.
	 */
	private function verify( $nonce_action ) {

		if ( ! check_ajax_referer( $nonce_action, 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid token', 'erident-custom-login-and-dashboard' ), 401 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You are not allowed to do this', 'erident-custom-login-and-dashboard' ), 403 );
		}

	}

	/**
	 * Save the settings. Empty fields aren't stored.
	 */
	public function save() {

		$this->verify( 'cldashboard_nonce_save_settings' );

		update_option( 'plugin_erident_settings', cldashboard_sanitize_settings( wp_unslash( $_POST ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Checked in verify().

		wp_send_json_success( __( 'Settings have been saved', 'erident-custom-login-and-dashboard' ) );

	}

	/**
	 * Delete all settings.
	 */
	public function reset() {

		$this->verify( 'cldashboard_nonce_reset_settings' );

		delete_option( 'plugin_erident_settings' );

		wp_send_json_success( __( 'Settings have been reset', 'erident-custom-login-and-dashboard' ) );

	}

	/**
	 * Replace the settings with the default settings.
	 */
	public function load_defaults() {

		$this->verify( 'cldashboard_nonce_load_default_settings' );

		$settings = cldashboard_get_field_default_values();

		update_option( 'plugin_erident_settings', $settings );

		wp_send_json_success(
			[
				'message'  => __( 'Settings have been replaced', 'erident-custom-login-and-dashboard' ),
				'settings' => $settings,
			]
		);

	}

}
