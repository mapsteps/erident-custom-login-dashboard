<?php
/**
 * Login form settings box: layout, background, labels, input fields, button & links.
 *
 * @package Custom_Login_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<div class="heatbox is-grouped">

	<h2><?php esc_html_e( 'Login Form', 'erident-custom-login-and-dashboard' ); ?></h2>
	<table class="form-table" role="presentation">
		<?php
		cldashboard_number_field( $settings, 'dashboard_login_width', __( 'Form Width', 'erident-custom-login-and-dashboard' ) );
		cldashboard_number_field( $settings, 'dashboard_login_radius', __( 'Border Radius', 'erident-custom-login-and-dashboard' ) );
		cldashboard_number_field( $settings, 'dashboard_border_thick', __( 'Border Width', 'erident-custom-login-and-dashboard' ) );
		cldashboard_select_field( $settings, 'dashboard_login_border', __( 'Border Style', 'erident-custom-login-and-dashboard' ), [ 'none', 'solid', 'dotted', 'dashed', 'double' ] );
		cldashboard_color_field( $settings, 'dashboard_border_color', __( 'Border Color', 'erident-custom-login-and-dashboard' ) );
		cldashboard_toggle_field( $settings, 'dashboard_check_form_shadow', __( 'Box Shadow', 'erident-custom-login-and-dashboard' ) );
		cldashboard_color_field( $settings, 'dashboard_form_shadow', __( 'Box Shadow Color', 'erident-custom-login-and-dashboard' ), [ 'show_if' => 'dashboard_check_form_shadow' ] );
		?>
	</table>

	<h2><?php esc_html_e( 'Form Background', 'erident-custom-login-and-dashboard' ); ?></h2>
	<table class="form-table" role="presentation">
		<?php
		cldashboard_color_field( $settings, 'dashboard_login_bg', __( 'Background Color', 'erident-custom-login-and-dashboard' ) );
		cldashboard_image_field( $settings, 'login_bg_image', __( 'Background Image', 'erident-custom-login-and-dashboard' ) );
		cldashboard_select_field( $settings, 'login_bg_repeat', __( 'Background Repeat', 'erident-custom-login-and-dashboard' ), [ 'no-repeat', 'repeat', 'repeat-x', 'repeat-y' ] );
		cldashboard_text_field(
			$settings,
			'login_bg_xpos',
			__( 'Horizontal Position', 'erident-custom-login-and-dashboard' ),
			[
				'class'       => 'small-text',
				'description' => cldashboard_position_description( 'x' ),
				'hide_if'     => 'login_bg_repeat',
				'hide_value'  => 'repeat',
			]
		);
		cldashboard_text_field(
			$settings,
			'login_bg_ypos',
			__( 'Vertical Position', 'erident-custom-login-and-dashboard' ),
			[
				'class'       => 'small-text',
				'description' => cldashboard_position_description( 'y' ),
				'hide_if'     => 'login_bg_repeat',
				'hide_value'  => 'repeat',
			]
		);
		?>
	</table>

	<h2><?php esc_html_e( 'Labels', 'erident-custom-login-and-dashboard' ); ?></h2>
	<table class="form-table" role="presentation">
		<?php
		cldashboard_color_field( $settings, 'dashboard_text_color', __( 'Label Color', 'erident-custom-login-and-dashboard' ) );
		cldashboard_number_field( $settings, 'dashboard_label_text_size', __( 'Label Font Size', 'erident-custom-login-and-dashboard' ) );
		?>
	</table>

	<h2><?php esc_html_e( 'Input Fields', 'erident-custom-login-and-dashboard' ); ?></h2>
	<table class="form-table" role="presentation">
		<?php
		cldashboard_color_field( $settings, 'dashboard_input_text_color', __( 'Text Color', 'erident-custom-login-and-dashboard' ) );
		cldashboard_number_field( $settings, 'dashboard_input_text_size', __( 'Font Size', 'erident-custom-login-and-dashboard' ) );
		?>
	</table>

	<h2><?php esc_html_e( 'Button', 'erident-custom-login-and-dashboard' ); ?></h2>
	<table class="form-table" role="presentation">
		<?php
		cldashboard_color_field( $settings, 'dashboard_button_color', __( 'Background Color', 'erident-custom-login-and-dashboard' ) );
		cldashboard_color_field( $settings, 'dashboard_button_text_color', __( 'Text Color', 'erident-custom-login-and-dashboard' ) );
		?>
	</table>

	<h2><?php esc_html_e( 'Links', 'erident-custom-login-and-dashboard' ); ?></h2>
	<table class="form-table" role="presentation">
		<?php
		cldashboard_color_field( $settings, 'dashboard_link_color', __( 'Link Color', 'erident-custom-login-and-dashboard' ) );
		cldashboard_toggle_field( $settings, 'dashboard_check_shadow', __( 'Text Shadow', 'erident-custom-login-and-dashboard' ) );
		cldashboard_color_field( $settings, 'dashboard_link_shadow', __( 'Text Shadow Color', 'erident-custom-login-and-dashboard' ), [ 'show_if' => 'dashboard_check_shadow' ] );
		?>
	</table>

</div>
