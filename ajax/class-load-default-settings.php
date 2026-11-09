<?php
/**
 * Load default settings.
 *
 * @package Custom_Login_Dashboard
 */

namespace CustomLoginDashboard\Ajax;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Class to handle ajax request of loading default settings.
 */
class Load_Default_Settings {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		add_action( 'wp_ajax_cldashboard_load_default_settings', [ $this, 'load_default_settings' ] );

	}

	/**
	 * Load default settings.
	 */
	public function load_default_settings() {

		if ( ! check_ajax_referer( 'cldashboard_nonce_load_default_settings', 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid token', 'erident-custom-login-and-dashboard' ), 401 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You are not allowed to do this', 'erident-custom-login-and-dashboard' ), 403 );
		}

		$default_settings = cldashboard_get_field_default_values();
		update_option( 'plugin_erident_settings', $default_settings );

		wp_send_json_success(
			[
				'message'  => __( 'Settings have been replaced', 'erident-custom-login-and-dashboard' ),
				'settings' => $default_settings,
			]
		);

	}

}
