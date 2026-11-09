<?php
/**
 * Output login CSS.
 *
 * @package Custom_Login_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Output login CSS.
 *
 * @param array $settings The plugin settings.
 */
return function ( $settings ) {

	// Sanitize on output too, the option may contain values from older versions.
	// After this, toggles are 1 or missing & empty values are gone.
	$settings = cldashboard_sanitize_settings( $settings );

	$get = function ( $key ) use ( $settings ) {
		return empty( $settings[ $key ] ) ? '' : $settings[ $key ];
	};

	$page_bg_color  = $get( 'top_bg_color' );
	$page_bg_image  = $get( 'top_bg_image' );
	$page_bg_repeat = $get( 'top_bg_repeat' );
	$page_bg_pos_x  = $get( 'top_bg_xpos' );
	$page_bg_pos_y  = $get( 'top_bg_ypos' );
	$page_bg_size   = $get( 'top_bg_size' );

	$logo_image  = $get( 'dashboard_image_logo' );
	$logo_width  = $get( 'dashboard_image_logo_width' );
	$logo_height = $get( 'dashboard_image_logo_height' );

	$login_form_box_shadow = $get( 'dashboard_check_form_shadow' ) ? '0 4px 10px -1px ' . ( $get( 'dashboard_form_shadow' ) ? $get( 'dashboard_form_shadow' ) : '#CCCCCC' ) : '';

	$remove_register_link     = $get( 'dashboard_check_lost_pass' );
	$remove_back_to_blog_link = $get( 'dashboard_check_backtoblog' );

	$btn_text_color     = $get( 'dashboard_button_text_color' );
	$btn_bg_color       = $get( 'dashboard_button_color' );
	$btn_bg_color_hover = $btn_bg_color ? cldashboard_color_with_alpha( $btn_bg_color, 0.9 ) : '';

	$form_width         = $get( 'dashboard_login_width' );
	$form_border_radius = $get( 'dashboard_login_radius' );
	$form_border_width  = $get( 'dashboard_border_thick' );
	$form_border_style  = $get( 'dashboard_login_border' );
	$form_border_color  = $get( 'dashboard_border_color' );

	// Older versions had a separate form background opacity.
	$login_form_bg_color = $get( 'dashboard_login_bg' );

	if ( $login_form_bg_color && isset( $settings['dashboard_login_bg_opacity'] ) && 1 > (float) $settings['dashboard_login_bg_opacity'] && false === stripos( $login_form_bg_color, 'rgba' ) ) {
		$login_form_bg_color = cldashboard_color_with_alpha( $login_form_bg_color, $settings['dashboard_login_bg_opacity'] );
	}

	$login_bg_image  = $get( 'login_bg_image' );
	$login_bg_repeat = $get( 'login_bg_repeat' );
	$login_bg_pos_x  = $get( 'login_bg_xpos' );
	$login_bg_pos_y  = $get( 'login_bg_ypos' );

	$label_font_color = $get( 'dashboard_text_color' );
	$label_font_size  = $get( 'dashboard_label_text_size' );

	$input_font_color = $get( 'dashboard_input_text_color' );
	$input_font_size  = $get( 'dashboard_input_text_size' );

	$link_color             = $get( 'dashboard_link_color' );
	$login_link_text_shadow = $get( 'dashboard_check_shadow' ) ? ( $get( 'dashboard_link_shadow' ) ? $get( 'dashboard_link_shadow' ) : '#ffffff' ) . ' 0 1px 0' : '';
	?>

	html {
		background: none !important;
	}

	html body.login {
		<?php if ( ! empty( $page_bg_color ) ) : ?>
			background-color: <?php echo esc_html( $page_bg_color ); ?> !important;
		<?php endif; ?>

		<?php if ( ! empty( $page_bg_image ) ) : ?>
			background-image: url("<?php echo esc_url( $page_bg_image ); ?>") !important;

			<?php if ( ! empty( $page_bg_repeat ) ) : ?>
				background-repeat: <?php echo esc_html( $page_bg_repeat ); ?> !important;
			<?php endif; ?>

			<?php if ( ! empty( $page_bg_pos_x ) && ! empty( $page_bg_pos_y ) ) : ?>
				background-position: <?php echo esc_html( $page_bg_pos_x ); ?> <?php echo esc_html( $page_bg_pos_y ); ?> !important;
			<?php endif; ?>

			<?php if ( ! empty( $page_bg_size ) ) : ?>
				background-size: <?php echo esc_html( $page_bg_size ); ?> !important;
			<?php endif; ?>
		<?php endif; ?>
	}

	body.login div#login h1 a {
		margin: 0 auto;

		<?php if ( ! empty( $logo_image ) ) : ?>
			background-image: url("<?php echo esc_url( $logo_image ); ?>") !important;
		<?php endif; ?>

		<?php if ( ! empty( $logo_width ) || ! empty( $logo_height ) ) : ?>
			<?php if ( ! empty( $logo_width ) && ! empty( $logo_height ) ) : ?>
				background-size: <?php echo esc_html( $logo_width ); ?>px <?php echo esc_html( $logo_height ); ?>px;
			<?php elseif ( ! empty( $logo_width ) && empty( $logo_height ) ) : ?>
				background-size: <?php echo esc_html( $logo_width ); ?>px auto;
			<?php elseif ( empty( $logo_width ) && ! empty( $logo_height ) ) : ?>
				background-size: auto <?php echo esc_html( $logo_height ); ?>px;
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( ! empty( $logo_width ) ) : ?>
			width: <?php echo esc_html( $logo_width ); ?>px;
		<?php endif; ?>

		<?php if ( ! empty( $logo_height ) ) : ?>
			height: <?php echo esc_html( $logo_height ); ?>px;
		<?php endif; ?>
	}

	body.login #login {
		<?php if ( ! empty( $form_width ) ) : ?>
			width: <?php echo esc_html( $form_width ); ?>px;
		<?php endif; ?>
	}

	#loginform {
		<?php if ( ! empty( $form_border_radius ) ) : ?>
			border-radius:<?php echo esc_html( $form_border_radius ); ?>px !important;
		<?php endif; ?>

		<?php if ( ! empty( $form_border_color ) ) : ?>
			border-color: <?php echo esc_html( $form_border_color ); ?> !important;

			<?php if ( ! empty( $form_border_width ) ) : ?>
				border-width: <?php echo esc_html( $form_border_width ); ?>px !important;
			<?php endif; ?>

			<?php if ( ! empty( $form_border_style ) ) : ?>
				border-style: <?php echo esc_html( $form_border_style ); ?> !important;
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( ! empty( $login_form_bg_color ) ) : ?>
			background-color: <?php echo esc_html( $login_form_bg_color ); ?> !important;
		<?php endif; ?>

		<?php if ( ! empty( $login_bg_image ) ) : ?>
			background-image: url("<?php echo esc_url( $login_bg_image ); ?>") !important;

			<?php if ( ! empty( $login_bg_repeat ) ) : ?>
				background-repeat: <?php echo esc_html( $login_bg_repeat ); ?> !important;
			<?php endif; ?>

			<?php if ( ! empty( $login_bg_pos_x ) && ! empty( $login_bg_pos_y ) ) : ?>
				background-position: <?php echo esc_html( $login_bg_pos_x ); ?> <?php echo esc_html( $login_bg_pos_y ); ?> !important;
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( ! empty( $login_form_box_shadow ) ) : ?>
			box-shadow: <?php echo esc_html( $login_form_box_shadow ); ?> !important;
		<?php endif; ?>
	}

	body.login div#login form label,
	p#reg_passmail {
		<?php if ( ! empty( $label_font_color ) ) : ?>
			color: <?php echo esc_html( $label_font_color ); ?> !important;
		<?php endif; ?>

		<?php if ( ! empty( $label_font_size ) ) : ?>
			font-size: <?php echo esc_html( $label_font_size ); ?>px !important;
		<?php endif; ?>
	}

	body.login #loginform p.submit .button-primary,
	body.wp-core-ui .button-primary {
		border: none !important;

		<?php if ( ! empty( $btn_text_color ) ) : ?>
			color: <?php echo esc_html( $btn_text_color ); ?> !important;
		<?php endif; ?>

		<?php if ( ! empty( $btn_bg_color ) ) : ?>
			background: <?php echo esc_html( $btn_bg_color ); ?> !important;
		<?php endif; ?>

		<?php if ( ! empty( $login_link_text_shadow ) ) : ?>
			text-shadow: <?php echo esc_html( $login_link_text_shadow ); ?> !important;
		<?php endif; ?>
	}

	body.login #loginform p.submit .button-primary:hover,
	body.login #loginform p.submit .button-primary:focus,
	body.wp-core-ui .button-primary:hover {
		<?php if ( ! empty( $btn_bg_color_hover ) ) : ?>
			background: <?php echo esc_html( $btn_bg_color_hover ); ?> !important;
		<?php endif; ?>
	}

	body.login div#login form .input,
	.login input[type="text"] {
		<?php if ( ! empty( $input_font_color ) ) : ?>
			color: <?php echo esc_html( $input_font_color ); ?> !important;
		<?php endif; ?>

		<?php if ( ! empty( $input_font_size ) ) : ?>
			font-size: <?php echo esc_html( $input_font_size ); ?>px !important;
		<?php endif; ?>
	}

	body.login #nav a, body.login #backtoblog a {
		<?php if ( ! empty( $link_color ) ) : ?>
			color: <?php echo esc_html( $link_color ); ?> !important;
		<?php endif; ?>
	}

	body.login #nav,
	body.login #backtoblog {
		<?php if ( ! empty( $login_link_text_shadow ) ) : ?>
			text-shadow: <?php echo esc_html( $login_link_text_shadow ); ?> !important;
		<?php endif; ?>
	}

	.login form .input,
	.login input[type=text],
	.wp-core-ui .button-primary:focus {
		box-shadow: none !important;
	}

	body.login #loginform p.submit .button-primary,
	body.wp-core-ui .button-primary {
		box-shadow: none;
	}

	body.login p#nav {
		<?php if ( $remove_register_link ) : ?>
			display: none !important;
		<?php endif; ?>
	}

	body.login #backtoblog {
		<?php if ( $remove_back_to_blog_link ) : ?>
			display: none !important;
		<?php endif; ?>
	}

	<?php
};
