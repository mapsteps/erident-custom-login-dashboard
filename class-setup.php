<?php
/**
 * Setup Custom Login Dashboard plugin.
 *
 * @package Custom_Login_Dashboard
 */

namespace CustomLoginDashboard;

/**
 * Setup Better Admin Bar.
 */
class Setup {

	/**
	 * The class instance.
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Get instance of the class.
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Init the class setup.
	 */
	public static function init() {
		self::$instance = new self();

		add_action( 'plugins_loaded', array( self::$instance, 'setup' ) );
	}

	/**
	 * Setup action & filter hooks.
	 */
	public function __construct() {}

	/**
	 * Setup action & filters.
	 */
	public function setup() {

		add_filter( 'plugin_action_links', array( $this, 'add_settings_link' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ), 20 );

		// Process export-import.
		add_action( 'admin_init', array( $this, 'process_export' ) );
		add_action( 'admin_init', array( $this, 'process_import' ) );

		// Migration to Ultimate Dashboard.
		add_action( 'admin_enqueue_scripts', array( $this, 'migration_scripts' ) );
		add_action( 'admin_notices', array( $this, 'migration_notice' ) );

		// Ajax handlers.
		new Ajax\Save_Settings();
		new Ajax\Reset_Settings();
		new Ajax\Load_Default_Settings();
		new Ajax\Migration();

	}

	/**
	 * Add settings link to plugin list page.
	 *
	 * @param array  $actions     An array of plugin action links.
	 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
	 *
	 * @return array The modified plugin action links.
	 */
	public function add_settings_link( $actions, $plugin_file ) {

		if ( CUSTOM_LOGIN_DASHBOARD_PLUGIN_BASENAME === $plugin_file ) {
			$support_link = '<a href="https://wordpress.org/support/plugin/erident-custom-login-and-dashboard/" target="_blank">' . esc_html__( 'Support', 'erident-custom-login-and-dashboard' ) . '</a>';

			array_unshift( $actions, $support_link );

			$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=erident-custom-login-and-dashboard' ) ) . '">' . esc_html__( 'Settings', 'erident-custom-login-and-dashboard' ) . '</a>';

			array_unshift( $actions, $settings_link );

			if ( cldashboard_can_migrate() ) {
				$actions['cldashboard_migrate'] = '<a href="' . esc_url( admin_url( 'options-general.php?page=erident-custom-login-and-dashboard' ) ) . '" style="font-weight: 600;">' . esc_html__( 'Migrate to Ultimate Dashboard', 'erident-custom-login-and-dashboard' ) . '</a>';
			}
		}

		return $actions;

	}

	/**
	 * Add submenu under "Settings" menu item.
	 */
	public function add_submenu_page() {

		add_options_page( __( 'Custom Login & Dashboard', 'erident-custom-login-and-dashboard' ), __( 'Custom Login & Dashboard', 'erident-custom-login-and-dashboard' ), 'manage_options', 'erident-custom-login-and-dashboard', [ $this, 'page_output' ] );

	}

	/**
	 * Better Admin Bar page output.
	 */
	public function page_output() {

		$settings = get_option( 'plugin_erident_settings', [] );
		$settings = is_array( $settings ) ? $settings : [];

		require __DIR__ . '/templates/settings-template.php';

	}

	/**
	 * Enqueue admin styles & scripts.
	 */
	public function admin_scripts() {

		$current_screen = get_current_screen();

		if ( 'settings_page_erident-custom-login-and-dashboard' !== $current_screen->id ) {
			return;
		}

		if ( function_exists( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		} else {
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'thickbox' );
		}

		// CSS dependencies.

		// WP Color picker dependency.
		wp_enqueue_style( 'wp-color-picker' );

		// Settings page styling.
		wp_enqueue_style( 'heatbox', CUSTOM_LOGIN_DASHBOARD_PLUGIN_URL . '/assets/css/heatbox.css', array(), CUSTOM_LOGIN_DASHBOARD_PLUGIN_VERSION );

		// Custom Login Dashboard admin styling.
		wp_enqueue_style( 'custom-login-dashboard', CUSTOM_LOGIN_DASHBOARD_PLUGIN_URL . '/assets/css/admin.css', array(), CUSTOM_LOGIN_DASHBOARD_PLUGIN_VERSION );

		// JS dependencies.

		// Color picker alpha.
		wp_enqueue_script( 'wp-color-picker-alpha', CUSTOM_LOGIN_DASHBOARD_PLUGIN_URL . '/assets/js/wp-color-picker-alpha.js', array( 'jquery', 'wp-color-picker', 'wp-i18n' ), '2.1.3', true );

		// Settings page scripts.
		wp_enqueue_script( 'custom-login-dashboard', CUSTOM_LOGIN_DASHBOARD_PLUGIN_URL . '/assets/js/settings-page.js', array( 'wp-color-picker' ), CUSTOM_LOGIN_DASHBOARD_PLUGIN_VERSION, true );

		wp_localize_script(
			'custom-login-dashboard',
			'CustomLoginDashboard',
			array(
				'nonces'  => array(
					'saveSettings'        => wp_create_nonce( 'cldashboard_nonce_save_settings' ),
					'resetSettings'       => wp_create_nonce( 'cldashboard_nonce_reset_settings' ),
					'loadDefaultSettings' => wp_create_nonce( 'cldashboard_nonce_load_default_settings' ),
				),
				'i18n'    => array(
					'chooseImage' => __( 'Choose Image', 'erident-custom-login-and-dashboard' ),
					'useImage'    => __( 'Use Image', 'erident-custom-login-and-dashboard' ),
				),
				'dialogs' => array(
					'resetSettingsConfirmation'       => __( 'Delete all settings? The login screen will look like the WordPress default again.', 'erident-custom-login-and-dashboard' ),
					'loadDefaultSettingsConfirmation' => __( 'Replace your settings with the default settings?', 'erident-custom-login-and-dashboard' ),
				),
			)
		);

	}

