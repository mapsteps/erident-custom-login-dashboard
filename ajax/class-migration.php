<?php
/**
 * Migration to Ultimate Dashboard.
 *
 * @package Custom_Login_Dashboard
 */

namespace CustomLoginDashboard\Ajax;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * The one-click migration to Ultimate Dashboard & its notice.
 */
class Migration {

	/**
	 * The option that makes sure only one migration runs at a time.
	 *
	 * @var string
	 */
	const LOCK = 'cldashboard_migration_lock';

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
	 *
	 * The migration runs in steps, so nothing is removed before Ultimate Dashboard is installed:
	 * 1. "start": checks & takes a lock, then the browser installs Ultimate Dashboard.
	 * 2. "finish": copies the settings to Ultimate Dashboard & removes this plugin, then the browser activates Ultimate Dashboard.
	 * "abort" releases the lock if installing Ultimate Dashboard failed.
	 */
	public function handler() {

		if ( ! check_ajax_referer( 'cldashboard_nonce_migration', 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid token', 'erident-custom-login-and-dashboard' ), 401 );
		}

		if ( ! cldashboard_can_migrate() ) {
			wp_send_json_error( __( 'You are not allowed to do this', 'erident-custom-login-and-dashboard' ), 403 );
		}

		$step = isset( $_POST['step'] ) ? sanitize_key( wp_unslash( $_POST['step'] ) ) : '';

		if ( 'start' === $step ) {
			$this->start();
		} elseif ( 'finish' === $step ) {
			$this->finish();
		} elseif ( 'abort' === $step ) {
			delete_option( self::LOCK );
			wp_send_json_success();
		}

		wp_send_json_error( __( 'Invalid request', 'erident-custom-login-and-dashboard' ), 400 );

	}

	/**
	 * Step 1: check if the migration can run & take the lock.
	 */
	private function start() {

		if ( cldashboard_is_udb_installed() ) {
			wp_send_json_error( __( 'Ultimate Dashboard is already installed, so the migration cannot run. Your Erident settings are unchanged.', 'erident-custom-login-and-dashboard' ), 409 );
		}

		// Erident gets deleted & Ultimate Dashboard installed, so WordPress must be able to write plugin files without asking for credentials.
		require_once ABSPATH . 'wp-admin/includes/file.php';

		if ( ! WP_Filesystem() ) {
			wp_send_json_error( __( 'WordPress cannot install plugins automatically on this server. Please install Ultimate Dashboard manually.', 'erident-custom-login-and-dashboard' ), 503 );
		}

		// Check that Ultimate Dashboard can be downloaded.
		if ( is_wp_error( wp_remote_head( 'https://api.wordpress.org/', [ 'timeout' => 10 ] ) ) ) {
			wp_send_json_error( __( 'WordPress.org cannot be reached, so Ultimate Dashboard cannot be downloaded. Please check your internet connection. If you use a proxy, try to disable it.', 'erident-custom-login-and-dashboard' ), 503 );
		}

		// Only one migration at a time (e.g. in two tabs). A lock older than 15 minutes is left over from a broken run.
		if ( ! $this->take_lock() ) {
			wp_cache_delete( self::LOCK, 'options' );

			if ( time() - (int) get_option( self::LOCK ) < 15 * MINUTE_IN_SECONDS ) {
				wp_send_json_error( __( 'The migration is already running, e.g. in another tab.', 'erident-custom-login-and-dashboard' ), 409 );
			}

			update_option( self::LOCK, time(), false );
		}

		wp_send_json_success();

	}

	/**
	 * Take the migration lock.
	 *
	 * Not add_option(): it checks & then writes, so two requests at the same time would both get it.
	 *
	 * @return bool Whether the lock was taken.
	 */
	private function take_lock() {

		global $wpdb;

		$inserted = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$wpdb->options} (option_name, option_value, autoload) VALUES (%s, %s, 'no')", self::LOCK, time() ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		wp_cache_delete( 'notoptions', 'options' );
		wp_cache_delete( self::LOCK, 'options' );

		return 1 === $inserted;

	}

