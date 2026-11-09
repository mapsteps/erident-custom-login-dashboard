<?php
/** Custom Login Dashboard's helper functions.
 *
 * @package Custom_Login_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Get field data types.
 *
 * Types: html, text, url, color, css, int, float & bool.
 */
function cldashboard_get_field_data_types() {

	return [
		'dashboard_data_left'         => 'html',
		'dashboard_data_right'        => 'html',
		'dashboard_image_logo'        => 'url',
		'dashboard_image_logo_width'  => 'int',
		'dashboard_image_logo_height' => 'int',
		'dashboard_power_text'        => 'text',
		'dashboard_login_width'       => 'int',
		'dashboard_login_radius'      => 'int',
		'dashboard_login_border'      => 'css',
		'dashboard_border_thick'      => 'int',
		'dashboard_border_color'      => 'color',
		'dashboard_login_bg'          => 'color',
		'dashboard_login_bg_opacity'  => 'float', // Deprecated.
		'dashboard_text_color'        => 'color',
		'dashboard_input_text_color'  => 'color',
		'dashboard_label_text_size'   => 'int',
		'dashboard_input_text_size'   => 'int',
		'dashboard_link_color'        => 'color',
		'dashboard_check_shadow'      => 'bool',
		'dashboard_link_shadow'       => 'color',
		'dashboard_check_form_shadow' => 'bool',
		'dashboard_check_lost_pass'   => 'bool',
		'dashboard_check_backtoblog'  => 'bool',
		'dashboard_form_shadow'       => 'color',
		'dashboard_button_color'      => 'color',
		'dashboard_button_text_color' => 'color',
		'top_bg_color'                => 'color',
		'top_bg_image'                => 'url',
		'top_bg_repeat'               => 'css',
		'top_bg_xpos'                 => 'css',
		'top_bg_ypos'                 => 'css',
		'login_bg_image'              => 'url',
		'login_bg_repeat'             => 'css',
		'login_bg_xpos'               => 'css',
		'login_bg_ypos'               => 'css',
		'top_bg_size'                 => 'css',
		'dashboard_delete_db'         => 'bool',
	];

}

/**
 * Sanitize the plugin settings.
 *
 * Used when saving and importing. Unknown fields are dropped, so are empty values.
 *
 * @param array $raw_settings The unsanitized settings (already unslashed).
 *
 * @return array The sanitized settings.
 */
function cldashboard_sanitize_settings( $raw_settings ) {

	$settings = [];

	if ( ! is_array( $raw_settings ) ) {
		return $settings;
	}

	foreach ( cldashboard_get_field_data_types() as $field => $value_type ) {
		if ( ! isset( $raw_settings[ $field ] ) || ! is_scalar( $raw_settings[ $field ] ) ) {
			continue;
		}

		$value = trim( (string) $raw_settings[ $field ] );

		switch ( $value_type ) {
			case 'int':
				$value = absint( $value );
				break;

			case 'float':
				$value = '' === $value ? '' : max( 0, min( 1, (float) $value ) );
				break;

			case 'bool':
				$value = in_array( strtolower( $value ), [ '', '0', 'no', 'false' ], true ) ? '' : 1;
				break;

			case 'html':
				$value = wp_kses_post( $value );
				break;

			case 'url':
				$value = esc_url_raw( $value );
				break;

			case 'color':
				$value = cldashboard_sanitize_color( $value );
				break;

			case 'css':
				$value = cldashboard_sanitize_css_value( $value );
				break;

			default:
				$value = sanitize_text_field( $value );
				break;
		}

		// Keep the database free of empty fields. 0 is a valid value for int & float fields.
		if ( '' !== $value && ( $value || in_array( $value_type, [ 'int', 'float' ], true ) ) ) {
			$settings[ $field ] = $value;
		}
	}

	return $settings;

}

/**
 * Sanitize a color: hex, rgb() or rgba().
 *
 * @param string $color The color.
 *
 * @return string The color, or an empty string if it's not valid.
 */
