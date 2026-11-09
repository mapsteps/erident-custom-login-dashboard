<?php
/**
 * Login screen & admin footer output.
 *
 * @package Custom_Login_Dashboard
 */

namespace CustomLoginDashboard;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Login screen & admin footer output.
 */
class Output {

	/**
	 * Hook in once all plugins are loaded.
	 */
	public static function init() {

		add_action( 'plugins_loaded', [ new self(), 'setup' ] );

	}

	/**
	 * Register the action & filter hooks.
	 */
	public function setup() {

		// Left side of the admin area's footer text.
		add_filter( 'admin_footer_text', [ $this, 'left_footer_text' ] );

		// Right side of the admin area's footer text.
		add_filter( 'update_footer', [ $this, 'right_footer_text' ], 11 );

		add_action( 'login_head', [ $this, 'print_login_styles' ] );

		add_filter( 'login_headerurl', [ $this, 'login_logo_url' ] );
		add_filter( 'login_headertext', [ $this, 'login_logo_title' ] );

	}

	/**
	 * Filters the “Thank you” text displayed in the admin footer.
	 *
	 * @param string $text The existing footer text.
	 * @return string The modified footer text.
	 */
	public function left_footer_text( $text ) {

		$settings = get_option( 'plugin_erident_settings', [] );
		if ( empty( $settings['dashboard_data_left'] ) ) {
			return $text;
		}

		return wp_kses_post( stripslashes( $settings['dashboard_data_left'] ) );

	}

	/**
	 * Filters the version/update text displayed in the admin footer.
	 *
	 * @param string $content The content that will be printed.
	 */
	public function right_footer_text( $content ) {

		$settings = get_option( 'plugin_erident_settings', [] );
		if ( empty( $settings['dashboard_data_right'] ) ) {
			return $content;
		}

		return wp_kses_post( stripslashes( $settings['dashboard_data_right'] ) );

	}

	/**
	 * Print login styles.
	 */
	public function print_login_styles() {

		$settings  = get_option( 'plugin_erident_settings', [] );
		$print_css = require __DIR__ . '/inc/login.css.php';

		echo '<style>';
		$print_css( $settings ); // Values are sanitized & escaped in the template.
		echo '</style>';

	}

	/**
	 * Link the login logo to the site instead of wordpress.org.
	 *
	 * @return string
	 */
	public function login_logo_url() {

		return home_url();

	}

	/**
	 * Change login logo title.
	 *
	 * @param string $headertext The existing header text.
	 * @return string
	 */
	public function login_logo_title( $headertext ) {

		$settings = get_option( 'plugin_erident_settings' );
		if ( empty( $settings['dashboard_power_text'] ) ) {
			return $headertext;
		}

		// Core prints this unescaped.
		return esc_html( $settings['dashboard_power_text'] );

	}

}
