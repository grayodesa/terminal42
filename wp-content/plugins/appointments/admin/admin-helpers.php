<?php

/**
 * @internal
 *
 * @param string $slug
 *
 * @return array
 */
function _appointments_get_admin_notice( $slug ) {

	$gcal_tab_url = add_query_arg(
		array( 'page' => 'app_settings', 'tab' => 'gcal' ),
		admin_url( 'admin.php' )
	);

	$notices = array(
		'1-7-gcal' => sprintf(
			_x( '%s have changed on version 1.7. If you have been using Google Calendar prior to 1.7 please review your settings.', 'Google Calendar Settings admin notice fo 1.7 upgrade.', 'appointments' ),
			'<a href="' . esc_url( $gcal_tab_url ) . '">' . __( 'Google Calendar Settings', 'appointments' ) . '</a>'
		)
	);

	return isset( $notices[ $slug ] ) ? $notices[ $slug ] : false;
}

/**
 * @internal
 * @return array
 */
function _appointments_get_admin_notices() {
	return get_option( 'app_admin_notices', array() );
}

/**
 * @internal
 * @return array
 */
function _appointments_get_user_dismissed_notices( $user_id ) {
	$dismissed = get_user_meta( $user_id, 'app_dismissed_notices', true );
	if ( ! is_array( $dismissed ) ) {
		$dismissed = array();
	}
	return $dismissed;
}

/**
 * @param $name
 * @internal
 * @return bool|string
 */
function _appointments_get_view_path( $name ) {
	$file = appointments_plugin_dir() . 'admin/views/' . $name . '.php';
	$file = apply_filters( 'appointments_admin_view_path', $file );
	if ( is_file( $file ) ) {
		return $file;
	}

	return false;
}

/**
 * @param $tab
 * @internal
 * @return bool|string
 */
function _appointments_get_settings_tab_view_file_path( $tab ) {
	$file = "page-settings-tab-$tab";
	return apply_filters( "appointments_get_settings_tab_view-$tab", _appointments_get_view_path( $file ) );
}

/**
 * @param $tab
 * @param $section
 * @internal
 * @return bool|string
 */
function _appointments_get_settings_section_view_file_path( $tab, $section ) {
	$file = "page-settings-tab-$tab-section-$section";
	return apply_filters( "appointments_get_settings_tab_section_view-$tab", _appointments_get_view_path( $file ) );
}

/**
 * @internal
 * @param string $tab
 * @param string $text Submit button text
 * @param string $class primary|secondary
 */
function _appointments_settings_submit_block( $tab, $text = '', $class = 'primary' ) {
	if (  ! $text ) {
		$text = __( 'Save Changes', 'appointments' );
	}

	?>
		<input type="hidden" name="action_app" value="save_<?php echo $tab; ?>"/>
		<?php wp_nonce_field( 'update_app_settings', 'app_nonce' ); ?>
		<?php submit_button( $text, $class ); ?>
	<?php
}