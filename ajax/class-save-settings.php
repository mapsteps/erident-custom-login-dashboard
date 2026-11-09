<?php
/**
 * Save settings.
 *
 * @package Custom_Login_Dashboard
 */

namespace CustomLoginDashboard\Ajax;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Class to handle ajax request of settings saving.
 */
class Save_Settings {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		add_action( 'wp_ajax_cldashboard_save_settings', [ $this, 'save' ] );

	}

	/**
	 * Save menu.
	 */
	public function save() {
		if ( ! check_ajax_referer( 'cldashboard_nonce_save_settings', 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid token', 'erident-custom-login-and-dashboard' ), 401 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You are not allowed to do this', 'erident-custom-login-and-dashboard' ), 403 );
		}

		$post_data = cldashboard_sanitize_settings( wp_unslash( $_POST ) );

		/**
		 * Update settings.
		 * If the value of a field is not set, then it will be deleted from the database.
		 * So that the database will not contain empty fields.
		 */
		update_option( 'plugin_erident_settings', $post_data );

		wp_send_json_success( __( 'Settings have been saved', 'erident-custom-login-and-dashboard' ) );
	}

}