	/**
	 * Step 2: copy the settings to Ultimate Dashboard & remove this plugin.
	 */
	private function finish() {

		if ( ! get_option( self::LOCK ) ) {
			wp_send_json_error( __( 'The migration was not started. Please reload the page and try again.', 'erident-custom-login-and-dashboard' ), 409 );
		}

		if ( ! cldashboard_is_udb_installed() ) {
			delete_option( self::LOCK );
			wp_send_json_error( __( 'Ultimate Dashboard is not installed. Your Erident settings are unchanged.', 'erident-custom-login-and-dashboard' ), 409 );
		}

		$this->migrate_settings();

		update_option( 'udb_migration_from_erident', 1 );

		// The plugin that gets deleted: never take this from the request.
		$plugin = CUSTOM_LOGIN_DASHBOARD_PLUGIN_BASENAME;

		deactivate_plugins( $plugin );

		$deletion = file_exists( WP_PLUGIN_DIR . '/' . $plugin ) ? delete_plugins( [ $plugin ] ) : true;

		delete_option( self::LOCK );

		if ( true === $deletion ) {
			delete_option( 'plugin_erident_settings' );

			wp_send_json_success(
				[
					'removed' => true,
					'message' => __( 'Settings moved to Ultimate Dashboard & Erident Custom Login & Dashboard removed.', 'erident-custom-login-and-dashboard' ),
				]
			);
		}

		// The settings are in Ultimate Dashboard already, so carry on & let the user delete the plugin by hand.
		wp_send_json_success(
			[
				'removed' => false,
				'message' => __( 'Settings moved to Ultimate Dashboard. Erident Custom Login & Dashboard is deactivated, but could not be deleted. You can delete it on the Plugins screen.', 'erident-custom-login-and-dashboard' ),
			]
		);

	}

	/**
	 * Copy the Erident settings to Ultimate Dashboard's settings.
	 */
	private function migrate_settings() {

		$settings = $this->get_erident_settings();

		$this->migrate_general_settings( $settings );
		$this->migrate_login_settings( $settings );

	}

	/**
	 * Copy "remove data on uninstall" & the admin footer texts.
	 *
	 * @param array $settings The Erident settings (sanitized).
	 */
	private function migrate_general_settings( $settings ) {

		if ( empty( $settings ) ) {
			return;
		}

		if ( ! empty( $settings['dashboard_delete_db'] ) ) {
			$udb_settings = get_option( 'udb_settings', [] );

			$udb_settings['remove-on-uninstall'] = 1;

			update_option( 'udb_settings', $udb_settings );
		}

		$udb_branding = get_option( 'udb_branding', [] );
		$changed      = false;

		foreach ( [
			'dashboard_data_left'  => 'footer_text',
			'dashboard_data_right' => 'version_text',
		] as $key => $udb_key ) {
			$text = empty( $settings[ $key ] ) ? '' : stripslashes( $settings[ $key ] );

			if ( $text ) {
				$udb_branding[ $udb_key ] = $text;
				$changed                  = true;
			}
		}

		if ( $changed ) {
			update_option( 'udb_branding', $udb_branding );
		}

	}

