<?php
/**
 * Reset settings.
 *
 * @package Custom_Login_Dashboard
 */

namespace CustomLoginDashboard\Ajax;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Class to handle ajax request of settings reset.
 */
class Reset_Settings {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		add_action( 'wp_ajax_cldashboard_reset_settings', [ $this, 'reset' ] );

	}

	/**
	 * Reset settings.
	 */
	public function reset() {

		if ( ! check_ajax_referer( 'cldashboard_nonce_reset_settings', 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid token', 'erident-custom-login-and-dashboard' ), 401 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You are not allowed to do this', 'erident-custom-login-and-dashboard' ), 403 );
		}

		delete_option( 'plugin_erident_settings' );

		wp_send_json_success( __( 'Settings have been reset', 'erident-custom-login-and-dashboard' ) );

	}

}