	/**
	 * Modify admin body class.
	 *
	 * @param string $classes The class names.
	 */
	public function admin_body_class( $classes ) {

		$current_user = wp_get_current_user();
		$classes     .= ' custom-login-dashboard-user-' . $current_user->user_nicename;

		$roles = $current_user->roles;
		$roles = $roles ? $roles : array();

		foreach ( $roles as $role ) {
			$classes .= ' custom-login-dashboard-role-' . $role;
		}

		$screens = array(
			'settings_page_erident-custom-login-and-dashboard',
		);

		$screen = get_current_screen();

		if ( ! in_array( $screen->id, $screens, true ) ) {
			return $classes;
		}

		$classes .= ' heatbox-admin has-header';

		return $classes;

	}

	/**
	 * Process widget export.
	 */
	public function process_export() {

		if ( ! isset( $_POST['er_action'] ) || 'export_settings' !== $_POST['er_action'] ) {
			return;
		}

		if ( ! isset( $_POST['er_export_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['er_export_nonce'] ) ), 'er_export_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$exporter = new Helpers\Export();

		$exporter->export();

	}

	/**
	 * Process widget import.
	 */
	public function process_import() {

		if ( ! isset( $_POST['er_action'] ) || 'import_settings' !== $_POST['er_action'] ) {
			return;
		}

		if ( ! isset( $_POST['er_import_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['er_import_nonce'] ) ), 'er_import_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$importer = new Helpers\Import();

		$importer->import();

	}

	/**
	 * Check if the migration notice should show on the current screen.
	 *
	 * Only on the Dashboard & Plugins screens. It comes back 2 months after each dismissal.
	 *
	 * @return bool
	 */
	private function show_migration_notice() {

		$screen = get_current_screen();

		if ( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'plugins' ), true ) ) {
			return false;
		}

		if ( ! cldashboard_can_migrate() || cldashboard_is_udb_installed() ) {
			return false;
		}

		$dismissed = get_user_meta( get_current_user_id(), 'cldashboard_migration_notice_dismissed', true );

		if ( ! $dismissed ) {
			return true;
		}

		// Show it again 2 months after it was dismissed.
		return ! is_numeric( $dismissed ) || time() - (int) $dismissed >= 2 * MONTH_IN_SECONDS;

	}

	/**
	 * Enqueue the migration scripts on the settings page & where the notice shows.
	 */
	public function migration_scripts() {

		$screen = get_current_screen();

		$is_settings_page = $screen && 'settings_page_erident-custom-login-and-dashboard' === $screen->id;

		if ( ! $this->show_migration_notice() && ! ( $is_settings_page && cldashboard_can_migrate() ) ) {
			return;
		}

		wp_enqueue_style( 'cldashboard-migration', CUSTOM_LOGIN_DASHBOARD_PLUGIN_URL . '/assets/css/migration.css', array(), CUSTOM_LOGIN_DASHBOARD_PLUGIN_VERSION );

		wp_enqueue_script( 'cldashboard-migration', CUSTOM_LOGIN_DASHBOARD_PLUGIN_URL . '/assets/js/migration.js', array( 'jquery', 'updates' ), CUSTOM_LOGIN_DASHBOARD_PLUGIN_VERSION, true );

		$new_plugin_basename = 'ultimate-dashboard/ultimate-dashboard.php';

		wp_localize_script(
			'cldashboard-migration',
			'CldashboardMigration',
			array(
				'redirectUrl' => admin_url( 'edit.php?post_type=udb_widgets&page=udb_plugin_onboarding' ),
				'newPlugin'   => array(
					'slug'          => 'ultimate-dashboard',
					'activationUrl' => add_query_arg(
						array(
							'action'   => 'activate',
							'plugin'   => rawurlencode( $new_plugin_basename ),
							'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $new_plugin_basename ),
						),
						admin_url( 'plugins.php' )
					),
				),
				'nonces'      => array(
					'migration' => wp_create_nonce( 'cldashboard_nonce_migration' ),
					'dismiss'   => wp_create_nonce( 'cldashboard_nonce_dismiss_migration_notice' ),
				),
				'i18n'        => array(
					'confirm'      => __( 'Migrate to Ultimate Dashboard now? Please stay on this page until the migration is complete.', 'erident-custom-login-and-dashboard' ),
					'uninstalling' => __( 'Moving your settings & removing Erident Custom Login & Dashboard...', 'erident-custom-login-and-dashboard' ),
					'uninstalled'  => __( 'Settings moved & Erident Custom Login & Dashboard removed.', 'erident-custom-login-and-dashboard' ),
					'installing'   => __( 'Installing Ultimate Dashboard...', 'erident-custom-login-and-dashboard' ),
					'installed'    => __( 'Ultimate Dashboard installed.', 'erident-custom-login-and-dashboard' ),
					'activating'   => __( 'Activating Ultimate Dashboard...', 'erident-custom-login-and-dashboard' ),
					'activated'    => __( 'Ultimate Dashboard activated. Redirecting...', 'erident-custom-login-and-dashboard' ),
					'genericError' => __( 'Something went wrong. Please reload the page and try again.', 'erident-custom-login-and-dashboard' ),
				),
			)
		);

	}

	/**
	 * Notice about the migration to Ultimate Dashboard.
	 */
	public function migration_notice() {

		if ( ! $this->show_migration_notice() ) {
			return;
		}

		$cldashboard_is_notice = true;

		require __DIR__ . '/templates/migration.php';

	}

}