	/**
	 * Copy the login screen settings.
	 *
	 * @param array $settings The Erident settings (sanitized).
	 */
	private function migrate_login_settings( $settings ) {

		$udb_login = get_option( 'udb_login', [] );

		// Erident links the logo to the site.
		$udb_login['logo_url'] = home_url();

		if ( empty( $settings ) ) {
			update_option( 'udb_login', $udb_login );
			return;
		}

		$get = function ( $key ) use ( $settings ) {
			return empty( $settings[ $key ] ) ? '' : $settings[ $key ];
		};

		// Same value.
		$copy = [
			'dashboard_power_text'        => [ 'logo_title' ],
			'top_bg_color'                => [ 'bg_color' ],
			'top_bg_repeat'               => [ 'bg_repeat' ],
			'dashboard_login_border'      => [ 'form_border_style' ],
			'dashboard_border_color'      => [ 'form_border_color' ],
			'dashboard_form_shadow'       => [ 'form_shadow_color' ],
			'login_bg_repeat'             => [ 'form_bg_repeat' ],
			'dashboard_text_color'        => [ 'labels_color' ],
			// Erident has no separate focus & hover colors.
			'dashboard_input_text_color'  => [ 'fields_text_color', 'fields_text_color_focus' ],
			'dashboard_button_text_color' => [ 'button_text_color', 'button_text_color_hover' ],
			'dashboard_link_color'        => [ 'footer_link_color', 'footer_link_color_hover' ],
			// Not in Ultimate Dashboard's login customizer, but kept.
			'dashboard_link_shadow'       => [ 'link_shadow_color' ],
		];

		foreach ( $copy as $key => $udb_keys ) {
			if ( $get( $key ) ) {
				foreach ( $udb_keys as $udb_key ) {
					$udb_login[ $udb_key ] = $get( $key );
				}
			}
		}

		// Numbers in pixels.
		$pixels = [
			'dashboard_image_logo_width'  => 'logo_width', // Not in Ultimate Dashboard's login customizer, but kept.
			'dashboard_image_logo_height' => 'logo_height',
			'dashboard_login_width'       => 'form_width',
			'dashboard_login_radius'      => 'form_border_radius',
			'dashboard_border_thick'      => 'form_border_width',
			'dashboard_label_text_size'   => 'labels_font_size',
			'dashboard_input_text_size'   => 'fields_font_size',
		];

		foreach ( $pixels as $key => $udb_key ) {
			if ( $get( $key ) ) {
				$udb_login[ $udb_key ] = trim( $get( $key ) ) . 'px';
			}
		}

		// Toggles.
		$toggles = [
			'dashboard_check_form_shadow' => 'enable_form_shadow',
			'dashboard_check_shadow'      => 'enable_link_shadow', // Not in Ultimate Dashboard's login customizer, but kept.
			'dashboard_check_lost_pass'   => 'remove_register_lost_pw_link',
			'dashboard_check_backtoblog'  => 'remove_back_to_site_link',
		];

		foreach ( $toggles as $key => $udb_key ) {
			if ( $get( $key ) ) {
				$udb_login[ $udb_key ] = 1;
			}
		}

		// Images: the ones inside this plugin's folder are copied first, the folder gets deleted.
		$images = [
			'dashboard_image_logo' => 'logo_image',
			'top_bg_image'         => 'bg_image',
			'login_bg_image'       => 'form_bg_image',
		];

		foreach ( $images as $key => $udb_key ) {
			$url = $this->keep_image( $get( $key ) );

			if ( $url ) {
				$udb_login[ $udb_key ] = $url;
			}
		}

		// Background positions & size.
		foreach ( [
			'bg_'      => [ 'top_bg_xpos', 'top_bg_ypos' ],
			'form_bg_' => [ 'login_bg_xpos', 'login_bg_ypos' ],
		] as $prefix => $keys ) {
			$position = $this->get_bg_position( $settings, $keys[0], $keys[1] );

			if ( $position ) {
				$udb_login = array_merge( $udb_login, $this->prefix_keys( $position, $prefix ) );
			}
		}

		$bg_size = trim( $get( 'top_bg_size' ) );

		if ( in_array( $bg_size, [ 'auto', 'cover', 'contain' ], true ) ) {
			$udb_login['bg_size'] = $bg_size;
		} elseif ( '' !== $bg_size ) {
			$udb_login['bg_size']        = 'custom';
			$udb_login['bg_custom_size'] = $bg_size;
		}

		// Form background: older versions had a separate opacity.
		$form_bg_color = $get( 'dashboard_login_bg' );

		if ( isset( $settings['dashboard_login_bg_opacity'] ) && 1 > (float) $settings['dashboard_login_bg_opacity'] && false === stripos( $form_bg_color, 'rgba' ) ) {
			$form_bg_color = cldashboard_color_with_alpha( $form_bg_color, $settings['dashboard_login_bg_opacity'] );
		}

		if ( $form_bg_color ) {
			$udb_login['form_bg_color'] = $form_bg_color;
		}

		// Button: Erident darkens it a little on hover.
		if ( $get( 'dashboard_button_color' ) ) {
			$udb_login['button_bg_color']       = $get( 'dashboard_button_color' );
			$udb_login['button_bg_color_hover'] = cldashboard_color_with_alpha( $get( 'dashboard_button_color' ), 0.9 );
		}

		update_option( 'udb_login', $udb_login );

	}

	/**
	 * Copy an image that lives in this plugin's folder (e.g. the default logo) to the uploads folder.
	 *
	 * The plugin folder gets deleted, so its images would be gone.
	 *
	 * @param string $url The image URL.
	 *
	 * @return string The URL to use: unchanged, the copy's URL, or empty if the copy failed.
	 */
	private function keep_image( $url ) {

		$no_scheme  = '#^https?:#i';
		$plugin_url = preg_replace( $no_scheme, '', CUSTOM_LOGIN_DASHBOARD_PLUGIN_URL ) . '/';
		$image_url  = preg_replace( $no_scheme, '', $url );

		if ( 0 !== strpos( $image_url, $plugin_url ) ) {
			return $url;
		}

		$plugin_dir = realpath( CUSTOM_LOGIN_DASHBOARD_PLUGIN_DIR );
		$file       = realpath( CUSTOM_LOGIN_DASHBOARD_PLUGIN_DIR . '/' . strtok( substr( $image_url, strlen( $plugin_url ) ), '?#' ) );
		$file_type  = $file ? wp_check_filetype( $file ) : [];

		if ( ! $plugin_dir || ! $file || 0 !== strpos( $file, $plugin_dir ) || empty( $file_type['type'] ) ) {
			return '';
		}

		$upload = wp_upload_bits( basename( $file ), null, file_get_contents( $file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local file.

		if ( ! empty( $upload['error'] ) ) {
			return '';
		}

		// Add it to the media library too.
		$attachment_id = wp_insert_attachment(
			[
				'post_mime_type' => $upload['type'],
				'post_title'     => sanitize_file_name( pathinfo( $upload['file'], PATHINFO_FILENAME ) ),
				'post_status'    => 'inherit',
			],
			$upload['file']
		);

		if ( $attachment_id && ! is_wp_error( $attachment_id ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
		}

		return $upload['url'];

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
