<?php
/**
 * Migration to Ultimate Dashboard.
 *
 * @package Custom_Login_Dashboard
 */

namespace CustomLoginDashboard\Ajax;

/**
 * Class to manage ajax request of migration to UDB.
 */
class Migration {

	/**
	 * The old plugin slug.
	 *
	 * @var string
	 */
	private $old_plugin_basename = '';

	/**
	 * Class constructor.
	 */
	public function __construct() {

		add_action( 'wp_ajax_cldashboard_migration', [ $this, 'handler' ] );
		add_action( 'wp_ajax_cldashboard_dismiss_migration_notice', [ $this, 'dismiss_notice' ] );

	}

	/**
	 * Dismiss the migration notice for the current user.
	 */
	public function dismiss_notice() {

		if ( ! check_ajax_referer( 'cldashboard_nonce_dismiss_migration_notice', 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid token', 'erident-custom-login-and-dashboard' ), 401 );
		}

		// Store when, so it comes back 2 months later.
		update_user_meta( get_current_user_id(), 'cldashboard_migration_notice_dismissed', time() );

		wp_send_json_success();

	}

	/**
	 * The request handler.
	 */
	public function handler() {

		$this->validate();
		$this->migrate();

	}

	/**
	 * Validate the request & check if the migration can run.
	 */
	private function validate() {

		if ( ! check_ajax_referer( 'cldashboard_nonce_migration', 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid token', 'erident-custom-login-and-dashboard' ), 401 );
		}

		if ( ! cldashboard_can_migrate() ) {
			wp_send_json_error( __( 'You are not allowed to do this', 'erident-custom-login-and-dashboard' ), 403 );
		}

		if ( cldashboard_is_udb_installed() ) {
			wp_send_json_error(
				__( 'Ultimate Dashboard is already installed. To migrate, delete Ultimate Dashboard first, then start the migration again. Make sure the "Remove Data on Uninstall" option in Ultimate Dashboard is NOT checked, so your Ultimate Dashboard settings stay in place.', 'erident-custom-login-and-dashboard' ),
				409
			);
		}

		// Erident gets deleted & Ultimate Dashboard installed, so WordPress must be able to write plugin files without asking for credentials.
		require_once ABSPATH . 'wp-admin/includes/file.php';

		if ( ! WP_Filesystem() ) {
			wp_send_json_error( __( 'WordPress cannot install plugins automatically on this server. Please install Ultimate Dashboard manually.', 'erident-custom-login-and-dashboard' ), 503 );
		}

		// Check that Ultimate Dashboard can be downloaded before anything is removed.
		if ( is_wp_error( wp_remote_head( 'https://api.wordpress.org/', [ 'timeout' => 10 ] ) ) ) {
			wp_send_json_error( __( 'WordPress.org cannot be reached, so Ultimate Dashboard cannot be downloaded. Please check your internet connection. If you use a proxy, try to disable it.', 'erident-custom-login-and-dashboard' ), 503 );
		}

		// Never take this from the request: it's the plugin that gets deleted.
		$this->old_plugin_basename = CUSTOM_LOGIN_DASHBOARD_PLUGIN_BASENAME;

	}

	/**
	 * Migrate to Ultimate Dashboard.
	 */
	private function migrate() {

		if ( ! file_exists( WP_PLUGIN_DIR . '/' . $this->old_plugin_basename ) ) {
			wp_send_json_error( __( 'Erident Custom Login & Dashboard plugin was not found', 'erident-custom-login-and-dashboard' ), 403 );
		}

		$this->migrate_settings();

		update_option( 'udb_migration_from_erident', 1 );

		deactivate_plugins( $this->old_plugin_basename );

		$deletion = delete_plugins( [ $this->old_plugin_basename ] );

		if ( $deletion && ! is_wp_error( $deletion ) ) {
			// If the plugin is deleted successfully, let's delete the option.
			delete_option( 'plugin_erident_settings' );
			wp_send_json_success( __( 'Erident Custom Login & Dashboard has been removed.', 'erident-custom-login-and-dashboard' ) );
		} elseif ( is_wp_error( $deletion ) ) {
			wp_send_json_error( $deletion->get_error_message(), 403 );
		} elseif ( null === $deletion ) {
			wp_send_json_error( __( 'Filesystem credentials are required', 'erident-custom-login-and-dashboard' ), 403 );
		} else {
			wp_send_json_error( __( 'Erident Custom Login & Dashboard plugin is not specified', 'erident-custom-login-and-dashboard' ), 401 );
		}

	}

	/**
	 * Migrate Erident Custom Login & Dashboard settings to Ultimate Dashboard settings.
	 */
	private function migrate_settings() {

		$this->migrate_general_settings();
		$this->migrate_login_settings();

	}

	/**
	 * Migrate general settings.
	 */
	private function migrate_general_settings() {

		$udb_general_options  = get_option( 'udb_settings', [] );
		$udb_branding_options = get_option( 'udb_branding', [] );
		$erident_options      = $this->get_erident_settings();

		if ( empty( $erident_options ) ) {
			return;
		}

		$is_general_changed  = false;
		$is_branding_changed = false;

		$clean_deactivation = isset( $erident_options['dashboard_delete_db'] ) ? $erident_options['dashboard_delete_db'] : 0;
		$clean_deactivation = 'yes' === strtolower( $clean_deactivation ) ? 1 : $clean_deactivation;
		$clean_deactivation = 'no' === strtolower( $clean_deactivation ) ? 0 : $clean_deactivation;

		if ( $clean_deactivation ) {
			$udb_general_options['remove-on-uninstall'] = 1;

			$is_general_changed = true;
		}

		$footer_text = isset( $erident_options['dashboard_data_left'] ) ? stripslashes( $erident_options['dashboard_data_left'] ) : '';

		if ( $footer_text ) {
			$udb_branding_options['footer_text'] = $footer_text;

			$is_branding_changed = true;
		}

		$version_text = isset( $erident_options['dashboard_data_right'] ) ? stripslashes( $erident_options['dashboard_data_right'] ) : '';

		if ( $version_text ) {
			$udb_branding_options['version_text'] = $version_text;

			$is_branding_changed = true;
		}

		if ( $is_general_changed ) {
			update_option( 'udb_settings', $udb_general_options );
		}

		if ( $is_branding_changed ) {
			update_option( 'udb_branding', $udb_branding_options );
		}

	}

	/**
	 * Migrate login customization settings.
	 *
	 * We don't need to use "is_changed" flag here because
	 * almost all Erident settings are about login customization.
	 */
	private function migrate_login_settings() {

		$udb_login_options = get_option( 'udb_login', [] );
		$erident_options   = $this->get_erident_settings();

		$udb_login_options['logo_url'] = get_bloginfo( 'url' );

		if ( empty( $erident_options ) ) {
			update_option( 'udb_login', $udb_login_options );
			return;
		}

		$logo_image_url = isset( $erident_options['dashboard_image_logo'] ) && ! empty( $erident_options['dashboard_image_logo'] ) ? $erident_options['dashboard_image_logo'] : '';

		if ( $logo_image_url ) {
			$udb_login_options['logo_image'] = $logo_image_url;
		}

		$logo_width = isset( $erident_options['dashboard_image_logo_width'] ) && ! empty( $erident_options['dashboard_image_logo_width'] ) ? $erident_options['dashboard_image_logo_width'] : '';

		if ( $logo_width ) {
			$logo_width = trim( $logo_width ) . 'px';

			// Logo width doesn't exist in UDB login customizer, but let's keep this.
			$udb_login_options['logo_width'] = $logo_width;
		}

		$logo_height = isset( $erident_options['dashboard_image_logo_height'] ) && ! empty( $erident_options['dashboard_image_logo_height'] ) ? $erident_options['dashboard_image_logo_height'] : '';

		if ( $logo_height ) {
			$logo_height = trim( $logo_height ) . 'px';

			$udb_login_options['logo_height'] = $logo_height;
		}

		$logo_text = isset( $erident_options['dashboard_power_text'] ) && ! empty( $erident_options['dashboard_power_text'] ) ? $erident_options['dashboard_power_text'] : '';

		if ( $logo_text ) {
			$udb_login_options['logo_title'] = $logo_text;
		}

		$bg_color = isset( $erident_options['top_bg_color'] ) && ! empty( $erident_options['top_bg_color'] ) ? $erident_options['top_bg_color'] : '';

		if ( $bg_color ) {
			$udb_login_options['bg_color'] = $bg_color;
		}

		$bg_image_url = isset( $erident_options['top_bg_image'] ) && ! empty( $erident_options['top_bg_image'] ) ? $erident_options['top_bg_image'] : '';

		if ( $bg_image_url ) {
			$udb_login_options['bg_image'] = $bg_image_url;
		}

		$bg_repeat = isset( $erident_options['top_bg_repeat'] ) && ! empty( $erident_options['top_bg_repeat'] ) ? $erident_options['top_bg_repeat'] : '';

		if ( $bg_repeat ) {
			$udb_login_options['bg_repeat'] = $bg_repeat;
		}

		$bg_position = $this->get_bg_position( $erident_options, 'top_bg_xpos', 'top_bg_ypos' );

		if ( $bg_position ) {
			$udb_login_options = array_merge( $udb_login_options, $this->prefix_keys( $bg_position, 'bg_' ) );
		}

		$bg_size = isset( $erident_options['top_bg_size'] ) ? trim( $erident_options['top_bg_size'] ) : '';

		if ( in_array( $bg_size, [ 'auto', 'cover', 'contain' ], true ) ) {
			$udb_login_options['bg_size'] = $bg_size;
		} elseif ( '' !== $bg_size ) {
			$udb_login_options['bg_size']        = 'custom';
			$udb_login_options['bg_custom_size'] = $bg_size;
		}

		$form_width = isset( $erident_options['dashboard_login_width'] ) && ! empty( $erident_options['dashboard_login_width'] ) ? $erident_options['dashboard_login_width'] : '';

		if ( $form_width ) {
			$form_width = trim( $form_width ) . 'px';

			$udb_login_options['form_width'] = $form_width;
		}

		$form_border_radius = isset( $erident_options['dashboard_login_radius'] ) && ! empty( $erident_options['dashboard_login_radius'] ) ? $erident_options['dashboard_login_radius'] : '';

		if ( $form_border_radius ) {
			$form_border_radius = trim( $form_border_radius ) . 'px';

			$udb_login_options['form_border_radius'] = $form_border_radius;
		}

		$form_border_width = isset( $erident_options['dashboard_border_thick'] ) && ! empty( $erident_options['dashboard_border_thick'] ) ? $erident_options['dashboard_border_thick'] : '';

		if ( $form_border_width ) {
			$form_border_width = trim( $form_border_width ) . 'px';

			$udb_login_options['form_border_width'] = $form_border_width;
		}

		$form_border_style = isset( $erident_options['dashboard_login_border'] ) && ! empty( $erident_options['dashboard_login_border'] ) ? $erident_options['dashboard_login_border'] : '';

		if ( $form_border_style ) {
			$udb_login_options['form_border_style'] = $form_border_style;
		}

		$form_border_color = isset( $erident_options['dashboard_border_color'] ) && ! empty( $erident_options['dashboard_border_color'] ) ? $erident_options['dashboard_border_color'] : '';

		if ( $form_border_color ) {
			$udb_login_options['form_border_color'] = $form_border_color;
		}

		$enable_form_shadow = isset( $erident_options['dashboard_check_form_shadow'] ) ? $erident_options['dashboard_check_form_shadow'] : 0;
		$enable_form_shadow = 'yes' === strtolower( $enable_form_shadow ) ? 1 : $enable_form_shadow;
		$enable_form_shadow = 'no' === strtolower( $enable_form_shadow ) ? 0 : $enable_form_shadow;
		$enable_form_shadow = absint( $enable_form_shadow );

		if ( $enable_form_shadow ) {
			$udb_login_options['enable_form_shadow'] = 1;
		}

		$form_shadow_color = isset( $erident_options['dashboard_form_shadow'] ) && ! empty( $erident_options['dashboard_form_shadow'] ) ? $erident_options['dashboard_form_shadow'] : '';

		if ( $form_shadow_color ) {
			$udb_login_options['form_shadow_color'] = $form_shadow_color;
		}

		$form_bg_color = isset( $erident_options['dashboard_login_bg'] ) && ! empty( $erident_options['dashboard_login_bg'] ) ? $erident_options['dashboard_login_bg'] : '';

		if ( isset( $erident_options['dashboard_login_bg_opacity'] ) ) {
			// This `dashboard_login_bg_opacity` won't be used anymore since we use colorpicker alpha now.
			$form_bg_opacity = '' !== $erident_options['dashboard_login_bg_opacity'] ? $erident_options['dashboard_login_bg_opacity'] : 1; // 0 is allowed here.

			if ( false === stripos( $form_bg_color, 'rgba' ) && 1 > $form_bg_opacity ) {
				$form_bg_color = cldashboard_color_with_alpha( $form_bg_color, $form_bg_opacity );
			}
		}

		if ( $form_bg_color ) {
			$udb_login_options['form_bg_color'] = $form_bg_color;
		}

		$form_bg_image_url = isset( $erident_options['login_bg_image'] ) && ! empty( $erident_options['login_bg_image'] ) ? $erident_options['login_bg_image'] : '';

		if ( $form_bg_image_url ) {
			$udb_login_options['form_bg_image'] = $form_bg_image_url;
		}

		$form_bg_repeat = isset( $erident_options['login_bg_repeat'] ) && ! empty( $erident_options['login_bg_repeat'] ) ? $erident_options['login_bg_repeat'] : '';

		if ( $form_bg_repeat ) {
			$udb_login_options['form_bg_repeat'] = $form_bg_repeat;
		}

		$form_bg_position = $this->get_bg_position( $erident_options, 'login_bg_xpos', 'login_bg_ypos' );

		if ( $form_bg_position ) {
			$udb_login_options = array_merge( $udb_login_options, $this->prefix_keys( $form_bg_position, 'form_bg_' ) );
		}

		$labels_color = isset( $erident_options['dashboard_text_color'] ) && ! empty( $erident_options['dashboard_text_color'] ) ? $erident_options['dashboard_text_color'] : '';

		if ( $labels_color ) {
			$udb_login_options['labels_color'] = $labels_color;
		}

		$labels_font_size = isset( $erident_options['dashboard_label_text_size'] ) && ! empty( $erident_options['dashboard_label_text_size'] ) ? $erident_options['dashboard_label_text_size'] : '';

		if ( $labels_font_size ) {
			$labels_font_size = trim( $labels_font_size ) . 'px';

			$udb_login_options['labels_font_size'] = $labels_font_size;
		}

		$fields_text_color = isset( $erident_options['dashboard_input_text_color'] ) && ! empty( $erident_options['dashboard_input_text_color'] ) ? $erident_options['dashboard_input_text_color'] : '';

		if ( $fields_text_color ) {
			$udb_login_options['fields_text_color']       = $fields_text_color;
			$udb_login_options['fields_text_color_focus'] = $fields_text_color;
		}

		$fields_font_size = isset( $erident_options['dashboard_input_text_size'] ) && ! empty( $erident_options['dashboard_input_text_size'] ) ? $erident_options['dashboard_input_text_size'] : '';

		if ( $fields_font_size ) {
			$fields_font_size = trim( $fields_font_size ) . 'px';

			$udb_login_options['fields_font_size'] = $fields_font_size;
		}

		$button_bg_color       = isset( $erident_options['dashboard_button_color'] ) && ! empty( $erident_options['dashboard_button_color'] ) ? $erident_options['dashboard_button_color'] : '';
		$button_bg_color_hover = '';

		if ( $button_bg_color ) {
			$button_bg_color_hover = cldashboard_color_with_alpha( $button_bg_color, 0.9 );

			$udb_login_options['button_bg_color']       = $button_bg_color;
			$udb_login_options['button_bg_color_hover'] = $button_bg_color_hover;
		}

		$button_text_color = isset( $erident_options['dashboard_button_text_color'] ) && ! empty( $erident_options['dashboard_button_text_color'] ) ? $erident_options['dashboard_button_text_color'] : '';

		if ( $button_text_color ) {
			$udb_login_options['button_text_color']       = $button_text_color;
			$udb_login_options['button_text_color_hover'] = $button_text_color;
		}

		$footer_link_color = isset( $erident_options['dashboard_link_color'] ) && ! empty( $erident_options['dashboard_link_color'] ) ? $erident_options['dashboard_link_color'] : '';

		if ( $footer_link_color ) {
			$udb_login_options['footer_link_color']       = $footer_link_color;
			$udb_login_options['footer_link_color_hover'] = $footer_link_color;
		}

		$enable_link_shadow = isset( $erident_options['dashboard_check_shadow'] ) ? $erident_options['dashboard_check_shadow'] : 0;
		$enable_link_shadow = 'yes' === strtolower( $enable_link_shadow ) ? 1 : $enable_link_shadow;
		$enable_link_shadow = 'no' === strtolower( $enable_link_shadow ) ? 0 : $enable_link_shadow;
		$enable_link_shadow = absint( $enable_link_shadow );

		if ( $enable_link_shadow ) {
			// Enable link shadow doesn't exist in UDB login customizer, but let's keep this.
			$udb_login_options['enable_link_shadow'] = 1;
		}

		$link_shadow_color = isset( $erident_options['dashboard_link_shadow'] ) && ! empty( $erident_options['dashboard_link_shadow'] ) ? $erident_options['dashboard_link_shadow'] : '';

		if ( $link_shadow_color ) {
			// Link shadow color doesn't exist in UDB login customizer, but let's keep this.
			$udb_login_options['link_shadow_color'] = $link_shadow_color;
		}

		$remove_register_link = isset( $erident_options['dashboard_check_lost_pass'] ) ? $erident_options['dashboard_check_lost_pass'] : 0;
		$remove_register_link = 'yes' === strtolower( $remove_register_link ) ? 1 : $remove_register_link;
		$remove_register_link = 'no' === strtolower( $remove_register_link ) ? 0 : $remove_register_link;
		$remove_register_link = absint( $remove_register_link );

		if ( $remove_register_link ) {
			$udb_login_options['remove_register_lost_pw_link'] = 1;
		}

		$remove_back_to_blog_link = isset( $erident_options['dashboard_check_backtoblog'] ) ? $erident_options['dashboard_check_backtoblog'] : 0;
		$remove_back_to_blog_link = 'yes' === strtolower( $remove_back_to_blog_link ) ? 1 : $remove_back_to_blog_link;
		$remove_back_to_blog_link = 'no' === strtolower( $remove_back_to_blog_link ) ? 0 : $remove_back_to_blog_link;
		$remove_back_to_blog_link = absint( $remove_back_to_blog_link );

		if ( $remove_back_to_blog_link ) {
			$udb_login_options['remove_back_to_site_link'] = 1;
		}

		update_option( 'udb_login', $udb_login_options );

	}

	/**
	 * Get the Erident settings, sanitized.
	 *
	 * Older versions saved them unsanitized.
	 *
	 * @return array
	 */
	private function get_erident_settings() {

		$settings = get_option( 'plugin_erident_settings', [] );

		return is_array( $settings ) ? cldashboard_sanitize_settings( $settings ) : [];

	}

	/**
	 * Convert an Erident background position to Ultimate Dashboard's format.
	 *
	 * @param array  $settings The Erident settings.
	 * @param string $x_key    The horizontal position key.
	 * @param string $y_key    The vertical position key.
	 *
	 * @return array The "position" & maybe "custom_position", or empty if not set.
	 */
	private function get_bg_position( $settings, $x_key, $y_key ) {

		$x = isset( $settings[ $x_key ] ) ? trim( $settings[ $x_key ] ) : '';
		$y = isset( $settings[ $y_key ] ) ? trim( $settings[ $y_key ] ) : '';

		if ( '' === $x && '' === $y ) {
			return [];
		}

		// A missing value means "center" in CSS.
		$position = ( '' === $x ? 'center' : $x ) . ' ' . ( '' === $y ? 'center' : $y );

		$udb_positions = [ 'left top', 'left center', 'left bottom', 'center top', 'center center', 'center bottom', 'right top', 'right center', 'right bottom' ];

		if ( in_array( $position, $udb_positions, true ) ) {
			return [ 'position' => $position ];
		}

		return [
			'position'        => 'custom',
			'custom_position' => $position,
		];

	}

	/**
	 * Prefix the keys of an array.
	 *
	 * @param array  $values The values.
	 * @param string $prefix The prefix.
	 *
	 * @return array
	 */
	private function prefix_keys( $values, $prefix ) {

		$prefixed = [];

		foreach ( $values as $key => $value ) {
			$prefixed[ $prefix . $key ] = $value;
		}

		return $prefixed;

	}

}
