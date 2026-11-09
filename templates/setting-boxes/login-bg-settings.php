<?php
/**
 * Login background settings box.
 *
 * @package Custom_Login_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<div class="heatbox">
	<h2><?php esc_html_e( 'Background', 'erident-custom-login-and-dashboard' ); ?></h2>
	<table class="form-table" role="presentation">
		<?php
		cldashboard_color_field( $settings, 'top_bg_color', __( 'Background Color', 'erident-custom-login-and-dashboard' ) );
		cldashboard_image_field( $settings, 'top_bg_image', __( 'Background Image', 'erident-custom-login-and-dashboard' ) );
		cldashboard_select_field( $settings, 'top_bg_repeat', __( 'Background Repeat', 'erident-custom-login-and-dashboard' ), [ 'no-repeat', 'repeat', 'repeat-x', 'repeat-y' ] );
		cldashboard_text_field(
			$settings,
			'top_bg_xpos',
			__( 'Horizontal Position', 'erident-custom-login-and-dashboard' ),
			[
				'class'       => 'small-text',
				'description' => cldashboard_position_description( 'x' ),
				'hide_if'     => 'top_bg_repeat',
				'hide_value'  => 'repeat',
			]
		);
		cldashboard_text_field(
			$settings,
			'top_bg_ypos',
			__( 'Vertical Position', 'erident-custom-login-and-dashboard' ),
			[
				'class'       => 'small-text',
				'description' => cldashboard_position_description( 'y' ),
				'hide_if'     => 'top_bg_repeat',
				'hide_value'  => 'repeat',
			]
		);
		cldashboard_text_field(
			$settings,
			'top_bg_size',
			__( 'Background Size', 'erident-custom-login-and-dashboard' ),
			[
				'class'       => 'small-text',
				'description' => __( 'Possible values: <code>auto</code>, <code>cover</code>, <code>contain</code> or a value with a unit, e.g. <code>100px</code> or <code>50%</code>.', 'erident-custom-login-and-dashboard' ),
			]
		);
		?>
	</table>
</div>