function cldashboard_sanitize_color( $color ) {

	$color = trim( (string) $color );

	if ( preg_match( '/^#([0-9a-f]{3}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $color ) ) {
		return $color;
	}

	if ( preg_match( '/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(,\s*(0|1|0?\.\d+)\s*)?\)$/i', $color ) ) {
		return $color;
	}

	return '';

}

/**
 * Sanitize a simple CSS value like a keyword or a length (e.g. "center", "50%", "10px 20px").
 *
 * @param string $value The value.
 *
 * @return string The value, or an empty string if it's not valid.
 */
function cldashboard_sanitize_css_value( $value ) {

	$value = trim( (string) $value );

	return preg_match( '/^[a-z0-9.%\s-]+$/i', $value ) ? $value : '';

}

/**
 * Convert a hex, rgb or rgba color to rgba with the given alpha.
 *
 * @param string $color The color.
 * @param float  $alpha The alpha value (0-1).
 *
 * @return string The rgba color, or the original color if it can't be parsed.
 */
function cldashboard_color_with_alpha( $color, $alpha ) {

	$color = trim( (string) $color );
	$alpha = max( 0, min( 1, (float) $alpha ) );

	if ( preg_match( '/^#?([0-9a-f]{3}|[0-9a-f]{6})$/i', $color, $matches ) ) {
		$hex = $matches[1];

		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		$rgb = array_map( 'hexdec', str_split( $hex, 2 ) );
	} elseif ( preg_match( '/^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})/i', $color, $matches ) ) {
		$rgb = array( (int) $matches[1], (int) $matches[2], (int) $matches[3] );
	} else {
		return $color;
	}

	return 'rgba(' . implode( ',', $rgb ) . ',' . $alpha . ')';

}

/**
 * Check if Ultimate Dashboard is installed.
 *
 * @return bool
 */
function cldashboard_is_udb_installed() {

	return defined( 'ULTIMATE_DASHBOARD_PLUGIN_VERSION' ) || file_exists( WP_PLUGIN_DIR . '/ultimate-dashboard/ultimate-dashboard.php' );

}

/**
 * Check if the current user can run the migration to Ultimate Dashboard.
 *
 * The migration installs, activates & deletes plugins.
 *
 * @return bool
 */
function cldashboard_can_migrate() {

	return current_user_can( 'manage_options' ) && current_user_can( 'install_plugins' ) && current_user_can( 'activate_plugins' ) && current_user_can( 'delete_plugins' );

}

/**
 * Get default values of the settings fields.
 */
function cldashboard_get_field_default_values() {

	return [
		'dashboard_data_left'         => 'Thank you for creating with <a href="https://wordpress.org/">WordPress</a>.',
		'dashboard_data_right'        => '&copy; ' . gmdate( 'Y' ) . ' All Rights Reserved',
		'dashboard_image_logo'        => CUSTOM_LOGIN_DASHBOARD_PLUGIN_URL . '/assets/images/default-logo.png',
		'dashboard_image_logo_width'  => 90,
		'dashboard_image_logo_height' => 90,
		'dashboard_power_text'        => 'Powered by Your Website',
		'dashboard_login_width'       => 320,
		'dashboard_login_radius'      => 4,
		'dashboard_login_border'      => 'solid',
		'dashboard_border_thick'      => 2,
		'dashboard_border_color'      => '#dddddd',
		'dashboard_login_bg'          => '#ffffff',
		'dashboard_login_bg_opacity'  => 1, // Deprecated.
		'dashboard_text_color'        => '#3c434a',
		'dashboard_input_text_color'  => '#2c3338',
		'dashboard_label_text_size'   => 14,
		'dashboard_input_text_size'   => 24,
		'dashboard_link_color'        => '#50575e',
		'dashboard_check_shadow'      => 0,
		'dashboard_link_shadow'       => '#ffffff',
		'dashboard_check_form_shadow' => 0,
		'dashboard_check_lost_pass'   => 0,
		'dashboard_check_backtoblog'  => 0,
		'dashboard_form_shadow'       => '#CCCCCC',
		'dashboard_button_color'      => '#2271b1',
		'dashboard_button_text_color' => '#FFFFFF',
		'top_bg_color'                => '#f1f1f1',
		'top_bg_image'                => '',
		'top_bg_repeat'               => 'repeat',
		'top_bg_xpos'                 => 'left',
		'top_bg_ypos'                 => 'top',
		'login_bg_image'              => '',
		'login_bg_repeat'             => 'repeat',
		'login_bg_xpos'               => 'left',
		'login_bg_ypos'               => 'top',
		'top_bg_size'                 => 'auto',
		'dashboard_delete_db'         => 0,
	];

}
