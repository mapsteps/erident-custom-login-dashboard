<?php
/**
 * Setting field rows for the settings page.
 *
 * Each function prints a heatbox form-table row (<tr>).
 *
 * @package Custom_Login_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Get a setting value.
 *
 * @param array  $settings The plugin settings.
 * @param string $name     The field name.
 *
 * @return string The value or an empty string.
 */
function cldashboard_field_value( $settings, $name ) {

	return isset( $settings[ $name ] ) && ! is_array( $settings[ $name ] ) ? (string) $settings[ $name ] : '';

}

/**
 * Print the opening of a field row.
 *
 * @param string $name  The field name.
 * @param string $label The field label.
 * @param array  $args  {
 *     Optional arguments.
 *
 *     @type string $description Text under the label. May contain <code>.
 *     @type string $show_if     Only show the row if this checkbox is checked.
 *     @type string $hide_if     Hide the row if the select with this id ...
 *     @type string $hide_value  ... has this value.
 * }
 */
function cldashboard_field_row_start( $name, $label, $args = [] ) {

	$row_attrs = '';

	if ( ! empty( $args['show_if'] ) ) {
		$row_attrs = ' data-show-if-field-checked="' . esc_attr( $args['show_if'] ) . '"';
	} elseif ( ! empty( $args['hide_if'] ) ) {
		$row_attrs = ' data-hide-if-field="' . esc_attr( $args['hide_if'] ) . '" data-hide-if-value="' . esc_attr( $args['hide_value'] ) . '"';
	}
	?>

	<tr<?php echo $row_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?>>
		<th scope="row">
			<label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?></label>
			<?php if ( ! empty( $args['description'] ) ) : ?>
				<p class="description" id="<?php echo esc_attr( $name ); ?>-description"><?php echo wp_kses( $args['description'], [ 'code' => [] ] ); ?></p>
			<?php endif; ?>
		</th>
		<td>

	<?php

}

/**
 * Print the closing of a field row.
 */
function cldashboard_field_row_end() {

	?>

		</td>
	</tr>

	<?php

}

/**
 * Get the aria-describedby attribute if the field has a description.
 *
 * @param string $name The field name.
 * @param array  $args The field args.
 *
 * @return string The attribute (escaped) or an empty string.
 */
function cldashboard_field_describedby( $name, $args ) {

	return empty( $args['description'] ) ? '' : ' aria-describedby="' . esc_attr( $name . '-description' ) . '"';

}

/**
 * Text field.
 *
 * @param array  $settings The plugin settings.
 * @param string $name     The field name.
 * @param string $label    The field label.
 * @param array  $args     See cldashboard_field_row_start().
 */
function cldashboard_text_field( $settings, $name, $label, $args = [] ) {

	cldashboard_field_row_start( $name, $label, $args );
	?>

	<input type="text" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( stripslashes( cldashboard_field_value( $settings, $name ) ) ); ?>" class="<?php echo esc_attr( empty( $args['class'] ) ? 'regular-text' : $args['class'] ); ?> general-setting-field"<?php echo cldashboard_field_describedby( $name, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in the function. ?>>

	<?php
	cldashboard_field_row_end();

}

/**
 * Number field with a "px" unit.
 *
 * @param array  $settings The plugin settings.
 * @param string $name     The field name.
 * @param string $label    The field label.
 * @param array  $args     See cldashboard_field_row_start().
 */
function cldashboard_number_field( $settings, $name, $label, $args = [] ) {

	cldashboard_field_row_start( $name, $label, $args );
	?>

	<input type="number" min="0" step="1" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( cldashboard_field_value( $settings, $name ) ); ?>" class="small-text general-setting-field"<?php echo cldashboard_field_describedby( $name, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in the function. ?>>
	<span class="cldashboard-unit">px</span>

	<?php
	cldashboard_field_row_end();

}

/**
 * Color field.
 *
 * @param array  $settings The plugin settings.
 * @param string $name     The field name.
 * @param string $label    The field label.
 * @param array  $args     See cldashboard_field_row_start().
 */
function cldashboard_color_field( $settings, $name, $label, $args = [] ) {

	$value = cldashboard_field_value( $settings, $name );

	cldashboard_field_row_start( $name, $label, $args );
	?>

	<input type="text" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" class="color-picker-field general-setting-field" data-alpha="true" data-default-color="<?php echo esc_attr( $value ); ?>">

	<?php
	cldashboard_field_row_end();

}

/**
 * Image field: URL input with a media library & a remove button.
 *
 * @param array  $settings The plugin settings.
 * @param string $name     The field name.
 * @param string $label    The field label.
 * @param array  $args     See cldashboard_field_row_start().
 */
function cldashboard_image_field( $settings, $name, $label, $args = [] ) {

	cldashboard_field_row_start( $name, $label, $args );
	?>

	<div class="cldashboard-image-field">
		<input type="url" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_url( cldashboard_field_value( $settings, $name ) ); ?>" class="regular-text general-setting-field cldashboard-media-field">
		<button type="button" class="button cldashboard-upload-button"><?php esc_html_e( 'Choose Image', 'erident-custom-login-and-dashboard' ); ?></button>
		<button type="button" class="button cldashboard-clear-button" aria-label="<?php esc_attr_e( 'Remove image', 'erident-custom-login-and-dashboard' ); ?>">
			<span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
		</button>
	</div>

	<?php
	cldashboard_field_row_end();

}

/**
 * Select field.
 *
 * @param array  $settings The plugin settings.
 * @param string $name     The field name.
 * @param string $label    The field label.
 * @param array  $options  The options (values are shown as labels too).
 * @param array  $args     See cldashboard_field_row_start().
 */
function cldashboard_select_field( $settings, $name, $label, $options, $args = [] ) {

	$value = cldashboard_field_value( $settings, $name );

	cldashboard_field_row_start( $name, $label, $args );
	?>

	<select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" class="general-setting-field">
		<?php foreach ( $options as $option ) : ?>
			<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $option, $value ); ?>><?php echo esc_html( $option ); ?></option>
		<?php endforeach; ?>
	</select>

	<?php
	cldashboard_field_row_end();

}

/**
 * Toggle field (checkbox).
 *
 * @param array  $settings The plugin settings.
 * @param string $name     The field name.
 * @param string $label    The field label.
 * @param array  $args     See cldashboard_field_row_start().
 */
function cldashboard_toggle_field( $settings, $name, $label, $args = [] ) {

	$value = strtolower( cldashboard_field_value( $settings, $name ) );

	cldashboard_field_row_start( $name, $label, $args );
	?>

	<label class="toggle-switch">
		<input type="checkbox" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" value="1" class="general-setting-field" <?php checked( '' !== $value && '0' !== $value && 'no' !== $value ); ?><?php echo cldashboard_field_describedby( $name, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in the function. ?>>
		<span class="switch-track"><span class="switch-thumb"></span></span>
	</label>

	<?php
	cldashboard_field_row_end();

}

/**
 * Get the description of a background position field.
 *
 * @param string $axis Either "x" or "y".
 *
 * @return string The description (may contain <code>).
 */
function cldashboard_position_description( $axis ) {

	if ( 'y' === $axis ) {
		return __( 'Possible values: <code>top</code>, <code>center</code>, <code>bottom</code> or a value with a unit, e.g. <code>20px</code> or <code>50%</code>.', 'erident-custom-login-and-dashboard' );
	}

	return __( 'Possible values: <code>left</code>, <code>center</code>, <code>right</code> or a value with a unit, e.g. <code>20px</code> or <code>50%</code>.', 'erident-custom-login-and-dashboard' );

}
